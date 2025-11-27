<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [CatalogController::class, 'index'])->name('home');

// ========================================
// RUTAS PÚBLICAS DEL ECOMMERCE
// ========================================
Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/producto/{product}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');

// API pública para validar carrito
Route::post('/api/carrito/validar', [CatalogController::class, 'validateCart'])->name('cart.validate');

// Ruta para obtener CSRF token fresh (no requiere autenticación)
Route::middleware('web')->get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.token');

// Checkout (requiere autenticación)
Route::middleware(['web', 'auth:sanctum'])->group(function () {
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/procesar', [CartController::class, 'processOrder'])->name('cart.process');
    
    // Rutas para pagos QR
    Route::post('/qr/generar', [App\Http\Controllers\QrPaymentController::class, 'generateQR'])->name('qr.generate');
    Route::post('/qr/verificar', [App\Http\Controllers\QrPaymentController::class, 'verifyPayment'])->name('qr.verify');
});

// Callback público para PagoFácil (sin middleware de autenticación)
Route::post('/qr/callback/{payment_number}', [App\Http\Controllers\QrPaymentController::class, 'callback'])->name('qr.callback');

// ========================================
// ÁREA DE CLIENTE
// ========================================
Route::middleware(['auth:sanctum', 'role:cliente'])->group(function () {
    Route::get('/mis-pedidos', [App\Http\Controllers\ClientOrdersController::class, 'index'])->name('client.orders');
    Route::get('/mis-pedidos/{order}', [App\Http\Controllers\ClientOrdersController::class, 'show'])->name('client.orders.show');
    Route::get('/mi-perfil', [App\Http\Controllers\ClientProfileController::class, 'show'])->name('client.profile.show');
    Route::put('/mi-perfil', [App\Http\Controllers\ClientProfileController::class, 'update'])->name('client.profile.update');
    Route::put('/mi-perfil/password', [App\Http\Controllers\ClientProfileController::class, 'updatePassword'])->name('client.profile.password');
});

// ========================================
// PANEL ADMINISTRATIVO
// ========================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::resource('users', UserController::class);

    // Gestión de productos
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('measurements', MeasurementController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('clients', ClientController::class);
    
    // Gestión de Inventario - RUTAS ESPECÍFICAS PRIMERO
    Route::get('/inventory/alerts/low-stock', [InventoryController::class, 'lowStockAlerts'])
        ->name('inventory.lowStockAlerts');
    Route::get('/inventory/alerts/critical-stock', [InventoryController::class, 'criticalStockAlerts'])
        ->name('inventory.criticalStockAlerts');
    Route::put('/inventory/bulk-update-stock', [InventoryController::class, 'bulkUpdateStock'])
        ->name('inventory.bulkUpdateStock');
    
    // Gestión de Movimientos de Inventario
    Route::prefix('inventory/movements')->name('inventory.movements.')->group(function () {
        Route::get('/', [App\Http\Controllers\InventoryMovementController::class, 'index'])->name('index');
        Route::get('/entrada/create', [App\Http\Controllers\InventoryMovementController::class, 'createEntrada'])->name('entrada.create');
        Route::post('/entrada', [App\Http\Controllers\InventoryMovementController::class, 'storeEntrada'])->name('entrada.store');
        Route::get('/salida/create', [App\Http\Controllers\InventoryMovementController::class, 'createSalida'])->name('salida.create');
        Route::post('/salida', [App\Http\Controllers\InventoryMovementController::class, 'storeSalida'])->name('salida.store');
        Route::get('/ajuste/create', [App\Http\Controllers\InventoryMovementController::class, 'createAjuste'])->name('ajuste.create');
        Route::post('/ajuste', [App\Http\Controllers\InventoryMovementController::class, 'storeAjuste'])->name('ajuste.store');
        Route::get('/{movement}', [App\Http\Controllers\InventoryMovementController::class, 'show'])->name('show');
        Route::post('/{movement}/apply', [App\Http\Controllers\InventoryMovementController::class, 'apply'])->name('apply');
        Route::post('/{movement}/revert', [App\Http\Controllers\InventoryMovementController::class, 'revert'])->name('revert');
        Route::delete('/{movement}', [App\Http\Controllers\InventoryMovementController::class, 'destroy'])->name('destroy');
    });

    // Resource route al final (captura /inventory/{id})
    Route::resource('inventory', InventoryController::class);
    Route::put('/inventory/{inventory}/update-stock', [InventoryController::class, 'updateStock'])
        ->name('inventory.updateStock');
    
    // Gestión de Compras
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/{purchase}/recibir', [PurchaseController::class, 'recibir'])->name('purchases.recibir');
    Route::post('/purchases/{purchase}/procesar-recepcion', [PurchaseController::class, 'procesarRecepcion'])->name('purchases.procesarRecepcion');
    
    // Gestión de Órdenes/Ventas
    Route::resource('orders', App\Http\Controllers\OrderController::class);
});
