<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    Public $otp; //the otp code tobe sent in email is taken as parameter. otp variable is created.

    public function __construct($otp) //properties of the class will go to the view.
    {
        $this->otp=$otp; //variable is bound within constructor. Because whenever the object of mail class is created,this otp code will be passed within constructor as parameter. the code will be available in the blade view.
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'O T P Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content( //the attached view file is the content body for the email. properties of the class will go to the view.
            view: 'email.OTPMail', //view of the email body content is created in: Resources→ Views→ OTPMail.blade.php, No need for extra method/parameters/data like: with data and with view data.
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
