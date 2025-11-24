<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDetail extends Model
{
    protected $fillable = [
        'cantidad',
        'cantidad_minima',
        'precio_venta',
        'inventory_id',
        'product_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'cantidad_minima' => 'integer',
        'precio_venta' => 'decimal:2',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('cantidad', '<=', 'cantidad_minima');
    }

    public function isLowStock(): bool
    {
        return $this->cantidad <= $this->cantidad_minima;
    }
}