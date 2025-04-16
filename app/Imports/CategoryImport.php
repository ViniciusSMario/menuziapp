<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class CategoryImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ignora header ou linhas vazias
            if (empty($row[0]) || $row[0] === 'Nome') continue;

            Category::create([
                'tenant_id' => Auth::user()->tenant_id,
                'name' => $row[0],
            ]);
        }
    }
}
