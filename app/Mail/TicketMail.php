<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $sites_id;
    public $ticket_number;
    public $date_reported;
    public $name;
    public $address;
    public $nearest_landmark;
    public $issue;
    public $troubleshooting;

    public function __construct($sites_id, $ticket_number, $date_reported, $name, $address, $nearest_landmark, $issue, $troubleshooting)
    {
        $this->sites_id = $sites_id;
        $this->ticket_number = $ticket_number;
        $this->date_reported = $date_reported;
        $this->name = $name;
        $this->address = $address;
        $this->nearest_landmark = $nearest_landmark;
        $this->issue = $issue;
        $this->troubleshooting = $troubleshooting;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Ticket!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.ticket-mail',
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
