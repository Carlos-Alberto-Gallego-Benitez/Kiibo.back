<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    // Campos que se pueden llenar de manera masiva
    protected $fillable = ['id_usuario', 'titulo', 'descripcion', 'fecha_creacion', 'fecha_limite', 'estado'];



    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
