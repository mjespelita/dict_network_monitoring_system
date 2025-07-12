<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
    /** @use HasFactory<\Database\Factories\SitesFactory> */
    protected $fillable = [
        "name",
        "siteId",
        "region",
        "timezone",
        "scenario",
        "type",
        "supportES",
        "supportL2",
        "batch_number",
        "isTrash"
    ];
    use HasFactory;

    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customerId');
    }

    public function tickets()
    {
        return $this->hasMany(Tickets::class);
    }
}
