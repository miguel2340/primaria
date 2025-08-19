<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidacionPreguntasController extends Controller
{
    public function mostrarVistaPreguntas(Request $request)
    {
        if (!session()->has('acepta_datos') || !session()->has('libre_eleccion')) {
            return redirect()->route('formulario.mostrar')->with('error', 'Debe aceptar los términos antes de continuar.');
        }

        // 🔒 Revalidar que la cédula no haya sido registrada mientras se navegaba
        $documentoSesion = session('documento_validacion');
        if ($documentoSesion && DB::table('formulario_primaria')->where('documento', $documentoSesion)->exists()) {
            session()->forget(['preguntas', 'respuestas_correctas', 'documento_validacion', 'intentos']);
            return redirect()->route('formulario.mostrar')->with('error', 'Este documento ya fue registrado. No es necesario volver a diligenciar el formulario.');
        }

        $preguntas = session('preguntas');
        return view('formulario_preguntas', ['preguntas' => $preguntas]);
    }

    public function generarPreguntas(Request $request)
    {
        $documento = $request->input('documento');

        // 1. Verifica si existe
        $afiliado = DB::table('afiliado')->where('numero_documento', $documento)->first();
        if (!$afiliado) {
            return redirect()->back()->with('error', 'No se encontró ningún afiliado con ese documento.');
        }

        // 2. Está bloqueado
        if (DB::table('bloqueados')->where('documento', $documento)->exists()) {
            return redirect()->back()->with('error', 'Este documento está bloqueado. Escriba a correcciondatos@fomag.gov.co');
        }

        // 3. Ya diligenciado (primer filtro)
        if (DB::table('formulario_primaria')->where('documento', $documento)->exists()) {
            return redirect()->back()->with('error', 'Este documento ya fue registrado. No es necesario volver a diligenciar el formulario.');
        }

        // --- Flujo especial: pregunta única de fecha de expedición ---
        if (!empty($afiliado->fecha_expedicion)) {
            $preguntas = [];
            $respuestasCorrectas = [];

            $preguntas[] = [
                'campo' => 'fecha_expedicion',
                'texto' => '¿Cuál es la fecha de expedición de su documento?',
                'tipo' => 'date', // importante para la vista
            ];
            $respuestasCorrectas['fecha_expedicion'] = date('Y-m-d', strtotime($afiliado->fecha_expedicion));

            session([
                'preguntas' => $preguntas,
                'respuestas_correctas' => $respuestasCorrectas,
                'documento_validacion' => $documento,
                'intentos' => 3, // reset explícito
            ]);

            // 🔒 Revalidar justo antes de mostrar la vista (por si se creó el registro entre el primer check y aquí)
            if (DB::table('formulario_primaria')->where('documento', $documento)->exists()) {
                session()->forget(['preguntas', 'respuestas_correctas', 'documento_validacion', 'intentos']);
                return redirect()->back()->with('error', 'Este documento ya fue registrado. No es necesario volver a diligenciar el formulario.');
            }

            return view('formulario_preguntas', ['preguntas' => $preguntas]);
        }

        // --- Flujo de preguntas con opciones ---
        $campos = [
            'IPS_Primaria' => '¿Cuál es su IPS primaria?',
            'correo_principal' => '¿Cuál es su correo principal?',
            'celular_principal' => '¿Cuál es su celular principal?',
            'municipio_atencion' => '¿Cuál es su municipio de atención?',
            'numero_documento_cotizante' => '¿Cuál es el documento del cotizante?'
        ];

        $preguntas = [];
        $respuestasCorrectas = [];

        foreach ($campos as $campo => $texto) {
            $respuestaReal = $afiliado->$campo ?? null;

            // Si la respuesta real está vacía, se considera "Ninguna de las anteriores"
            $valorCorrecto = (is_null($respuestaReal) || $respuestaReal === '' || $respuestaReal === '0')
                ? 'Ninguna de las anteriores'
                : $respuestaReal;

            $opciones = $this->generarOpciones($valorCorrecto, $campo);

            $preguntas[] = [
                'campo' => $campo,
                'texto' => $texto,
                'opciones' => $opciones
            ];

            $respuestasCorrectas[$campo] = $valorCorrecto;
        }

        session([
            'preguntas' => $preguntas,
            'respuestas_correctas' => $respuestasCorrectas,
            'documento_validacion' => $documento,
            'intentos' => 3, // reset explícito
        ]);

        // 🔒 Revalidar también aquí por seguridad
        if (DB::table('formulario_primaria')->where('documento', $documento)->exists()) {
            session()->forget(['preguntas', 'respuestas_correctas', 'documento_validacion', 'intentos']);
            return redirect()->back()->with('error', 'Este documento ya fue registrado. No es necesario volver a diligenciar el formulario.');
        }

        return view('formulario_preguntas', ['preguntas' => $preguntas]);
    }

    private function generarOpciones($valorCorrecto, $campo)
    {
        $falsas = [];

        // Genera 2 opciones falsas distintas
        while (count($falsas) < 2) {
            $falsa = $this->generarDatoFalso($campo);

            if ($falsa !== $valorCorrecto && !in_array($falsa, $falsas)) {
                $falsas[] = $falsa;
            }
        }

        $opciones = [$valorCorrecto, $falsas[0], $falsas[1]];

        // Si el valor correcto no es "Ninguna", agregarla también
        if ($valorCorrecto !== 'Ninguna de las anteriores') {
            $opciones[] = 'Ninguna de las anteriores';
        }

        shuffle($opciones);
        return $opciones;
    }

    private function generarDatoFalso($campo)
    {
        switch ($campo) {
            case 'correo_principal':
                $nombres = ['juan', 'carlos', 'laura', 'maria', 'andres', 'camilo', 'sandra', 'diana', 'luis', 'ana'];
                $apellidos = ['perez', 'gomez', 'rodriguez', 'martinez', 'garcia', 'fernandez', 'lopez', 'ramirez', 'torres', 'moreno'];
                $dominios = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'epssalud.co'];
                $nombre = $nombres[array_rand($nombres)];
                $apellido = $apellidos[array_rand($apellidos)];
                $numero = rand(10, 999);
                $dominio = $dominios[array_rand($dominios)];
                $correo = $nombre . $apellido . $numero . '@' . $dominio;
                return strtoupper($correo);

            case 'celular_principal':
                return '3' . rand(100000000, 999999999);

            case 'municipio_atencion':
                $municipios = ['Arauca', 'Saravena', 'Tame', 'Cravo Norte', 'Puerto Rondón', 'Fortul'];
                return strtoupper($municipios[array_rand($municipios)]);

            case 'IPS_Primaria':
                $ips = ['Centro Médico Arauca', 'Hospital de Saravena', 'Nueva Salud EPS', 'Salud y Vida IPS', 'Coosalud EPS'];
                return strtoupper($ips[array_rand($ips)]);

            case 'numero_documento_cotizante':
                return (string) rand(10000000, 99999999);

            default:
                return 'VALOR FALSO ' . rand(1, 999);
        }
    }

    public function validarPreguntas(Request $request)
    {
        $respuestas = $request->input('respuestas', []);
        $correctas = session('respuestas_correctas', []);
        $documento = session('documento_validacion');

        if (!$documento) {
            return redirect()->route('formulario.mostrar')->with('error', 'La sesión de validación expiró. Inicie nuevamente.');
        }

        // 🔒 Revalidar ANTES de evaluar respuestas
        if (DB::table('formulario_primaria')->where('documento', $documento)->exists()) {
            session()->forget(['preguntas', 'respuestas_correctas', 'documento_validacion', 'intentos']);
            return redirect()->route('formulario.mostrar')->with('error', 'Este documento ya fue registrado. No es necesario volver a diligenciar el formulario.');
        }

        $aciertos = 0;
        foreach ($correctas as $campo => $valorCorrecto) {
            $respuestaUsuario = $respuestas[$campo] ?? null;

            if ($campo === 'fecha_expedicion') {
                $respuestaUsuario = $respuestaUsuario ? date('Y-m-d', strtotime($respuestaUsuario)) : null;
                $valorCorrecto = $valorCorrecto ? date('Y-m-d', strtotime($valorCorrecto)) : null;
            } else {
                $valorCorrecto = (is_null($valorCorrecto) || $valorCorrecto === '' || $valorCorrecto === '0' || $valorCorrecto === 0)
                    ? 'Ninguna de las anteriores'
                    : (string) $valorCorrecto;
            }

            if ((string) $respuestaUsuario === (string) $valorCorrecto) {
                $aciertos++;
            }
        }

        if ($aciertos === count($correctas)) {
            // 🔒 Revalidación final por carrera entre validación y guardado
            if (DB::table('formulario_primaria')->where('documento', $documento)->exists()) {
                session()->forget(['preguntas', 'respuestas_correctas', 'documento_validacion', 'intentos']);
                return redirect()->route('formulario.mostrar')->with('error', 'Este documento ya fue registrado. No es necesario volver a diligenciar el formulario.');
            }

            session(['documento_validado' => true]);
            return redirect()->route('formulario.datos');
        } else {
            $intentos = (int) session('intentos', 3) - 1;
            session(['intentos' => $intentos]);

            if ($intentos <= 0) {
                DB::table('bloqueados')->insert([
                    'documento' => $documento,
                    'fecha_bloqueo' => now()
                ]);
                session()->forget(['preguntas', 'respuestas_correctas', 'documento_validacion', 'intentos']);
                return redirect()->route('formulario.preguntas')->with('error', 'Documento bloqueado por intentos fallidos. Escriba al correo correcciondatos@fomag.gov.co');
            }

            session()->forget(['preguntas', 'respuestas_correctas']);
            return redirect()->route('formulario.preguntas')->with('error', "Respuestas incorrectas. Intentos restantes: $intentos");
        }
    }
}
