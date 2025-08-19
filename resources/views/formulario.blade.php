<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Elección de Prestadores - Arauca</title>
    <style>
        :root {
           --primario: linear-gradient(to right, #009fe3, #773266);
            --fondo: #f4f4f9;
            --texto: #222;
            --borde: #ccc;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--fondo);
            color: var(--texto);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 760px;
            margin: 50px auto;
            background: #fff;
            border: 1px solid var(--borde);
            border-radius: 10px;
            padding: 30px 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

h2 {
    background: linear-gradient(to right, #009fe3, #773266);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}


        p {
            line-height: 1.6;

        }

        .pregunta {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }


        label {
            display: block;
            margin: 8px 0;
        }

        input[type="radio"] {
            margin-right: 6px;
        }

        .error {
            background-color: #ffe0e0;
            color: #b30000;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ffb0b0;
        }

button {
    background-color:rgb(4, 115, 167);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 25px;
    transition: background 0.3s ease;
}

button:hover {
    opacity: 0.9;
}

        a {
            color: #0073e6;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
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
        <img src="{{ asset('images/logo_fomag.JPG') }}" alt="Logo FOMAG" style="max-width: 700px;">
    </div>
    <h2>Presentación y elección de posibles prestadores de Atención Primaria</h2>
    <p>
        Este formulario ha sido diseñado para presentar y elegir los posibles prestadores de atención primaria que se inscribieron a la convocatoria nacional pública de oferentes para la conformación y actualización de la red integrada e integral del Fondo Nacional de Prestaciones Sociales del Magisterio, que han cumplido con los criterios de integralidad. Con el fin de recopilar los datos de los afiliados que desean hacer efectiva su elección de la IPS primaria, en cumplimiento del Acuerdo 003 del 1 de abril de 2024.

      Con esta herramienta, se busca garantizar el derecho a la libre elección, en cumplimiento de lo establecido en la normativa vigente. A través de este formulario, los afiliados (docentes y beneficiarios) podrán expresar formalmente su interés en un prestador primario de manera voluntaria.
    </p>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('formulario.validar') }}">
        @csrf

        <div class="pregunta">
            <p><strong>*</strong> La respuesta a este formulario con el envío de información personal, propia o de terceros, implica su aceptación inequívoca al eventual uso o tratamiento de datos personales que realice Fiduprevisora S.A conforme a las finalidades contenidas en la política de protección de datos personales publicada <a href="https://tinyurl.com/2daxtblg" target="_blank">https://tinyurl.com/2daxtblg</a> en <a href="https://www.fiduprevisora.com.co" target="_blank">www.fiduprevisora.com.co</a>, en la cual se detallan entre otros aspectos, los derechos que le asisten como titular de información para realizar consultas, peticiones o reclamos relacionados con el tratamiento de información por parte de Fiduprevisora S.A.</a>.</p>
            <label><input type="radio" name="acepta_datos" value="SI" {{ old('acepta_datos') == 'SI' ? 'checked' : '' }} required> SI</label><br>
            <label><input type="radio" name="acepta_datos" value="NO" {{ old('acepta_datos') == 'NO' ? 'checked' : '' }} required> NO</label>
        </div>

        <div class="pregunta">
            <p><strong>*</strong> Declaro que ejerzo mi derecho a la libre escogencia según el Acuerdo 003 del 1 de abril del 2024, de una manera libre, formal y voluntaria, sin que exista ninguna influencia externa o presión para tomar esta decisión.</p>
            <label><input type="radio" name="libre_eleccion" value="SI" {{ old('libre_eleccion') == 'SI' ? 'checked' : '' }} required> SI</label><br>
            <label><input type="radio" name="libre_eleccion" value="NO" {{ old('libre_eleccion') == 'NO' ? 'checked' : '' }} required> NO</label>
        </div>

        <br>
        <button type="submit">Continuar</button>
    </form>
</div>
</body>
</html>
<script>
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