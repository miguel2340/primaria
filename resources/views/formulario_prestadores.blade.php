<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selección de Prestadores</title>
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


    .radio-group {
        margin-top: 10px;
    }

    .radio-group label {
        font-weight: normal;
        font-size: 14px; /* o 13px, según lo que necesites */
        margin-right: 15px;
    }

    input[type="radio"] + label,
    .prestador label {
        font-weight: normal;
        font-size: 14px;
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
.prestador-radio {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.prestador-radio input[type="radio"] {
    margin-right: 8px;
}

.prestador-radio label {
    font-weight: normal;
    font-size: 14px;
    margin: 0;
}

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }
.radio-group .pregunta-principal {
    font-size: 17px;
    font-weight: bold;
    margin-bottom: 10px;
    display: block;
}
.radio-group {
    margin-top: 30px; /* Aumentado para separar de las opciones anteriores */
}
.prestador-radio:last-child {
    margin-bottom: 20px;
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
    <h2>Seleccione el Prestador para el Municipio: <strong>{{ strtoupper($municipio) }}</strong></h2>

    <form method="POST" action="{{ route('formulario.finalizar') }}">

        @csrf

@forelse($prestadores as $p)
    <div class="prestador-radio">
        <input type="radio" id="p{{ $loop->index }}" name="prestador" value="{{ $p->nit }}" required>
        <label for="p{{ $loop->index }}">{{ $p->nombre_prestador }}</label>
    </div>
@empty
    <p>No se presento a la convocatoria ninguna IPS para atención primaria en salud, capa 1</p>
@endforelse



<div class="radio-group">
    <span class="pregunta-principal">¿Desea que la IPS seleccionada se asigne a todo su grupo familiar? <span style="color:red;">*</span></span>
    <label>
        <input type="radio" name="asignar_grupo_familiar" value="SI" required> SI
    </label>
    <label>
        <input type="radio" name="asignar_grupo_familiar" value="NO" required> NO
    </label>
</div>


        <button type="submit">Finalizar</button>
    </form>
</div>
</body>
</html>
<script>
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
