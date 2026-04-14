<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'tenant';
    protected $table = 'clientes'; // Especificar el nombre de la tabla si no sigue la convención

    protected $fillable = [
        'codigo',
        'tipo_cliente',
        'tipo_documento',
        'numero_documento',
        'digito_verificacion',
        'razon_social',
        'rep_legal_nombre',
        'rep_legal_documento',
        'primer_apellido',
        'segundo_apellido',
        'primer_nombre',
        'segundo_nombre',
        'email',
        'telefono',
        'celular',
        'direccion',
        'ciudad_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Un cliente pertenece a una ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    // Accessor — nombre completo para mostrar en listados
    public function getNombreCompletoAttribute(): string
    {
        if ($this->razon_social) {
            return $this->razon_social;
        }

        return trim(
            "{$this->primer_nombre} {$this->segundo_nombre} " .
            "{$this->primer_apellido} {$this->segundo_apellido}"
        );
    }

    // Accessor — documento formateado para mostrar
    public function getDocumentoFormateadoAttribute(): string
    {
        if ($this->digito_verificacion) {
            return "{$this->numero_documento}-{$this->digito_verificacion}";
        }
        return $this->numero_documento;
    }
}