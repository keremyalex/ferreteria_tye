<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDetail extends Model
{
    protected $fillable = [
        'cantidad',
        'precio',
        'cantidad_recibida',
        'estado_item',
        'observaciones_recepcion',
        'purchase_id',
        'product_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'cantidad_recibida' => 'integer',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->cantidad * $this->precio;
    }

    // Métodos para manejo de recepción
    public function isPendiente(): bool
    {
        return $this->estado_item === 'pendiente';
    }

    public function isCompleto(): bool
    {
        return $this->estado_item === 'completo';
    }

    public function isParcial(): bool
    {
        return $this->estado_item === 'parcial';
    }

    public function isFaltante(): bool
    {
        return $this->estado_item === 'faltante';
    }

    public function marcarComoRecibido(int $cantidadRecibida, ?string $observaciones = null): void
    {
        $estado = match(true) {
            $cantidadRecibida === 0 => 'faltante',
            $cantidadRecibida === $this->cantidad => 'completo',
            $cantidadRecibida < $this->cantidad => 'parcial',
            default => 'completo'
        };

        $this->update([
            'cantidad_recibida' => $cantidadRecibida,
            'estado_item' => $estado,
            'observaciones_recepcion' => $observaciones,
        ]);
    }

    public function getCantidadPendienteAttribute(): int
    {
        return $this->cantidad - ($this->cantidad_recibida ?? 0);
    }

    public function getSubtotalRecibidoAttribute(): float
    {
        return ($this->cantidad_recibida ?? 0) * $this->precio;
    }
}