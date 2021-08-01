<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiantes extends Model
{
    use HasFactory;

    /**
     * Datos a llenar.
     *
     * @var array
     */
    protected $fillable = ['name','last_name', 'email', 'carrera_id', 'status'];

    /**
     * Obtiene la carrera del estudiante
     */
    public function carrera()
    {
        return $this->belongsTo(Carreras::class);
    }
}
