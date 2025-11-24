<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'nit',
        'nombre_empresa',
        'nombre_persona',
        'direccion',
        'telefono',
        'correo',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
