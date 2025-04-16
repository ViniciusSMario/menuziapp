<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class TenantConfigController extends Controller
{
    public function storeBanners(Request $request, Tenant $tenant)
    {
        try {
            $request->validate([
                'title' => 'nullable|string|max:255',
                'image' => 'required|image',
                'link' => 'nullable|url',
                'main_banner' => 'nullable|boolean',
                'active' => 'nullable|boolean',
            ]);

            if ($request->boolean('main_banner')) {
                Banner::where('tenant_id', $tenant->id)
                    ->where('main_banner', true)
                    ->update(['main_banner' => false]);
            }

            $path = $request->file('image')->store('banners', 'public');

            Banner::create([
                'tenant_id' => $tenant->id,
                'title' => $request->title,
                'image' => $path,
                'order' => $request->input('order', 0),
                'link' => $request->link,
                'main_banner' => $request->boolean('main_banner'),
                'active' => $request->boolean('active', true),
            ]);

            return redirect()->route('tenant.config.edit', $tenant->slug)->with('success', 'Banner adicionado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao adicionar banner: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao adicionar o banner.');
        }
    }

    public function edit()
    {
        try {
            $tenant = auth()->user()->tenant;
            $banners = Banner::where('tenant_id', $tenant->id)->orderBy('order')->get();

            return view('tenant.config.edit', compact('tenant', 'banners'));
        } catch (Exception $e) {
            Log::error('Erro ao carregar configurações do estabelecimento: ' . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar as configurações do estabelecimento.');
        }
    }

    public function update(Request $request, Tenant $tenant)
    {
        try {
            $tenant = auth()->user()->tenant;

            $data = $request->validate([
                'address' => 'nullable|string|max:255',
                'delivery_time' => 'nullable|string|max:50',
                'logo' => 'nullable|image|max:2048',
                'open_hours' => 'nullable|array',
                'main_color' => ['required', 'regex:/^#[a-fA-F0-9]{6}$/']
            ]);

            $openHours = [];
            foreach ($request->open_hours as $day => $info) {
                $openHours[$day] = [
                    'open' => $info['closed'] ?? false ? null : $info['open'],
                    'close' => $info['closed'] ?? false ? null : $info['close'],
                    'closed' => isset($info['closed']),
                ];
            }
            $data['open_hours'] = json_encode($openHours);

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('logos', 'public');
                $data['logo'] = $path;
            }

            $tenant->update($data);

            return redirect()->route('tenant.config.edit', ['tenant' => $tenant->slug])->with('success', 'Informações atualizadas!');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar configurações do estabelecimento: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao atualizar as informações.');
        }
    }

    public function editBanner($tenant, $id)
    {
        try {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
            $banner = Banner::where('tenant_id', $tenant->id)->findOrFail($id);
            return view('tenant.config.edit-banner', compact('tenant', 'banner'));
        } catch (Exception $e) {
            Log::error("Erro ao editar banner ID $id: " . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao carregar o banner para edição.');
        }
    }

    public function updateBanner(Request $request, $tenant, $id)
    {
        try {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
            $banner = Banner::where('tenant_id', $tenant->id)->findOrFail($id);

            $data = $request->only(['title', 'link', 'order', 'main_banner', 'active']);
            $data['main_banner'] = $request->has('main_banner');
            $data['active'] = $request->has('active');

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('banners', 'public');
                $data['image'] = $path;
            }

            $banner->update($data);

            return redirect()->route('tenant.config.edit', $tenant->slug)
                ->with('success', 'Banner atualizado com sucesso!');
        } catch (Exception $e) {
            Log::error("Erro ao atualizar banner ID $id: " . $e->getMessage());
            return redirect()->back()->withInput()->withErrors('Erro ao atualizar o banner.');
        }
    }

    public function destroyBanner($tenant, $id)
    {
        try {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
            $banner = Banner::where('tenant_id', $tenant->id)->findOrFail($id);

            $banner->delete();

            return back()->with('success', 'Banner removido com sucesso!');
        } catch (Exception $e) {
            Log::error("Erro ao deletar banner ID $id: " . $e->getMessage());
            return redirect()->back()->withErrors('Erro ao remover o banner.');
        }
    }
}
