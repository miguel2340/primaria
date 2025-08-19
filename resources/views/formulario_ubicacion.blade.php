<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ubicación</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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

     select { width: 100%; padding: 10px; border-radius: 5px; }
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
        <img src="{{ asset('images/logo_fomag.jpg') }}" alt="Logo FOMAG" style="max-width: 700px;">
    </div>
    <h2>Datos de Ubicación</h2>

    <form method="POST" action="{{ route('formulario.prestadores') }}">
        @if ($errors->any())
            <div style="color: red; margin-top: 15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @csrf

        <label>Departamento de Residencia</label>
        <select name="departamento_residencia" id="departamento_residencia" required>
            <option value="">-- Seleccione departamento --</option>
            @foreach($departamentos as $d)
                <option value="{{ $d->id_departamento }}">{{ strtoupper($d->departamento) }}</option>
            @endforeach
        </select>

        <label>Municipio de Residencia</label>
        <select name="municipio_residencia" id="municipio_residencia" required>
            <option value="">-- Seleccione municipio --</option>
        </select>


        <label>Departamento de Atención</label>
        <select name="departamento_atencion" id="departamento">
            <option value="">-- Seleccione departamento --</option>
            @foreach($departamentos as $d)
                <option value="{{ $d->id_departamento }}">{{ strtoupper($d->departamento) }}</option>
            @endforeach
        </select>

        <label>Municipio de Atención</label>
        <select name="municipio_atencion" id="municipio_atencion">
            <option value="">-- Seleccione municipio --</option>
        </select>

        <button type="submit">Continuar</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Cargar municipios de residencia según departamento seleccionado
$('#departamento_residencia').on('change', function () {
    let id = $(this).val();
    $('#municipio_residencia').html('<option>Cargando...</option>');

    if (id) {
        $.ajax({
            url: '{{ url("municipios/por-departamento") }}/' + id,
            method: 'GET',
            success: function (data) {
                let options = '<option value="">-- Seleccione municipio --</option>';
                $.each(data, function (key, value) {
                    options += `<option value="${key}">${value}</option>`;
                });
                $('#municipio_residencia').html(options);
            },
            error: function () {
                $('#municipio_residencia').html('<option>Error al cargar municipios</option>');
            }
        });
    } else {
        $('#municipio_residencia').html('<option value="">-- Seleccione municipio --</option>');
    }
});

    $(document).ready(function () {
        $('.select2').select2();

        // Validación al enviar
        $('form').on('submit', function (e) {
            let residencia = $('select[name="municipio_residencia"]').val();
            let departamento = $('#departamento').val();
            let municipioAtencion = $('#municipio_atencion').val();

            if (!residencia || !departamento || !municipioAtencion) {
                alert('Por favor complete todos los campos de ubicación antes de continuar.');
                e.preventDefault();
            }
        });

        // Cargar municipios por departamento
        $('#departamento').on('change', function () {
            let id = $(this).val();
            $('#municipio_atencion').html('<option>Cargando...</option>');

            if (id) {
                $.ajax({
                    url: '{{ url("municipios/por-departamento") }}/' + id,
                    method: 'GET',
                    success: function (data) {
                        let options = '<option value="">-- Seleccione municipio --</option>';
                        $.each(data, function (key, value) {
                            options += `<option value="${key}">${value}</option>`;
                        });
                        $('#municipio_atencion').html(options);
                    },
                    error: function (xhr) {
                        console.error("Error AJAX:", xhr);
                        $('#municipio_atencion').html('<option>Error al cargar municipios</option>');
                    }
                });
            } else {
                $('#municipio_atencion').html('<option value="">-- Seleccione municipio --</option>');
            }
        });
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
</body>
</html>
