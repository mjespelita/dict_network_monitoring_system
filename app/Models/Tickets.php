<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Tickets extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\TicketsFactory> */
    protected $fillable = [
        "sites_id",
        "ticket_number",
        "date_reported",
        "name","address",
        "nearest_landmark",
        "issue",
        "troubleshooting",
        "status",
        "isTrash"
    ];
    use HasFactory;

    use \OwenIt\Auditing\Auditable;

    protected $auditExclude = ['id'];

    public function generateTags(): array
    {
        return [
            'ticket_number:' . $this->ticket_number,
            'sites_id:' . $this->sites_id,
        ];
    }

    public function sites()
    {
        return $this->belongsTo(Sites::class, 'sites_id');
    }
}
