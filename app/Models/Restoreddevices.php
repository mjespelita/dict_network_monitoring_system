<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restoreddevices extends Model
{
    /** @use HasFactory<\Database\Factories\RestoreddevicesFactory> */
protected $fillable = [
    "name",
    "device_name",
    "device_mac",
    "device_type",
    "status",
    "ticket_number",
    "siteId",
    "reason",
    "troubleshoot",
    "isTrash"];
    use HasFactory;
}
