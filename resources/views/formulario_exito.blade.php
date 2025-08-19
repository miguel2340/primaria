<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h1 {
            color: green;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 30px;
        }

        a.button {
            background: linear-gradient(to right, #009fe3, #773266);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }

        a.button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Registro exitoso!</h1>
        <p>Gracias por realizar la selección de su prestador de atención primaria.</p>
        <a href="{{ route('formulario.reiniciar') }}" class="button">Inicio</a>

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
