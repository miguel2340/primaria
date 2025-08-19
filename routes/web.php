<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\DatosUsuarioController;
use App\Http\Controllers\ValidacionPreguntasController;

// Primer paso: validaciones iniciales
Route::get('/formulario', [FormularioController::class, 'mostrar'])->name('formulario.mostrar');
Route::post('/formulario/validar', [FormularioController::class, 'validar'])->name('formulario.validar');


Route::get('/formulario/preguntas', [ValidacionPreguntasController::class, 'mostrarVistaPreguntas'])->name('formulario.preguntas');
Route::post('/formulario/preguntas/generar', [ValidacionPreguntasController::class, 'generarPreguntas'])->name('formulario.generarPreguntas');
Route::post('/formulario/preguntas/validar', [ValidacionPreguntasController::class, 'validarPreguntas'])->name('formulario.validarPreguntas');


// Segundo paso: datos del usuario
Route::get('/formulario/datos', [DatosUsuarioController::class, 'mostrarFormulario'])->name('formulario.datos');
Route::post('/formulario/guardar', [DatosUsuarioController::class, 'guardarDatos'])->name('formulario.guardar');
Route::get('/formulario/ubicacion', [DatosUsuarioController::class, 'ubicacion'])->name('formulario.ubicacion');


Route::get('/formulario/ubicacion', [DatosUsuarioController::class, 'ubicacion'])->name('formulario.ubicacion');
Route::get('/municipios/por-departamento/{id}', [DatosUsuarioController::class, 'municipiosPorDepartamento']);
Route::post('/formulario/prestadores', [DatosUsuarioController::class, 'mostrarPrestadores'])->name('formulario.prestadores');

Route::post('/formulario/finalizar', [DatosUsuarioController::class, 'guardarFormularioCompleto'])->name('formulario.finalizar');
Route::get('/formulario/exito', function () {return view('formulario_exito');})->name('formulario.exito');
Route::post('/formulario/abandonado', [\App\Http\Controllers\FormularioController::class, 'abandonado'])->name('formulario.abandonado');

Route::get('/reiniciar-formulario', function () {
    session()->flush(); // Limpia toda la sesión
    return redirect('/formulario');
})->name('formulario.reiniciar');
Route::get('/ver-env', function () {
    return config('app.key');
});

// Página de inicio (opcional)
Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-sql', function () {
    try {
        \DB::connection()->getPdo();
        return "✅ Conexión exitosa a SQL Server";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});
