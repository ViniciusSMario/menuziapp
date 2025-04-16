<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'initial_amount',
        'final_amount',
        'opened_at',
        'closed_at',
        'is_open',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected $dates = ['opened_at', 'closed_at'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
