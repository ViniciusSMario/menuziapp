@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Card 1: Gestão de Tenants -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">Gerenciamento de Tenants</h3>
            <p class="mb-3 text-sm text-gray-500">Administre as empresas cadastradas na plataforma.</p>
            <a href="{{ route('saas.tenants.index') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Gerenciar Tenants</a>
        </div>

        <!-- Card 2: Gestão de Planos -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">Gerenciamento de Planos</h3>
            <p class="mb-3 text-sm text-gray-500">Controle os planos de assinatura disponíveis para os tenants.</p>
            <a href="#" class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Gerenciar Planos (em breve)</a>
        </div>

        <!-- Card 3: Financeiro -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">Relatórios Financeiros</h3>
            <p class="mb-3 text-sm text-gray-500">Visualize relatórios de faturamento e status de assinaturas.</p>
            <a href="#" class="inline-block px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Ver Relatórios (em breve)</a>
        </div>

        <!-- Card 4: Webhooks e Integrações -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">Integrações & Webhooks</h3>
            <p class="mb-3 text-sm text-gray-500">Gerencie integrações como Stripe Webhooks e automações.</p>
            <a href="#" class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Configurar Integrações (em breve)</a>
        </div>

    </div>
</div>
@endsection
