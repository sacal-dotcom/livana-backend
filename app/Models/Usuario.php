<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario'; // Imprescindible para conectar con tu SQL

    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];
}