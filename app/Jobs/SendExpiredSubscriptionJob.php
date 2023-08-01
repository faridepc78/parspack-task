<?php

namespace App\Jobs;

use App\Models\App;
use App\Models\Log;
use App\Models\User;
use App\Notifications\SendExpiredSubscriptionNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SendExpiredSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user, public App $app, public Log $log)
    {
    }

    public function handle(): bool
    {
        $this->user->notify(new SendExpiredSubscriptionNotification($this->app));

        return $this->log
            ->update([
                'sent_at' => Carbon::now(),
                'is_sent' => true,
            ]);
    }
}
