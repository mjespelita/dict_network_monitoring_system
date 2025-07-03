<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportIncidentMailer extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $siteId;
    public $time;
    public $reason;
    public $troubleshoot;
    public function __construct($name, $siteId, $time, $reason, $troubleshoot)
    {
        $this->name = $name;
        $this->siteId = $siteId;
        $this->time = $time;
        $this->reason = $reason;
        $this->troubleshoot = $troubleshoot;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Report Incident Mailer',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.report-incident-mailer',
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
