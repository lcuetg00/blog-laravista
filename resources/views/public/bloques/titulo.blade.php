{{-- Bloque de título suelto (h2). $clase permite a cada página ajustar alineación/margen sin perder el contenido de BD --}}
@php
    $clase = $clase ?? 'text-center mb-3';
@endphp

<h2 class="{{ $clase }}">{{ $bloque->campo('titulo') }}</h2>
