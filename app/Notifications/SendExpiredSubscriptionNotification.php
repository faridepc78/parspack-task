<?php

namespace App\Notifications;

use App\Mail\ExpiredSubscriptionEmail;
use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendExpiredSubscriptionNotification extends Notification
{
    use Queueable;

    public function __construct(public App $app)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ExpiredSubscriptionEmail
    {
        return (new ExpiredSubscriptionEmail($this->app, $notifiable))
            ->subject('expired subscription notification')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to($notifiable->toArray()['email']);
    }
}
