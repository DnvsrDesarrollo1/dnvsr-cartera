<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; // Importar la fachada File

class ProcessDatabaseQueryLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $query;
    protected $bindings;
    protected $time;
    protected $user_id;
    protected $ip_address;

    /**
     * Create a new job instance.
     */
    public function __construct($query, $bindings, $time, $user_id, $ip_address)
    {
        $this->query = $query;
        $this->bindings = $bindings;
        $this->time = $time;
        $this->user_id = $user_id;
        $this->ip_address = $ip_address;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $logPath = storage_path('logs/bd_operations.log');

        // Format the SQL query with bindings for better readability
        $sql = vsprintf(str_replace('?', '%s', $this->query), $this->bindings);

        // Create formatted log message
        $logMessage = sprintf(
            "[%s] Query executed:\nSQL: %s\nExecution Time: %s ms\nUser: %s\nIP: %s\n%s\n",
            now()->format('Y-m-d H:i:s'),
            $sql,
            number_format($this->time, 2),
            $this->user_id ?? 'system',
            $this->ip_address ?? 'unknown',
            str_repeat('-', 80)
        );

        // Ensure the logs directory exists
        if (!File::exists(dirname($logPath))) {
            File::makeDirectory(dirname($logPath), 0777, true, true);
        }

        // Append log message to the file
        File::append($logPath, $logMessage);
    }
}
