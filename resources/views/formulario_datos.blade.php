<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos Personales del Usuario</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        padding: 30px;
    }

    .container {
        max-width: 700px;
        margin: auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        background: linear-gradient(to right, #009fe3, #773266);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 22px;
        font-weight: bold;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }

    input[type="text"],
    input[type="number"],
    input[type="email"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .radio-group {
        margin-top: 10px;
    }

    .radio-group label {
        font-weight: normal;
        margin-right: 15px;
    }

    .success {
        color: green;
        margin-bottom: 20px;
    }

    button {
        margin-top: 25px;
        background-color:rgb(4, 115, 167);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: opacity 0.3s ease;
    }

    button:hover {
        opacity: 0.9;
    }

      .progreso {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        padding: 15px;
        background: #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
    }

    .paso {
        flex: 1;
        text-align: center;
        position: relative;
        padding-bottom: 10px;
        color: #777;
    }

    .paso::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 10%;
        width: 80%;
        height: 4px;
        background: #ccc;
        border-radius: 2px;
    }

    .paso.completado {
        color: #009fe3;
    }

    .paso.completado::after {
        background: linear-gradient(to right, #009fe3, #773266);
    }

    .paso.activo {
        color: #773266;
    }

    .paso.activo::after {
        background: linear-gradient(to right, #009fe3, #773266);
        height: 6px;
    }
    input.uppercase {
    text-transform: uppercase;
}
</style>

</head>
<body>
<div class="container">
<div class="progreso">
    <div class="paso {{ request()->routeIs('formulario.mostrar') ? 'activo' : 'completado' }}">1. Condiciones</div>
    <div class="paso {{ request()->routeIs('formulario.datos') ? 'activo' : (session()->has('formulario') ? 'completado' : '') }}">2. Datos</div>
    <div class="paso {{ request()->routeIs('formulario.ubicacion') ? 'activo' : (session()->has('municipio_atencion') ? 'completado' : '') }}">3. Ubicación</div>
    <div class="paso {{ request()->routeIs('formulario.prestadores') ? 'activo' : '' }}">4. Prestador</div>
</div>
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="{{ asset('images/logo_fomag2.jpg') }}" alt="Logo FOMAG" style="max-width: 700px;">
    </div>
    <h2>Datos Personales del Usuario</h2>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

<form method="POST" action="{{ route('formulario.guardar') }}">
    @csrf

    <label>Nombres y Apellidos Completos del Usuario <small>(Mayúscula y sin tildes)</small></label>
    <input type="text" name="nombre_completo"
           value="{{ old('nombre_completo', $datos['nombre_completo'] ?? '') }}"
           class="uppercase" required>

    <label>N° de Documento de Identidad <small>(Sin puntos)</small></label>
    <input type="number" name="documento"
           value="{{ old('documento', $datos['documento'] ?? '') }}"
           {{ isset($datos['documento']) ? 'readonly' : '' }} required>

    <label>Tipo de afiliado</label>
    <div class="radio-group">
        @foreach (['Cotizante','Beneficiario','Sustituto','Cotizante Pensionado','Cotizante Dependiente'] as $tipo)
            <label>
                <input type="radio" name="tipo_afiliado" value="{{ $tipo }}"
                    {{ old('tipo_afiliado', $datos['tipo_afiliado'] ?? '') == $tipo ? 'checked' : '' }} required>
                {{ $tipo }}
            </label>
        @endforeach
    </div>

    <label>Número de Celular</label>
    <input type="number" name="celular"
           value="{{ old('celular', $datos['celular'] ?? '') }}" required>

    <label>Correo electrónico</label>
    <input type="email" name="correo"
           value="{{ old('correo', $datos['correo'] ?? '') }}" required>

    <label>Localidad / Comuna / Barrio</label>
    <input type="text" name="localidad"
           value="{{ old('localidad', $datos['localidad'] ?? '') }}" class="uppercase">

    <button type="submit">Continuar</button>
</form>

</div>
</body>
</html>
<script>
    document.querySelector('input[name="nombre_completo"]').addEventListener('input', function (e) {
        e.target.value = e.target.value.toUpperCase();
    });

        history.pushState(null, null, location.href);
    window.onpopstate = function () {
        window.location.href = "{{ route('formulario.mostrar') }}";
    };
    let salidaControlada = false;

// Marcar cuando se hace clic en un botón de continuar
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        salidaControlada = true;
    });
});

// Detectar cierre, recarga o navegación fuera
window.addEventListener('beforeunload', function (e) {
    if (!salidaControlada) {
        navigator.sendBeacon("{{ route('formulario.abandonado') }}");
    }
});
</script>



