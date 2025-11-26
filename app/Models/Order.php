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
        'estado_pago',
        'usuario_id',
        'cliente_id',
        'vendedor_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'direccion_facturacion' => 'array',
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
