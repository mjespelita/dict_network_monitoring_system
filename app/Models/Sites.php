<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
    /** @use HasFactory<\Database\Factories\SitesFactory> */
protected $fillable = ["name","siteId","customerId","customerName","region","timezone","scenario","wan","connectedApNum","disconnectedApNum","isolatedApNum","connectedSwitchNum","disconnectedSwitchNum","type","isTrash"];
    use HasFactory;

    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customerId');
    }
}
