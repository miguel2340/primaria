<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatosUsuarioController extends Controller
{
public function mostrarFormulario(Request $request)
{
    if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
        return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
    }

    if (!session('documento_validado')) {
        return redirect()->route('formulario.preguntas')->with('error', 'Debe validar su identidad antes de continuar.');
    }
    $documento = session('documento_validacion');

    $afiliado = DB::table('afiliado')
        ->where('numero_documento', $documento)
        ->first();

    if (!$afiliado) {
        return redirect()->route('formulario.preguntas')->with('error', 'No se encontró información del afiliado.');
    }

    $datos = [
        'documento' => $afiliado->numero_documento,
        'nombre_completo' => strtoupper(trim($afiliado->primer_nombre . ' ' . $afiliado->segundo_nombre . ' ' . $afiliado->primer_apellido . ' ' . $afiliado->segundo_apellido)),
    ];

    return view('formulario_datos', compact('datos')); // Pasamos los datos precargados
}



    public function guardarDatos(Request $request)
    {

        if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
        return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
    }
        $request->validate([
            'nombre_completo' => 'required|string|max:150',
            'documento' => 'required|numeric',
            'tipo_afiliado' => 'required',
            'celular' => 'required|numeric',
            'correo' => 'required|email',
        ]);

        $request->session()->put('formulario', $request->only([
            'nombre_completo', 'documento', 'tipo_afiliado', 'celular', 'correo',
            'localidad', 'direccion_residencia'
        ]));

        return redirect()->route('formulario.ubicacion'); // Paso 3
    }

    public function ubicacion()
    {
            if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
        return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
    }

    if (!session()->has('formulario')) {
        return redirect()->route('formulario.datos')->with('error', 'Debe registrar sus datos personales antes de continuar.');
    }

       if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
        return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
    }
        $municipios = DB::table('municipios')->select('id_municipio', 'municipio')->orderBy('municipio')->get();
        $departamentos = DB::table('departamentos')->select('id_departamento', 'departamento')->orderBy('departamento')->get();

        return view('formulario_ubicacion', compact('municipios', 'departamentos'));
    }

    public function municipiosPorDepartamento($id)
    {
        $municipios = DB::table('municipios')
            ->where('departamento_id', $id)
            ->orderBy('municipio')
            ->pluck('municipio', 'id_municipio');

        return response()->json($municipios);
    }

public function mostrarPrestadores(Request $request)
{

    if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
    return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
}
    // Casteo manual a int para asegurar que Laravel no falle por tipo
    $request->merge([
        'municipio_atencion' => (int) $request->municipio_atencion,
    ]);

    $request->validate([
        'municipio_atencion' => ['required', 'exists:municipios,id_municipio'],
    ], [
        'municipio_atencion.required' => 'Debe seleccionar un municipio de atención.',
        'municipio_atencion.exists' => 'El municipio de atención no es válido.',
    ]);

    session([
        'departamento_residencia' => $request->departamento_residencia,
        'municipio_residencia' => $request->municipio_residencia,
        'departamento_atencion' => $request->departamento_atencion,
        'municipio_atencion' => $request->municipio_atencion,
    ]);


    $municipio = DB::table('municipios')
        ->where('id_municipio', $request->municipio_atencion)
        ->value('municipio');

    $prestadores = DB::table('prestadores_libre_eleccion')
        ->where('id_municipio', $request->municipio_atencion)
        ->orderBy('nombre_prestador')
        ->get();

    return view('formulario_prestadores', compact('prestadores', 'municipio'));
}



public function guardarFormularioCompleto(Request $request)
{


      if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
        return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
    }

    $request->validate([
        'prestador' => 'required',
        'asignar_grupo_familiar' => 'required'
    ]);

    $datos = session('formulario', []);
    $datos['acepta_datos'] = session('acepta_datos');
    $datos['libre_eleccion'] = session('libre_eleccion');

    $datos['departamento_residencia'] = session('departamento_residencia');
    $datos['municipio_residencia'] = session('municipio_residencia');
    $datos['departamento_atencion'] = session('departamento_atencion');
    $datos['municipio_atencion'] = session('municipio_atencion');
    $datos['prestador_nit'] = $request->prestador;
    $datos['asignar_grupo_familiar'] = $request->asignar_grupo_familiar;
    $datos['fecha_registro'] = now();

    DB::table('formulario_primaria')->insert($datos);

    session()->forget([
        'formulario',
        'municipio_residencia',
        'departamento_atencion',
        'municipio_atencion',
        'departamento_residencia',
        'acepta_datos',
        'libre_eleccion'
    ]);

       return redirect()->route('formulario.exito');
}

}
