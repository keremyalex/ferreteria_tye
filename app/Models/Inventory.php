<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = [
        'producto_id',
        'cantidad_actual',
        'cantidad_minima',
        'cantidad_maxima',
        'precio_venta',
    ];

    protected $casts = [
        'cantidad_actual' => 'decimal:2',
        'cantidad_minima' => 'decimal:2',
        'cantidad_maxima' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'producto_id');
    }

    public function movementDetails(): HasMany
    {
        return $this->hasMany(InventoryMovementDetail::class, 'producto_id', 'producto_id');
    }

    // Scopes
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('cantidad_actual', '<=', 'cantidad_minima');
    }

    public function scopeSinStock($query)
    {
        return $query->where('cantidad_actual', 0);
    }

    public function scopeConStock($query)
    {
        return $query->where('cantidad_actual', '>', 0);
    }

    public function scopeStockCritico($query)
    {
        return $query->whereRaw('cantidad_actual <= (cantidad_minima / 2)');
    }

    // Verificaciones de stock
    public function isStockBajo(): bool
    {
        return $this->cantidad_actual <= $this->cantidad_minima;
    }

    public function isSinStock(): bool
    {
        return $this->cantidad_actual <= 0;
    }

    public function isStockMaximo(): bool
    {
        return $this->cantidad_maxima && $this->cantidad_actual >= $this->cantidad_maxima;
    }

    // Precio efectivo
    public function getPrecioVentaEfectivoAttribute(): float
    {
        // Si tiene precio específico, lo usa; sino el precio base del producto
        return $this->precio_venta ?? $this->product->precio_venta ?? 0;
    }

    public function getValorTotalStockAttribute(): float
    {
        return $this->cantidad_actual * $this->precio_venta_efectivo;
    }

    // Operaciones de stock
    public function sumarStock(float $cantidad): bool
    {
        $this->cantidad_actual += $cantidad;
        return $this->save();
    }

    public function restarStock(float $cantidad): bool
    {
        if ($this->cantidad_actual < $cantidad) {
            return false; // No hay suficiente stock
        }
        
        $this->cantidad_actual -= $cantidad;
        return $this->save();
    }

    public function ajustarStock(float $nuevaCantidad): bool
    {
        $this->cantidad_actual = $nuevaCantidad;
        return $this->save();
    }
}
