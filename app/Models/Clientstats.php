<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientstats extends Model
{
    /** @use HasFactory<\Database\Factories\ClientstatsFactory> */
    protected $fillable = [
        "total",
        "wireless",
        "wired",
        "num2g",
        "num5g",
        "num6g",
        "numUser",
        "numGuest",
        "numWirelessUser",
        "numWirelessGuest",
        "num2gUser",
        "num5gUser",
        "num6gUser",
        "num2gGuest",
        "num5gGuest",
        "num6gGuest",
        "poor",
        "fair",
        "noData",
        "good",
        "siteId",
        "batch_number",
        "isTrash"
    ];
    use HasFactory;
}

// add na sin artisan command
