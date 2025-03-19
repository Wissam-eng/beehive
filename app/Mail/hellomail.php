<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class hellomail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;


    public function __construct($otp)
    {
        $this->otp = $otp;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'high level family',
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.hello', // تحديد اسم العرض فقط بدون دمج متغير
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
