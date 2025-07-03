<?php

namespace App\Jobs;

use App\Mail\DisconnectedDeviceIncidentMailer;
use App\Models\Disconnecteddevices;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class DisconnectedDeviceIncidentMailSender implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $final_list_of_offline_devices;
    public function __construct($final_list_of_offline_devices)
    {
        $this->final_list_of_offline_devices = $final_list_of_offline_devices;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $key => $user) {

            foreach ($this->final_list_of_offline_devices as $key => $offlineDevice) {

                $incidentAlreadyReported = Disconnecteddevices::where('siteId', $offlineDevice['siteId'])->count();

                if ($incidentAlreadyReported === 0) {

                    Disconnecteddevices::create([
                        'name' => $offlineDevice['site'],
                        'siteId' => $offlineDevice['siteId'],
                        'device_name' => $offlineDevice['device']['name'],
                        'device_mac' => $offlineDevice['device']['mac'],
                        'device_type' => $offlineDevice['device']['type'],
                        'status' => $offlineDevice['status'],
                    ]);

                    Mail::to($user['email'])->send(new DisconnectedDeviceIncidentMailer(
                        $offlineDevice['site'],
                        $offlineDevice['siteId'],
                        $offlineDevice['device']['name'],
                        $offlineDevice['device']['mac'],
                        $offlineDevice['device']['type'],
                        $offlineDevice['status'],
                    ));

                }

            }
        }
    }
}
