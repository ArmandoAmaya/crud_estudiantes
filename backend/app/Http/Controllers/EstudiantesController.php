<?php

namespace App\Http\Controllers;

use App\Models\Estudiantes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EstudiantesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Estudiantes::get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:estudiantes',
            'carrera_id' => 'required',
            'status' => 'required|in:0,1',
        ],[
            'required' => 'Este campo es requerido.',
            'email' => 'Este correo no tiene un formato válido.',
            'carrera_id' => 'Curso inválido.',
            'unique' => 'Este email ya está registrado.',
            'status.in' => 'Estado inválido.',
        ]);

        # En caso de haber errores
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ]);
        }

        # Guardar datos
        (new Estudiantes)->create($request->all());

        # Devolver respuesta
        return response()->json([
            'success' => true,
            'message' => 'Guardado correctamente.'
        ]);
    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Estudiantes  $estudiantes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Estudiantes $estudiante)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('estudiantes')->ignore($estudiante->id,'id')
            ],
            'carrera_id' => 'required',
            'status' => 'required|in:0,1',
        ],[
            'required' => 'Este campo es requerido.',
            'email' => 'Este correo no tiene un formato válido.',
            'carrera_id' => 'Curso inválido.',
            'unique' => 'Este email ya está registrado.',
            'status.in' => 'Estado inválido.',
        ]);

        

        # En caso de haber errores
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ]);
        }

        # Guardar datos
        $estudiante->update($request->all());

        # Devolver respuesta
        return response()->json([
            'success' => true,
            'message' => 'Guardado correctamente.'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Estudiantes  $estudiantes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estudiantes $estudiante)
    {
        $estudiante->delete();
    }
}
