<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restorations extends Model
{
    /** @use HasFactory<\Database\Factories\RestorationsFactory> */
protected $fillable = ["name","siteId","time","troubleshoot", "reason", "ticket_number", "isTrash"];
    use HasFactory;
}
