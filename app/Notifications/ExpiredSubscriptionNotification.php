<?php

namespace App\Notifications;

use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpiredSubscriptionNotification extends Notification
{
    use Queueable;

    public function __construct(public App $app, public Log $log)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ExpiredContractEmail
    {
        return (new ExpiredContractEmail($this->contract, $this->log))
            ->subject('expired contract notification')
            ->from(config('mail.from.address'), config('mail.from.address'))
            ->to($this->contract->customer->email);
    }
}
