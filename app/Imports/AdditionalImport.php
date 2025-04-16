<?php

namespace App\Imports;

use App\Models\Additional;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class AdditionalImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {
            $name = $row[0] ?? null;
            $price = $row[1] ?? null;
            $category_id = $row[2] ?? null;

            if (!$name || !$price || !$category_id) continue;

            // Verifica se o category_id pertence ao tenant logado
            $category = Category::where('tenant_id', Auth::user()->tenant_id)
                                ->where('id', $category_id)
                                ->first();

            if (!$category) continue;

            Additional::create([
                'tenant_id' => Auth::user()->tenant_id,
                'name' => $name,
                'price' => floatval($price),
                'category_id' => $category_id,
            ]);
        }
    }
}