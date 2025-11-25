<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'precio_venta',
        'category_id',
        'measurement_id',
        'supplier_id',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function measurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'producto_id');
    }

    public function inventoryMovementDetails(): HasMany
    {
        return $this->hasMany(InventoryMovementDetail::class);
    }

    // Accessors para precios
    public function getPrecioVentaActualAttribute()
    {
        // Usar el precio de venta específico del inventario si existe, sino el precio base
        return $this->inventory?->precio_venta ?? $this->precio_venta;
    }

    // Relación con purchase_details
    public function purchaseDetails()
    {
        return $this->hasMany(\App\Models\PurchaseDetail::class);
    }

    // Método para actualizar precio con nueva compra
    public function actualizarPrecioConCompra($precioCompra)
    {
        // Actualizar precio de venta si es necesario
        // Esta lógica puede ajustarse según las necesidades del negocio
        return $this;
    }

    // Métodos para trabajar con inventario
    public function getCantidadActual()
    {
        return $this->inventory?->cantidad_actual ?? 0;
    }

    public function getValorStock()
    {
        return $this->inventory?->valor_total_stock ?? 0;
    }

    public function isStockBajo(): bool
    {
        return $this->inventory?->isStockBajo() ?? false;
    }

    public function isSinStock(): bool
    {
        return $this->inventory?->isSinStock() ?? true;
    }

    public function getPrecioVentaEfectivo()
    {
        return $this->inventory?->precio_venta_efectivo ?? $this->precio_venta_actual;
    }
}
