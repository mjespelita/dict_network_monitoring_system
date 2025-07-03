<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disconnecteddevices extends Model
{
    /** @use HasFactory<\Database\Factories\DisconnecteddevicesFactory> */
    protected $fillable = [
        "name",
        "siteId",
        "device_name",
        "device_mac",
        "device_type",
        "status",
        "isReported",
        "isTrash"
    ];
    use HasFactory;
}
