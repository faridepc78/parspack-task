<?php

namespace App\Mail;

use App\Models\Contract;
use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ExpiredSubscriptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contract $contract, public Log $log)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'expired contract notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.expired_contract.'.App::getLocale().'.index',
            with: [
                'contract' => $this->contract,
                'description' => $this->log->body,
            ]
        );
    }
}
