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
        'supplier_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime:H:i',
        'monto_total' => 'decimal:2',
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
}