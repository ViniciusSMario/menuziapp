<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'shipping_cost',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}

