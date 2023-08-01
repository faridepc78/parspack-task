<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Queue::failing(function (JobFailed $event) {
            $payload = json_decode($event->job->getRawBody());
            $data = unserialize($payload->data->command);
            $log = $data->log;
            $log->update([
                'error_message' => $event->exception->getMessage(),
                'is_sent' => false,
            ]);
        });
    }
}
