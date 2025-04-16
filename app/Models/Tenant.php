<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tenant extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'address', 'delivery_time', 'open_hours', 'main_color'];

    public function getMainBannerAttribute()
    {
        $banner = $this->hasOne(\App\Models\Banner::class)
            ->where('main_banner', true)
            ->where('active', true)
            ->first();

        return $banner?->image;
    }

    public function bannersOrdenados()
    {
        return $this->banners()
            ->where('active', true)
            ->where('main_banner', false)
            ->orderBy('order')
            ->get();
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    public function categories()
    {
        return $this->hasMany(\App\Models\Category::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }

    public function getHorarioFuncionamentoHoje()
    {
        if (!$this->open_hours) {
            return 'Horário não definido';
        }

        $hours = json_decode($this->open_hours, true);
        $today = strtolower(Carbon::now()->locale('en_US')->dayName); // Ex: monday

        if (!isset($hours[$today]) || $hours[$today]['closed']) {
            return 'Fechado hoje';
        }

        return substr($hours[$today]['open'], 0, 5) . ' às ' . substr($hours[$today]['close'], 0, 5);
    }

    public function isOpen()
    {
        if (!$this->open_hours) {
            return false;
        }

        $hours = json_decode($this->open_hours, true);
        $today = strtolower(Carbon::now()->locale('en_US')->dayName);


        if (!isset($hours[$today]) || $hours[$today]['closed']) {
            return false;
        }

        $now = Carbon::now()->format('H:i');

        return $now >= $hours[$today]['open'] && $now <= $hours[$today]['close'];
    }
}
