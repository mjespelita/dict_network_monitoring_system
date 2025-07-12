<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    /** @use HasFactory<\Database\Factories\DevicesFactory> */
    protected $fillable = [
        "device_name",
        "ip_address",
        "status",
        "model",
        "version",
        "uptime",
        "cpu",
        "memory",
        "public_ip",
        "link_speed",
        "duplex",
        "siteId",
        "batch_number",
        "isTrash"
    ];
    use HasFactory;
}
