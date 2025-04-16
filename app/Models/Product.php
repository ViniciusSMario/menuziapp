<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'price', 'image', 'category_id', 'promotion_price', 'on_promotion'];

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function getFinalPriceAttribute()
    {
        return $this->on_promotion && $this->promotion_price
            ? $this->promotion_price
            : $this->price;
    }
}
