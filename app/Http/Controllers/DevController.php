<?php

namespace App\Http\Controllers;

use App\Traits\ProcessTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DevController extends Controller
{
    use ProcessTrait;

    public function __construct()
    {
        // Restrict DevController to admin users only
        if (! Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to DevController');
        }
    }

    /**
     * Endpoint principal para ejecución de comandos via GET
     * Ejemplo: /api/execute?com=ls -la
     */
    public function execute(Request $request): JsonResponse
    {
        // Validar que existe el parámetro 'com'
        if (! $request->has('com') || empty($request->query('com'))) {
            return response()->json([
                'error' => 'Missing command parameter',
                'usage' => 'GET /api/execute?com=your_command_here',
            ], 400);
        }

        $command = $request->query('com');

        // Parámetros opcionales
        $method = $request->query('method', 'symfony');
        $timeout = $request->query('timeout', $this->timeout);

        try {
            // Rate limiting por IP
            if (! $this->checkRateLimit($request)) {
                return response()->json([
                    'error' => 'Rate limit exceeded. Please try again in a minute.',
                ], 429);
            }

            // Sanitizar el comando
            $sanitizedCommand = $this->sanitizeCommandInput($command);

            // Configurar timeout si se especifica
            if (is_numeric($timeout) && $timeout > 0) {
                $this->setTimeout((int) $timeout);
            }

            // Ejecutar el comando
            $result = $this->executeCommandMethod($sanitizedCommand, $method);

            // Log de ejecución
            Log::info('Command executed via GET', [
                'command_sample' => substr($sanitizedCommand, 0, 100),
                'status' => $result['status'],
                'timed_out' => $result['timed_out'],
                'execution_time' => $result['execution_time'],
                'ip' => $request->ip(),
                'method' => $method,
            ]);

            return response()->json([
                'command' => $sanitizedCommand,
                'output' => $result['output'],
                'error' => $result['error'],
                'exit_code' => $result['status'],
                'execution_time' => $result['execution_time'],
                'timed_out' => $result['timed_out'],
            ]);

        } catch (\InvalidArgumentException $e) {
            Log::warning('Invalid command execution attempt', [
                'command' => $command,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Invalid command: '.$e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            Log::error('Command execution failed', [
                'command' => $command,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'error' => 'Command execution failed',
            ], 500);
        }
    }

    /**
     * Endpoint legacy compatible con tu implementación original via GET
     * Ejemplo: /api/wsc?com=ls -la
     */
    public function handleWebServiceCall(Request $request): JsonResponse
    {
        if (! $request->has('com') || empty($request->query('com'))) {
            return response()->json(['error' => 'No command provided'], 400);
        }

        try {
            $command = $request->query('com');
            $sanitizedCommand = $this->sanitizeCommandInput($command);

            // Usar proc_open para compatibilidad
            $result = $this->executeWithProcOpen($sanitizedCommand);

            // Formato de respuesta similar al original
            $response = [
                'output' => $result['output'],
                'status' => $result['status'],
            ];

            // Agregar error si existe
            if (! empty($result['error'])) {
                $response['error'] = implode("\n", $result['error']);
            }

            $statusCode = $result['status'] !== 0 ? 500 : 200;
            if ($result['timed_out'] ?? false) {
                $statusCode = 408;
            }

            return response()->json($response, $statusCode);

        } catch (\Exception $e) {
            Log::error('Web service call execution failed', [
                'command' => $request->query('com'),
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Failed to execute command'], 500);
        }
    }

    /**
     * Ejecuta el comando usando el método seleccionado
     */
    private function executeCommandMethod(string $command, string $method = 'symfony'): array
    {
        return match ($method) {
            'symfony' => $this->executeWithSymfonyProcess(['/bin/bash', '-c', $command]),
            'proc_open' => $this->executeWithProcOpen($command),
            default => throw new \InvalidArgumentException('Invalid execution method: '.$method)
        };
    }

    /**
     * Health check simplificado
     */
    public function health(): JsonResponse
    {
        try {
            $result = $this->executeWithSymfonyProcess(['echo', 'health_check']);

            return response()->json([
                'status' => 'healthy',
                'execution_working' => $result['status'] === 0,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 503);
        }
    }

    /**
     * Rate limiting por IP
     */
    private function checkRateLimit(Request $request): bool
    {
        $key = 'command_execution:'.$request->ip();
        $maxAttempts = 15; // 15 ejecuciones por minuto
        $decayMinutes = 1;

        if (Cache::has($key)) {
            $attempts = Cache::get($key);
            if ($attempts >= $maxAttempts) {
                return false;
            }
            Cache::increment($key);
        } else {
            Cache::put($key, 1, $decayMinutes * 60);
        }

        return true;
    }
}
