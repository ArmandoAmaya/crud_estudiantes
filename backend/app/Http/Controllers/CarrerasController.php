<?php

namespace App\Http\Controllers;

use App\Models\Carreras;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CarrerasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Carreras::get();
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
            'name'   => 'required|unique:carreras',
            'long_description' => 'required',
            'status' => 'required|in:0,1',
        ],[
            'required' => 'Este campo es requerido.',
            'unique' => 'Este curso ya está registrado.',
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
        (new Carreras)->create($request->all());

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
     * @param  \App\Models\Carreras  $carreras
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Carreras $carrera)
    {

        
        $validator = Validator::make($request->all(), [
            'name'   => [
                'required',
                Rule::unique('carreras')->ignore($carrera->id,'id')
            ],
            'long_description' => 'required',
            'status' => 'required|in:0,1',
        ],[
            'required' => 'Este campo es requerido.',
            'unique' => 'Este curso ya está registrado.',
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
        $carrera->update([
            'name' => $request->name,
            'long_description' => $request->long_description,
            'status' => $request->status
        ]);

        # Devolver respuesta
        return response()->json([
            'success' => true,
            'message' => 'Guardado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carreras  $carreras
     * @return \Illuminate\Http\Response
     */
    public function destroy(Carreras $carrera)
    {
        $carrera->delete();
    }
}
