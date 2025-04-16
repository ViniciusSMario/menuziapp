<?php

namespace App\Helpers;

use Carbon\Carbon;

class TenantHelper
{
    public static function isOpenNow($openHoursJson)
    {
        if (!$openHoursJson) return false;

        $hours = json_decode($openHoursJson, true);
        $today = strtolower(Carbon::now()->locale('pt_BR')->dayName); // ex: monday
        $now = Carbon::now()->format('H:i');

        if (!isset($hours[$today]) || $hours[$today]['closed']) {
            return false;
        }

        return $now >= $hours[$today]['open'] && $now <= $hours[$today]['close'];
    }
}