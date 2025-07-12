<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientdetails extends Model
{
    /** @use HasFactory<\Database\Factories\ClientdetailsFactory> */
    protected $fillable = [
        "mac",
        "name",
        "deviceType",
        "switchName",
        "switchMac",
        "port",
        "standardPort",
        "trafficDown",
        "trafficUp",
        "uptime",
        "guest",
        "blocked",
        "siteId",
        "batch_number",
        "isTrash"
    ];
    use HasFactory;
}
