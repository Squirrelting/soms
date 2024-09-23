<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailMessage;
    public $subject;

    public  $submittedmajorOffenses;

    public $submittedminorOffenses;
    /**
     * Create a new message instance.
     */
    public function __construct($message, $subject, $submittedminorOffenses, $submittedmajorOffenses)
    {
        $this->mailMessage = $message;
        $this->subject = $subject;
        $this->submittedminorOffenses = $submittedminorOffenses;
        $this->submittedmajorOffenses = $submittedmajorOffenses;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('samplecode42069@gmail.com', 'Sample'),
            replyTo: [
                new Address('christiansalvador1906@gmail.com', 'Yanong'),
            ],

            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail-template.send-mail',
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
