<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'nro',
        'fecha',
        'hora',
        'observaciones',
        'monto_total',
        'estado',
        'fecha_recepcion',
        'supplier_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i',
        'monto_total' => 'decimal:2',
        'fecha_recepcion' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseDetails(): HasMany
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('nro', 'like', '%' . $term . '%')
                    ->orWhere('observaciones', 'like', '%' . $term . '%')
                    ->orWhereHas('supplier', function ($q) use ($term) {
                        $q->where('nombre_empresa', 'like', '%' . $term . '%');
                    });
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('fecha', [$start, $end]);
    }

    public function generateNumber()
    {
        $lastPurchase = static::orderBy('id', 'desc')->first();
        $number = $lastPurchase ? $lastPurchase->id + 1 : 1;
        return 'COMP-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Métodos para manejo de estados
    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isRecibida(): bool
    {
        return $this->estado === 'recibida';
    }

    public function isParcial(): bool
    {
        return $this->estado === 'parcial';
    }

    public function isCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }

    public function marcarComoRecibida(): void
    {
        $this->update([
            'estado' => 'recibida',
            'fecha_recepcion' => now(),
        ]);
    }

    public function marcarComoParcial(): void
    {
        $this->update([
            'estado' => 'parcial',
            'fecha_recepcion' => now(),
        ]);
    }

    public function cancelar(): void
    {
        $this->update(['estado' => 'cancelada']);
    }

    // Métodos para cálculos de recepción
    public function getTotalItemsPendientes(): int
    {
        return $this->purchaseDetails()->where('estado_item', 'pendiente')->count();
    }

    public function getTotalItemsRecibidos(): int
    {
        return $this->purchaseDetails()->whereIn('estado_item', ['completo', 'parcial'])->count();
    }

    public function actualizarEstadoBasadoEnItems(): void
    {
        $totalItems = $this->purchaseDetails()->count();
        $itemsCompletos = $this->purchaseDetails()->where('estado_item', 'completo')->count();
        $itemsParciales = $this->purchaseDetails()->where('estado_item', 'parcial')->count();
        
        if ($itemsCompletos === $totalItems) {
            $this->marcarComoRecibida();
        } elseif ($itemsCompletos > 0 || $itemsParciales > 0) {
            $this->marcarComoParcial();
        }
    }
}