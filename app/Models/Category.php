<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['tenant_id', 'name'];

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    public function additionals()
    {
        return $this->hasMany(\App\Models\Additional::class);
    }
}
