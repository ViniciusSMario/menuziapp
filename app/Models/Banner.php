<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'title',
        'image',
        'link',
        'main_banner',
        'active',
        'order'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}