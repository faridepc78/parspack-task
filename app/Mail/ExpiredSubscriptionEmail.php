<?php

namespace App\Mail;

use App\Models\App;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpiredSubscriptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public App $app, public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'expired subscription notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.expired_subscription',
            with: [
                'app' => $this->app,
                'user' => $this->user,
            ]
        );
    }
}
