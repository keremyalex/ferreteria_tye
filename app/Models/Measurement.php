<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Measurement extends Model
{
    protected $fillable = [
        'nombre',
        'simbolo',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
