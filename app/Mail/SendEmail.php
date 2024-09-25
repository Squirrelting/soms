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

    public $subject;
    public $submittedminorOffenses;
    public $submittedmajorOffenses;
    public $student;
    /**
     * Create a new message instance.
     */
    public function __construct($subject, $submittedminorOffenses, $submittedmajorOffenses, $student)
    {
        $this->subject = $subject;
        $this->submittedminorOffenses = $submittedminorOffenses;
        $this->submittedmajorOffenses = $submittedmajorOffenses;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('samplecode42069@gmail.com', 'Santiago City National High School'),
            replyTo: [
                new Address('christiansalvador1906@gmail.com', 'Bossing'),
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
