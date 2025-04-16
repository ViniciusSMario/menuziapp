<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'tenant_id', 'code', 'type', 'discount', 'valid_until', 'active'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

}
