{{-- Bloque tabla: columnas y filas traducibles (créditos de fuentes e imágenes) --}}
@php
    $columnas = $bloque->campo('columnas', []);
    $filas = $bloque->campo('filas', []);
@endphp

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                @foreach ($columnas as $columna)
                    <th class="tabla-encabezado" scope="col">{{ $columna }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($filas as $fila)
                <tr>
                    @foreach ($fila as $celda)
                        <td>{{ $celda }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
