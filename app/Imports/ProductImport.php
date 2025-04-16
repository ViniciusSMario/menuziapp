<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {
            // Colunas esperadas: Nome | Preço | category_id | Preço promocional (opcional)
            $name = $row[0] ?? null;
            $price = $row[1] ?? null;
            $category_id = $row[2] ?? null;
            $promotion_price = $row[3] ?? null;

            if (!$name || !$price || !$category_id) continue;

            // Verifica se o category_id pertence ao tenant autenticado
            $category = Category::where('tenant_id', Auth::user()->tenant_id)
                                ->where('id', $category_id)
                                ->first();

            if (!$category) continue;

            Product::create([
                'tenant_id' => Auth::user()->tenant_id,
                'name' => $name,
                'price' => floatval($price),
                'promotion_price' => $promotion_price ? floatval($promotion_price) : null,
                'on_promotion' => $promotion_price ? true : false,
                'category_id' => $category->id,
            ]);
        }
    }
}