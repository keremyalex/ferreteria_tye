<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'fecha',
        'cantidad_actual',
        'cantidad_minima',
        'product_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad_actual' => 'integer',
        'cantidad_minima' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
