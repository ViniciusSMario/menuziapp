<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GarcomController extends Controller
{
    public function index()
    {
        $garcons = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('type', 'garcom')
            ->get();

        return view('tenant.garcons.index', compact('garcons'));
    }

    public function create()
    {
        return view('tenant.garcons.create');
    }

    public function store(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $data['type'] = 'garcom';
        $data['tenant_id'] = auth()->user()->tenant_id;
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('tenant.garcons.index', ['tenant' => $tenant->slug])->with('success', 'Garçom cadastrado com sucesso!');
    }

    public function destroy(Tenant $tenant, $id)
    {
        $garcom = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('type', 'garcom')
            ->findOrFail($id);

        $garcom->delete();

        return redirect()->route('tenant.garcons.index', ['tenant' => $tenant->slug])->with('success', 'Garçom deletado com sucesso!');
    }
}