<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Identidad</title>
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
            background: var(--primario);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pregunta {
            margin-bottom: 25px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        label {
            display: block;
            margin: 6px 0;
            cursor: pointer;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
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
            background-color: rgb(4, 115, 167);
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
            background: var(--primario);
        }

        .paso.activo {
            color: #773266;
        }

        .paso.activo::after {
            background: var(--primario);
            height: 6px;
        }
        input[type="date"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 10px;
    background-color: #fff;
    color: #333;
    font-family: 'Segoe UI', sans-serif;
    font-size: 16px;
}

    </style>
</head>
<body>
<div class="container">
    <div class="progreso">
        <div class="paso completado">1. Condiciones</div>
        <div class="paso activo">2. Validación</div>
        <div class="paso">3. Datos</div>
        <div class="paso">4. Prestador</div>
    </div>

    <h2>Validación de Identidad</h2>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if(empty($preguntas))
        {{-- FORMULARIO DE DOCUMENTO --}}
        <form method="POST" action="{{ route('formulario.generarPreguntas') }}">
            @csrf
            <label for="documento">Ingrese su número de documento:</label>
            <input type="number" name="documento" id="documento" required>
            <button type="submit">Continuar</button>
        </form>
    @else
        {{-- FORMULARIO DE PREGUNTAS --}}
        <form method="POST" action="{{ route('formulario.validarPreguntas') }}">
            @csrf
            @foreach($preguntas as $pregunta)
                <div class="pregunta">
                    <p><strong>{{ $pregunta['texto'] }}</strong></p>

                    @if($pregunta['campo'] === 'fecha_expedicion')
                        <input type="date" name="respuestas[fecha_expedicion]" required>
                    @else
                        @foreach($pregunta['opciones'] as $opcion)
                            <label>
                                <input type="radio" name="respuestas[{{ $pregunta['campo'] }}]" value="{{ $opcion }}" required>
                                {{ $opcion }}
                            </label>
                        @endforeach
                    @endif
                </div>
            @endforeach
            <button type="submit">Validar</button>
        </form>
    @endif
</div>
</body>
<script>
    let salidaControlada = false;

    window.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                salidaControlada = true;
            });
        });
    });

    // Beacon para abandono (recarga o cerrar pestaña)
    window.addEventListener('beforeunload', function () {
        if (!salidaControlada) {
            console.log('✅ Enviando beacon de abandono');

            // También registrar timestamp en localStorage (opcional)
            localStorage.setItem('ultimoBeacon', new Date().toISOString());

            const success = navigator.sendBeacon("{{ url('/formulario/abandonado') }}");

            if (success) {
                console.log('📡 Beacon enviado correctamente');
            } else {
                console.warn('❌ El beacon NO se pudo enviar');
            }
        }
    });
</script>

</html>
