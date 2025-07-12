<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    /** @use HasFactory<\Database\Factories\ClientsFactory> */
    protected $fillable = [
        "mac_address",
        "device_name",
        "device_type",
        "connected_device_type",
        "switch_name",
        "port",
        "standard_port",
        "network_theme",
        "uptime",
        "traffic_down",
        "traffic_up",
        "status",
        "siteId",
        "batch_number",
        "isTrash"
    ];
    use HasFactory;
}
