<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topcpuusages extends Model
{
    /** @use HasFactory<\Database\Factories\TopcpuusagesFactory> */
protected $fillable = ["name","mac","cpuUtil","model","modelVersion","type","isTrash"];
    use HasFactory;
}
