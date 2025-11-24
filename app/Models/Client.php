<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'nit',
        'ci',
        'telf',
    ];

    /**
     * Los atributos que deben ser castados.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con las ventas (cuando se implemente)
     */
    // public function sales()
    // {
    //     return $this->hasMany(Sale::class, 'client_id');
    // }

    /**
     * Scope para búsqueda por nombre
     */
    public function scopeByName($query, $name)
    {
        return $query->where('nombre', 'ILIKE', "%{$name}%");
    }

    /**
     * Scope para búsqueda por NIT
     */
    public function scopeByNit($query, $nit)
    {
        return $query->where('nit', 'ILIKE', "%{$nit}%");
    }

    /**
     * Scope para búsqueda por CI
     */
    public function scopeByCi($query, $ci)
    {
        return $query->where('ci', 'ILIKE', "%{$ci}%");
    }

    /**
     * Scope para búsqueda por teléfono
     */
    public function scopeByPhone($query, $phone)
    {
        return $query->where('telf', 'ILIKE', "%{$phone}%");
    }

    /**
     * Accessor para mostrar información completa del cliente
     */
    public function getFullInfoAttribute()
    {
        $info = $this->nombre;
        
        if ($this->ci) {
            $info .= ' - CI: ' . $this->ci;
        }
        
        if ($this->nit) {
            $info .= ' - NIT: ' . $this->nit;
        }
        
        if ($this->telf) {
            $info .= ' - Tel: ' . $this->telf;
        }
        
        return $info;
    }

    /**
     * Accessor para obtener el documento principal (CI o NIT)
     */
    public function getMainDocumentAttribute()
    {
        return $this->ci ?: $this->nit;
    }

    /**
     * Accessor para el tipo de documento principal
     */
    public function getMainDocumentTypeAttribute()
    {
        return $this->ci ? 'CI' : ($this->nit ? 'NIT' : null);
    }
}