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

// Checkout (requiere autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/procesar', [CartController::class, 'processOrder'])->name('cart.process');
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
    
    // Gestión de Inventario
    Route::resource('inventory', InventoryController::class);
    Route::put('/inventory/{inventory}/update-stock', [InventoryController::class, 'updateStock'])
        ->name('inventory.updateStock');
    Route::get('/inventory/alerts/low-stock', [InventoryController::class, 'lowStockAlerts'])
        ->name('inventory.lowStockAlerts');
    Route::get('/inventory/alerts/critical-stock', [InventoryController::class, 'criticalStockAlerts'])
        ->name('inventory.criticalStockAlerts');
    Route::put('/inventory/bulk-update-stock', [InventoryController::class, 'bulkUpdateStock'])
        ->name('inventory.bulkUpdateStock');
    
    // Gestión de Compras
    Route::resource('purchases', PurchaseController::class);
});
