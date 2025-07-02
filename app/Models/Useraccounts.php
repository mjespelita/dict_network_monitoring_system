<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Useraccounts extends Model
{
    /** @use HasFactory<\Database\Factories\UseraccountsFactory> */
protected $fillable = ["name","email","password","role","isTrash"];
    use HasFactory;
}
