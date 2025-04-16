<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Tenant;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterClientController;
use App\Http\Controllers\Auth\RegisterTenantController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Garcom\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaaS\TenantController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Tenant\{AdditionalController, CashRegisterController, CategoryController, ComandaController, CouponController, CozinhaController, GarcomController, HomeController as TenantHome, NeighborhoodController, OrderController, PDVController, ProductController, TableController};
use App\Http\Controllers\TenantConfigController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\InjectTenant;
use App\Models\Product;

// Rotas Públicas
Route::get('/', [RegisterTenantController::class, 'index'])->name('MenuziApp');

Route::view('/register-tenant', 'auth.register-tenant')->name('register.tenant');
Route::post('/register-tenant', [RegisterTenantController::class, 'store'])->name('register.tenant.process');

// Autenticação pública do tenant
Route::prefix('{tenant:slug}')
    ->middleware([InjectTenant::class])
    ->group(function () {

        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::get('/cadastro', [RegisterClientController::class, 'create'])->name('client.register');
        Route::post('/cadastro', [RegisterClientController::class, 'store']);

        // Site público
        Route::middleware(['isTenantActive'])->group(function () {
            Route::get('/', [SiteController::class, 'index'])->name('tenant.public.menu');
            Route::get('/cart', [SiteController::class, 'shop'])->name('shop');
            Route::get('/checkout', [SiteController::class, 'checkout'])->name('checkout');
            Route::post('/checkout', [SiteController::class, 'checkout'])->name('tenant.public.checkout');
            Route::post('/checkout/process', [SiteController::class, 'processOrder'])->name('site.checkout.process');
            Route::get('/orders/checkout/success', [SiteController::class, 'checkoutSuccess'])->name('checkout.success');
            Route::get('/orders/checkout/fail', [SiteController::class, 'checkoutFail'])->name('checkout.fail');
            Route::get('/meus-pedidos', [SiteController::class, 'meusPedidos'])->name('meus_pedidos');
            Route::get('/orders/{phone}', [SiteController::class, 'verificarUsuario'])->name('verificar.pedido');
            Route::get('/api/cupom/{code}', [CouponController::class, 'check'])->name('coupon.check');
            Route::get('/promocoes', [SiteController::class, 'promocoes'])->name('promotions');

            Route::get('/api/sabores-pizza', function () {
                $sabores = Product::whereHas('category', function ($q) {
                    $q->where('name', 'like', '%pizza%');
                })->get(['id', 'name', 'price']);
            
                return response()->json($sabores);
            })->name('sabores_pizza');
        });
    });

// APIs públicas
Route::get('/api/get-frete/{id}', [SiteController::class, 'getFrete'])->name('api.get-frete');
Route::get('/buscar-produtos', [SiteController::class, 'buscarProdutos'])->name('produtos.buscar');
Route::get('/api/get-endereco/{id}', [SiteController::class, 'getAddress'])->name('api.get-endereco');

// Comandas públicas
Route::get('/comandas-ativas/json', [ComandaController::class, 'ativasJson'])->name('tenant.comandas.ativas.json');

// Perfil
Route::middleware('tenant.auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// SaaS - Super Admin
Route::prefix('saas')->middleware(['auth', CheckRole::class . ':super_admin'])->name('saas.')->group(function () {
    Route::view('/dashboard', 'saas.dashboard')->name('dashboard');
    Route::resource('tenants', TenantController::class);
});

Route::prefix('admin/{tenant:slug}')
    ->middleware(['tenant.auth', CheckRole::class . ':admin', InjectTenant::class])
    ->name('tenant.')
    ->group(function () {

        // Liberado para admins mesmo sem assinatura ativa:
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.page');
        Route::get('/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
        Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    });

// Admin - Painel do Tenant
Route::prefix('admin/{tenant:slug}')
    ->middleware(['tenant.auth', CheckRole::class . ':admin', InjectTenant::class, 'check.subscription'])
    ->name('tenant.')
    ->group(function () {

        // Route::get('billing', function (Tenant $tenant) {
        //     return view('tenant.billing.index', compact('tenant'));
        // })->name('billing.page');

        Route::get('/dashboard', [TenantHome::class, 'index'])->name('dashboard');

        Route::resources([
            'products' => ProductController::class,
            'orders' => OrderController::class,
            'categories' => CategoryController::class,
            'additionals' => AdditionalController::class,
            'neighborhoods' => NeighborhoodController::class,
            'coupons' => CouponController::class,
        ]);

        // IMPORTAÇÃO
        Route::prefix('categories')->group(function () {
            Route::get('/importar/excel', [CategoryController::class, 'importForm'])->name('categories.import.form');
            Route::post('/importar/excel', [CategoryController::class, 'import'])->name('categories.import');
        });

        Route::prefix('products')->group(function () {
            Route::get('/importar/produtos', [ProductController::class, 'importForm'])->name('products.import.form');
            Route::post('/importar/produtos', [ProductController::class, 'import'])->name('products.import');
        });

        Route::prefix('additionals')->group(function () {
            Route::get('/importar/adicionais', [AdditionalController::class, 'importForm'])->name('additionals.import.form');
            Route::post('/importar/adicionais', [AdditionalController::class, 'import'])->name('additionals.import');
        });

        Route::prefix('neighborhoods')->group(function () {
            Route::get('/importar/bairros', [NeighborhoodController::class, 'importForm'])->name('neighborhoods.import.form');
            Route::post('/importar/bairros', [NeighborhoodController::class, 'import'])->name('neighborhoods.import');
        });
        // FIM IMPORTAÇÃO
        
        // Configurações do Tenant
        Route::get('/configuracoes', [TenantConfigController::class, 'edit'])->name('config.edit');
        Route::post('/configuracoes', [TenantConfigController::class, 'update'])->name('config.update');
        
        Route::post('/configuracoes/banners', [TenantConfigController::class, 'storeBanners'])->name('config.banner.store');
        Route::get('/configuracoes/banner/{id}', [TenantConfigController::class, 'editBanner'])->name('config.banner.edit');
        Route::put('/configuracoes/banner/{id}', [TenantConfigController::class, 'updateBanner'])->name('config.banner.update');
        Route::delete('/configuracoes/banner/{id}', [TenantConfigController::class, 'destroyBanner'])->name('config.banner.destroy');

        // PDV
        Route::prefix('pdv')->name('pdv.')->group(function () {
            Route::get('/', [PDVController::class, 'index'])->name('index');
            Route::post('/checkout', [PDVController::class, 'checkout'])->name('checkout');
            Route::get('/print/{order}', [PDVController::class, 'print'])->name('print');
            Route::get('/comandas', [PDVController::class, 'comandas'])->name('comandas');
            Route::post('/finalizar/{order}', [PDVController::class, 'finalizarPedido'])->name('finalizar');
            Route::post('adicionar-itens', [PDVController::class, 'adicionarItens'])->name('adicionar.itens');
            Route::get('/touch', [PDVController::class, 'pdvTouch'])->name('touch');
            Route::get('pedidos-recentes', [PDVController::class, 'pedidosRecentes'])->name('pedidos.recentes');
            Route::post('pedido/{order}/aceitar', [PDVController::class, 'aceitarPedido'])->name('aceitar');
            Route::post('/pedido/{order}/regenerar-nota', [PdvController::class, 'regenerarNota'])->name('regenerar-nota');
            Route::post('/pedido/{order}/imprimir', [PdvController::class, 'imprimirViaTermica'])->name('imprimir');
        });

        // Caixa
        Route::prefix('caixa')->name('caixa.')->group(function () {
            Route::post('/abrir', [CashRegisterController::class, 'open'])->name('abrir');
            Route::post('/fechar', [CashRegisterController::class, 'close'])->name('fechar');
            Route::post('/suprimento', [CashRegisterController::class, 'suprimento'])->name('suprimento');
            Route::post('/sangria', [CashRegisterController::class, 'sangria'])->name('sangria');
        });

        Route::get('/caixa/{id}/relatorio', [CashRegisterController::class, 'relatorio'])->name('caixa.relatorio');
        Route::get('/caixa/{id}/export/pdf', [CashRegisterController::class, 'exportPdf'])->name('caixa.export.pdf');
        Route::get('/caixa/{id}/export/excel', [CashRegisterController::class, 'exportExcel'])->name('caixa.export.excel');
        Route::get('/caixas', [CashRegisterController::class, 'historico'])->name('caixa.historico');

        // Mesas
        Route::resource('tables', TableController::class)->parameters(['tables' => 'table']);

        // Comandas
        Route::prefix('comandas')->name('comandas.')->group(function () {
            Route::get('/', [ComandaController::class, 'index'])->name('index');
            Route::get('/{table}', [ComandaController::class, 'show'])->name('show');
            Route::post('/{table}/fechar', [ComandaController::class, 'fechar'])->name('fechar');
            Route::get('/{table}/imprimir', [ComandaController::class, 'imprimir'])->name('imprimir');
        });

        // Garçons
        Route::prefix('garcons')->name('garcons.')->group(function () {
            Route::get('/', [GarcomController::class, 'index'])->name('index');
            Route::get('/criar', [GarcomController::class, 'create'])->name('create');
            Route::post('/', [GarcomController::class, 'store'])->name('store');
            Route::delete('/{id}', [GarcomController::class, 'destroy'])->name('destroy');
        });

        // Cozinha
        Route::prefix('cozinha')->name('cozinha.')->group(function () {
            Route::get('/', [CozinhaController::class, 'index'])->name('index');
            Route::get('/json', [CozinhaController::class, 'json'])->name('json');
            Route::post('/pedidos/{order}/status', [CozinhaController::class, 'atualizarStatus'])->name('status');
        });

        // Checkout com stripe
        Route::get('/checkout', fn (Request $request, Tenant $tenant) => view('tenant.checkout'))->name('checkout');
        Route::post('/checkout/process', function (Request $request) {
            $request->validate(['payment_method' => 'required']);
            $user = auth()->user();
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($request->payment_method);
            $user->newSubscription('default', 'price_1R6Am3CKWcIpOQBwpJkfkUWc')->create($request->payment_method);
            return redirect()->route('tenant.dashboard')->with('success', 'Assinatura realizada!');
        })->name('checkout.process');
    });

// Garçom
Route::prefix('garcom')->middleware(['tenant.auth', CheckRole::class . ':garcom'])->name('garcom.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mesa/{table}', [DashboardController::class, 'comanda'])->name('mesa');
    Route::post('/mesa/{table}/pedido', [DashboardController::class, 'adicionarPedido'])->name('pedido.store');
    Route::post('/mesa/{table}/fechar', [DashboardController::class, 'fecharComanda'])->name('mesa.fechar');
});

Route::post('/webhook/pagamento', [WebhookController::class, 'handle']);

Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Redirecionamento genérico por tipo de usuário
Route::get('/redirect', function () {
    $user = auth()->user();
    return match ($user->type) {
        'super_admin' => redirect()->route('saas.dashboard'),
        'admin' => redirect()->route('tenant.dashboard', ['tenant' => $user->tenant->slug]),
        'garcom' => redirect()->route('garcom.dashboard', ['tenant' => $user->tenant->slug]),
    };
})->middleware('tenant.auth');


require __DIR__ . '/auth.php';
