<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batches extends Model
{
    /** @use HasFactory<\Database\Factories\BatchesFactory> */
protected $fillable = ["batch_number","isTrash"];
    use HasFactory;
}
