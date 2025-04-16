<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cep',
        'rua',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'bairro', 'name');
    }

    public function formatted()
    {
        $parts = [
            "{$this->rua}, {$this->numero}",
            "{$this->bairro} - {$this->cidade}",
        ];

        return implode(' - ', array_filter($parts));
    }
}
