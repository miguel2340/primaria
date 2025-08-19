<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function mostrar()
    {
        return view('formulario'); // Paso 1: condiciones
    }

public function validar(Request $request)
{
    $request->validate([
        'acepta_datos' => 'required',
        'libre_eleccion' => 'required',
    ]);

    if ($request->acepta_datos === 'NO' || $request->libre_eleccion === 'NO') {
        return back()->withInput()->with('error', 'Debe aceptar ambas condiciones para continuar con el formulario.');
    }

    // Guardar en sesión los valores aceptados
    session([
        'acepta_datos' => $request->acepta_datos,
        'libre_eleccion' => $request->libre_eleccion
    ]);

    // Redirigir ahora a la validación de preguntas
    return redirect()->route('formulario.preguntas');
}
public function abandonado(Request $request)
{
    \Log::info('🔔 Se ejecutó abandonado(). Sesión antes de limpiar:', session()->all());

    session()->forget([
        'formulario',
        'municipio_residencia',
        'departamento_atencion',
        'municipio_atencion',
        'departamento_residencia',
        'acepta_datos',
        'libre_eleccion',
        'documento_validado',
        'documento_validacion',
        'preguntas',
        'respuestas_correctas'
    ]);

    \Log::info('🧹 Sesión después de limpiar:', session()->all());

    return response()->json(['status' => 'ok']);
}

    
}
