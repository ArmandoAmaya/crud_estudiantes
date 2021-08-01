<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carreras extends Model
{
    use HasFactory;

    /**
     * Asociación con la tabla Becas en la bases de datos.
     *
     * @var string
     */
    protected $table = 'carreras';

    /**
     * Primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Datos a llenar.
     *
     * @var array
     */
    protected $fillable = ['name','long_description', 'status'];

    /**
     * Obtiene los estudiantes del curso
     */
    public function estudiantes()
    {
        return $this->hasMany(Estudiantes::class);
    }
}
