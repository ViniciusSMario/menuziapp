<?php

namespace App\Imports;

use App\Models\Neighborhood;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class NeighborhoodImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {
            $name = $row[0] ?? null;
            $shipping_cost = $row[1] ?? null;

            if (!$name || !$shipping_cost) continue;

            Neighborhood::create([
                'tenant_id' => Auth::user()->tenant_id,
                'name' => $name,
                'shipping_cost' => floatval($shipping_cost),
            ]);
        }
    }
}