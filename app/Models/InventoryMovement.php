<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryMovement extends Model
{
    protected $table = 'inventory_movements';
    
    protected $fillable = [
        'tipo',
        'fecha',
        'referencia',
        'observaciones',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    // Relaciones
    public function details(): HasMany
    {
        return $this->hasMany(InventoryMovementDetail::class, 'movimiento_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }

    public function scopeAjustes($query)
    {
        return $query->where('tipo', 'ajuste');
    }

    public function scopeAplicados($query)
    {
        return $query->where('estado', 'aplicado');
    }

    public function scopePorReferencia($query, $referencia)
    {
        return $query->where('referencia', $referencia);
    }

    // Métodos
    public function isEntrada(): bool
    {
        return $this->tipo === 'entrada';
    }

    public function isSalida(): bool
    {
        return $this->tipo === 'salida';
    }

    public function isAjuste(): bool
    {
        return $this->tipo === 'ajuste';
    }

    public function getMontoTotalAttribute(): float
    {
        return $this->details->sum(function ($detail) {
            return $detail->cantidad * $detail->precio_unitario;
        });
    }

    // Aplicar movimiento al stock
    public function aplicar()
    {
        if ($this->estado !== 'pendiente') {
            return false;
        }

        foreach ($this->details as $detail) {
            $inventory = Inventory::firstOrCreate(
                ['producto_id' => $detail->producto_id],
                ['cantidad_actual' => 0, 'cantidad_minima' => 0]
            );

            if ($this->isEntrada()) {
                $inventory->cantidad_actual += $detail->cantidad;
            } else {
                $inventory->cantidad_actual -= $detail->cantidad;
                
                // No permitir stock negativo
                if ($inventory->cantidad_actual < 0) {
                    throw new \Exception('No hay suficiente stock para la salida del producto: ' . $detail->product->nombre);
                }
            }

            $inventory->save();
        }

        $this->update(['estado' => 'aplicado']);
        return true;
    }

    // Revertir movimiento
    public function revertir()
    {
        if ($this->estado !== 'aplicado') {
            return false;
        }

        foreach ($this->details as $detail) {
            $inventory = Inventory::where('producto_id', $detail->producto_id)->first();
            
            if (!$inventory) {
                continue;
            }

            if ($this->isEntrada()) {
                $inventory->cantidad_actual -= $detail->cantidad;
            } else {
                $inventory->cantidad_actual += $detail->cantidad;
            }

            $inventory->save();
        }

        $this->update(['estado' => 'cancelado']);
        return true;
    }
}
