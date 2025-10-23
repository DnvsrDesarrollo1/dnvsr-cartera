<?php

namespace App\Traits;

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

trait ProcessTrait
{
    protected int $timeout = 25;
    protected int $maxOutputLength = 100000; // 100KB
    protected float $streamSelectTimeout = 0.2; // 200ms

    /**
     * Ejecuta un comando usando Symfony Process
     */
    protected function executeWithSymfonyProcess(array $command, ?string $input = null): array
    {
        $process = new Process(
            $command,
            null,
            $this->getSafeEnvironmentVariables(),
            $input,
            $this->timeout
        );

        try {
            $process->run();

            if ($process->isTerminated() && $process->getExitCode() === null) {
                return [
                    'output' => [],
                    'error' => ['Process terminated due to timeout'],
                    'status' => 124,
                    'timed_out' => true,
                    'execution_time' => 0
                ];
            }

            return [
                'output' => $this->sanitizeOutput($process->getOutput()),
                'error' => $this->sanitizeOutput($process->getErrorOutput()),
                'status' => $process->getExitCode(),
                'timed_out' => false,
                'execution_time' => round($process->getLastOutputTime() ?? 0, 2)
            ];

        } catch (ProcessTimedOutException $e) {
            return [
                'output' => [],
                'error' => ['Command execution timed out'],
                'status' => 124,
                'timed_out' => true,
                'execution_time' => $this->timeout
            ];
        } catch (\Exception $e) {
            return [
                'output' => [],
                'error' => ['Process execution failed: ' . $e->getMessage()],
                'status' => -1,
                'timed_out' => false,
                'execution_time' => 0
            ];
        }
    }

    /**
     * Ejecuta un comando usando proc_open
     */
    protected function executeWithProcOpen(string $cmd, ?string $input = null): array
    {
        $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        $process = proc_open(
            $cmd,
            $descriptorspec,
            $pipes,
            null,
            $this->getSafeEnvironmentVariables()
        );

        if (!is_resource($process)) {
            return [
                'output' => [],
                'error' => ['Failed to execute command'],
                'status' => -1,
                'timed_out' => false,
                'execution_time' => 0
            ];
        }

        if ($input !== null) {
            fwrite($pipes[0], $input);
        }
        fclose($pipes[0]);

        return $this->monitorProcOpenProcess($process, $pipes);
    }

    /**
     * Monitorea el proceso de proc_open
     */
    private function monitorProcOpenProcess($process, array $pipes): array
    {
        $output = '';
        $errorOutput = '';
        $startTime = microtime(true);

        list($stdout, $stderr) = [$pipes[1], $pipes[2]];

        stream_set_blocking($stdout, false);
        stream_set_blocking($stderr, false);

        while (true) {
            $read = [$stdout, $stderr];
            $write = $except = null;

            $changed = stream_select($read, $write, $except, 0, (int)($this->streamSelectTimeout * 1000000));

            if ($changed > 0) {
                foreach ($read as $stream) {
                    $chunk = stream_get_contents($stream);
                    if ($chunk !== false) {
                        if ($stream === $stdout) {
                            $output .= $chunk;
                        } else {
                            $errorOutput .= $chunk;
                        }
                    }
                }
            }

            if (strlen($output) > $this->maxOutputLength || strlen($errorOutput) > $this->maxOutputLength) {
                proc_terminate($process);
                $output = substr($output, 0, $this->maxOutputLength) . "\n[OUTPUT TRUNCATED]";
                $errorOutput = substr($errorOutput, 0, $this->maxOutputLength) . "\n[ERROR OUTPUT TRUNCATED]";
                break;
            }

            $status = proc_get_status($process);
            if (!$status['running']) {
                $output .= stream_get_contents($stdout);
                $errorOutput .= stream_get_contents($stderr);
                break;
            }

            if (microtime(true) - $startTime > $this->timeout) {
                proc_terminate($process);
                $executionTime = round(microtime(true) - $startTime, 2);

                $output .= stream_get_contents($stdout);
                $errorOutput .= stream_get_contents($stderr);

                fclose($stdout);
                fclose($stderr);
                proc_close($process);

                return [
                    'output' => $this->sanitizeOutput($output),
                    'error' => array_merge(
                        $this->sanitizeOutput($errorOutput),
                        ['Command execution timed out after ' . $executionTime . ' seconds']
                    ),
                    'status' => 124,
                    'timed_out' => true,
                    'execution_time' => $executionTime
                ];
            }

            usleep(50000);
        }

        fclose($stdout);
        fclose($stderr);
        $returnCode = proc_close($process);
        $executionTime = round(microtime(true) - $startTime, 2);

        return [
            'output' => $this->sanitizeOutput($output),
            'error' => $this->sanitizeOutput($errorOutput),
            'status' => $returnCode,
            'timed_out' => false,
            'execution_time' => $executionTime
        ];
    }

    /**
     * Sanitiza el comando recibido por GET
     */
    protected function sanitizeCommandInput(string $command): string
    {
        $command = trim($command);

        // Remover caracteres de control peligrosos
        $command = preg_replace('/[\x00-\x09\x0B-\x1F\x7F]/', '', $command);

        // Limitar longitud del comando
        if (strlen($command) > 2000) {
            $command = substr($command, 0, 2000);
        }

        return $command;
    }

    /**
     * Sanitiza el output del comando
     */
    protected function sanitizeOutput(string $output): array
    {
        if (empty($output)) {
            return [];
        }

        $lines = explode("\n", $output);
        $sanitizedLines = [];

        foreach ($lines as $line) {
            $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
            $cleanLine = preg_replace('/[\x00-\x1F\x7F]/u', '', $line);

            if ($cleanLine !== '') {
                $sanitizedLines[] = $cleanLine;
            }
        }

        return $sanitizedLines;
    }

    /**
     * Variables de entorno seguras
     */
    protected function getSafeEnvironmentVariables(): array
    {
        return [
            'PATH' => '/usr/local/bin:/usr/bin:/bin',
            'LANG' => 'en_US.UTF-8',
            'LC_ALL' => 'en_US.UTF-8',
            'PYTHONPATH' => '',
            'NODE_PATH' => '',
            'HOME' => '/tmp',
            'TMP' => '/tmp',
            'TMPDIR' => '/tmp',
        ];
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function setMaxOutputLength(int $maxOutputLength): self
    {
        $this->maxOutputLength = $maxOutputLength;
        return $this;
    }
}
