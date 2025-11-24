<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function inventoryDetails(): HasMany
    {
        return $this->hasMany(InventoryDetail::class);
    }
}
