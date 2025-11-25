<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovementDetail extends Model
{
    protected $table = 'inventory_movement_details';
    
    protected $fillable = [
        'cantidad',
        'precio_unitario',
        'lote',
        'observaciones',
        'movimiento_id',
        'producto_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    // Relaciones
    public function movement(): BelongsTo
    {
        return $this->belongsTo(InventoryMovement::class, 'movimiento_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    // Accessors
    public function getSubtotalAttribute(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }
}