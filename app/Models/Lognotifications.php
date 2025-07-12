<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lognotifications extends Model
{
    /** @use HasFactory<\Database\Factories\LognotificationsFactory> */
    protected $fillable = [
        "key",
        "shortMsg",
        "siteId",
        "isTrash"
    ];
    use HasFactory;
}
