<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditlogs extends Model
{
    /** @use HasFactory<\Database\Factories\AuditlogsFactory> */
protected $fillable = ["time","operator","resource","ip","auditType","level","result","content","label","oldValue","newValue","isTrash"];
    use HasFactory;
}
