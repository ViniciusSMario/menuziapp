<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'items',
        'total',
        'status',
        'delivery_type',
        'shipping_cost',
        'payment_method',
        'troco',
        'address_id',
        'coupon_id',
        'table_id',
        'cash_register_id',
        'nota_pdf'
    ];

    const STATUS = [
        'pendente',     // acabou de ser criado
        'aceito',       // validado pelo atendente
        'em_preparo',   // enviado à cozinha
        'concluido',    // pronto
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    public function coupon()
    {
        return $this->belongsTo(\App\Models\Coupon::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['customer_phone'] = preg_replace('/\D/', '', $value);
    }
}
