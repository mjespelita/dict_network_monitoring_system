<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportDisconnectedDeviceMailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $name;
    public $deviceName;
    public $deviceMac;
    public $deviceType;
    public $status;
    public $siteId;
    public $reason;
    public $troubleshoot;
    public function __construct(
        $name,
        $deviceName,
        $deviceMac,
        $deviceType,
        $status,
        $siteId,
        $reason,
        $troubleshoot
    )
    {
        $this->name = $name;
        $this->deviceName = $deviceName;
        $this->deviceMac = $deviceMac;
        $this->deviceType = $deviceType;
        $this->status = $status;
        $this->siteId = $siteId;
        $this->reason = $reason;
        $this->troubleshoot = $troubleshoot;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Report Disconnected Device Mailer',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.report-disconnected-device-mailer',
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
