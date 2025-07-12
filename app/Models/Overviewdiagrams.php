<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overviewdiagrams extends Model
{
    /** @use HasFactory<\Database\Factories\OverviewdiagramsFactory> */
    protected $fillable = [
        "totalGatewayNum",
        "connectedGatewayNum",
        "disconnectedGatewayNum",
        "totalSwitchNum",
        "connectedSwitchNum",
        "disconnectedSwitchNum",
        "totalPorts",
        "availablePorts",
        "powerConsumption",
        "totalApNum",
        "connectedApNum",
        "isolatedApNum",
        "disconnectedApNum",
        "totalClientNum",
        "wiredClientNum",
        "wirelessClientNum",
        "guestNum",
        "siteId",
        "batch_number",
        "isTrash"
    ];
    use HasFactory;
}
