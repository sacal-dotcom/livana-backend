<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    use HasFactory;

    protected $table = 'inmuebles';
    protected $primaryKey = 'id_inmueble';
    public $timestamps = false; // Ajusta a true si tu tabla usa created_at/updated_at

    protected $fillable = [
        'arrendador_id',
        'nombre',
        'tipo',
        'direccion',
        'ciudad',
        'barrio',
        'estrato',
        'imagen_url',
    ];
}