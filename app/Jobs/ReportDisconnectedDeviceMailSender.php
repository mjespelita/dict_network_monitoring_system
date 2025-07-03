<?php

namespace App\Jobs;

use App\Mail\ReportDisconnectedDeviceMailer;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ReportDisconnectedDeviceMailSender implements ShouldQueue
{
    use Queueable;

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
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Mail::to($user['email'])->send(new ReportDisconnectedDeviceMailer(
                $this->name,
                $this->deviceName,
                $this->deviceMac,
                $this->deviceType,
                $this->status,
                $this->siteId,
                $this->reason,
                $this->troubleshoot
            ));
        }
    }
}
