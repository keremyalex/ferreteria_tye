<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'numero_orden',
        'tipo',
        'estado',
        'subtotal',
        'total',
        'direccion_facturacion',
        'observaciones',
        'metodo_pago',
        'tipo_pago',
        'primer_cuota',
        'segunda_cuota',
        'primer_cuota_pagada',
        'segunda_cuota_pagada',
        'fecha_vencimiento_segunda_cuota',
        'fecha_pago_primer_cuota',
        'fecha_pago_segunda_cuota',
        'estado_pago',
        'usuario_id',
        'cliente_id',
        'vendedor_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'primer_cuota' => 'decimal:2',
        'segunda_cuota' => 'decimal:2',
        'primer_cuota_pagada' => 'boolean',
        'segunda_cuota_pagada' => 'boolean',
        'direccion_facturacion' => 'array',
        'fecha_vencimiento_segunda_cuota' => 'date',
        'fecha_pago_primer_cuota' => 'datetime',
        'fecha_pago_segunda_cuota' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'orden_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function qrTransaction(): HasOne
    {
        return $this->hasOne(QrTransaction::class);
    }

    // public function inventoryMovements(): HasMany
    // {
    //     return $this->hasMany(InventoryMovement::class);
    // }
    // Nota: La asociación con movimientos de inventario se hace a través del campo 'referencia'

    // Scopes para filtrar por tipo
    public function scopeOnline($query)
    {
        return $query->where('tipo', 'online');
    }

    public function scopePresencial($query)
    {
        return $query->where('tipo', 'presencial');
    }

    // Helper methods
    public function isOnline(): bool
    {
        return $this->tipo === 'online';
    }

    public function isPresencial(): bool
    {
        return $this->tipo === 'presencial';
    }

    public function getCustomerName(): string
    {
        if ($this->isOnline()) {
            return $this->user?->name ?? 'Cliente no identificado';
        }
        
        return $this->client?->nombre ?? 'Cliente no identificado';
    }

    // Métodos para manejo de crédito
    public function isCredito(): bool
    {
        return $this->tipo_pago === 'credito';
    }

    public function isPagadoCompleto(): bool
    {
        if ($this->tipo_pago === 'contado') {
            return $this->estado_pago === 'pagado';
        }
        
        return $this->primer_cuota_pagada && $this->segunda_cuota_pagada;
    }

    public function isSegundaCuotaVencida(): bool
    {
        return $this->isCredito() && 
               !$this->segunda_cuota_pagada && 
               $this->fecha_vencimiento_segunda_cuota && 
               $this->fecha_vencimiento_segunda_cuota < now();
    }

    public function getSaldoPendiente(): float
    {
        if ($this->tipo_pago === 'contado') {
            return $this->estado_pago === 'pagado' ? 0 : $this->total;
        }

        $pendiente = 0;
        if (!$this->primer_cuota_pagada) $pendiente += $this->primer_cuota;
        if (!$this->segunda_cuota_pagada) $pendiente += $this->segunda_cuota;
        return $pendiente;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->numero_orden) {
                $order->numero_orden = 'ORD-' . date('Y') . '-' . str_pad(static::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
