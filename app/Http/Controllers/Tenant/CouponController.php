<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index()
    {
        try {
            $coupons = Coupon::where('tenant_id', auth()->user()->tenant_id)->latest()->get();
            return view('tenant.coupons.index', compact('coupons'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar cupons: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao carregar os cupons.');
        }
    }

    public function create()
    {
        return view('tenant.coupons.create');
    }

    public function store(Request $request, Tenant $tenant)
    {
        try {
            $data = $request->validate([
                'code' => 'required|string|max:50|unique:coupons,code',
                'type' => 'required|in:percent,fixed',
                'discount' => 'required|numeric|min:0',
                'expires_at' => 'nullable|date'
            ]);

            $data['tenant_id'] = auth()->user()->tenant_id;

            Coupon::create($data);

            return redirect()->route('tenant.coupons.index', ['tenant' => $tenant->slug])->with('success', 'Cupom criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar cupom: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao criar o cupom.')->withInput();
        }
    }

    public function edit(Tenant $tenant, Coupon $coupon)
    {
        try {
            if ($coupon->tenant_id != auth()->user()->tenant_id) abort(403);
            return view('tenant.coupons.create', compact('coupon'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar cupom para edição: ' . $e->getMessage());
            return redirect()->route('tenant.coupons.index', ['tenant' => $tenant->slug])->with('error', 'Erro ao carregar o cupom.');
        }
    }

    public function update(Request $request, Tenant $tenant, Coupon $coupon)
    {
        try {
            if ($coupon->tenant_id != auth()->user()->tenant_id) abort(403);

            $data = $request->validate([
                'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
                'type' => 'required|in:percent,fixed',
                'discount' => 'required|numeric|min:0',
                'expires_at' => 'nullable|date'
            ]);

            $coupon->update($data);

            return redirect()->route('tenant.coupons.index', ['tenant' => $tenant->slug])->with('success', 'Cupom atualizado!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cupom: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar o cupom.')->withInput();
        }
    }

    public function destroy(Tenant $tenant, Coupon $coupon)
    {
        try {
            if ($coupon->tenant_id != auth()->user()->tenant_id) abort(403);
            $coupon->delete();
            return redirect()->route('tenant.coupons.index', ['tenant' => $tenant->slug])->with('success', 'Cupom excluído!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir cupom: ' . $e->getMessage());
            return redirect()->route('tenant.coupons.index', ['tenant' => $tenant->slug])->with('error', 'Erro ao excluir o cupom.');
        }
    }

    public function check(Tenant $tenant, $code)
    {
        try {
            $coupon = Coupon::where('code', strtoupper($code))
                ->where('tenant_id', $tenant->id)
                ->where('active', true)
                ->first();

            if ($coupon) {
                return response()->json([
                    'valid' => true,
                    'id' => $coupon->id,
                    'type' => $coupon->type,
                    'discount' => $coupon->discount
                ]);
            }

            return response()->json(['valid' => false]);
        } catch (\Exception $e) {
            Log::error('Erro ao verificar cupom: ' . $e->getMessage());
            return response()->json(['valid' => false], 500);
        }
    }
}
