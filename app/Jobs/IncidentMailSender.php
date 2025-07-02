<?php

namespace App\Jobs;

use App\Mail\IncidentMailer;
use App\Models\Incidents;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class IncidentMailSender implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $final_list_of_offline_sites;
    public function __construct($final_list_of_offline_sites)
    {
        $this->final_list_of_offline_sites = $final_list_of_offline_sites;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::all();

        foreach ($users as $key => $user) {

            foreach ($this->final_list_of_offline_sites as $key => $offlineSite) {

                $incidentAlreadyReported = Incidents::where('siteId', $offlineSite['siteId'])->count();

                if ($incidentAlreadyReported === 0) {

                    Incidents::create([
                        'name' => $offlineSite['name'],
                        'siteId' => $offlineSite['siteId'],
                        'time' => $offlineSite['time'],
                    ]);

                    Mail::to($user->email)->send(new IncidentMailer(
                        $offlineSite['name'],
                        $offlineSite['siteId'],
                        $offlineSite['time'],
                    ));

                }

            }
        }
    }
}
