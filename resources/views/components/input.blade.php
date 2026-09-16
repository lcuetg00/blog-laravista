@props([
    'name' => null,
    // Texto del titulo del input
    'label',
    // Id del input (por defecto, el valor de 'name').
    'id' => null,
    // Tipo HTML (admite email, url, tel, number, ...).
    'type' => 'text',
    // Valor inicial del input, se le aplica old() directamente en este input.
    'value' => null,
    'maxlength' => null,
    'required' => false,
    'autocomplete' => null,
    // Si el input recibe foco al cargar la página.
    'autofocus' => false,
    // Si pinta el bloque de error correspondiente al name (por defecto true).
    'showError' => true,
    // Fuerza la clase is-invalid aunque no haya un error específico (útil en login, donde email y password se marcan a la vez para no revelar cuál falló).
    'invalid' => false,
    // Si está activo, el input se enlaza a Livewire con wire:model usando el name; deja de aplicar value/old.
    'wire' => false,
    // Clase de icono FontAwesome opcional; si se indica, el input se envuelve en un input-group con el icono como prefijo.
    'icon' => null,
    // Si está activo, sustituye el input por un editor de texto enriquecido (Summernote). Ignora type/icon/autocomplete.
    // Funciona con o sin Livewire (wire), pero solo en páginas que cargan panel.js (Summernote es exclusivo del panel).
    'richText' => false,
])

@php
    // La clave de error/estado es siempre el name (en modo Livewire es también la propiedad enlazada por wire:model)
    $claveError = $name;
    $inputId = $id ?? $name;
    $isInvalid = $invalid || $errors->has($claveError);
    $errorId = $inputId . '-error';
    $inputValue = old($name, $value);

    // Contenido inicial del editor enriquecido: entangle con Livewire (dos direcciones) o el valor plano ya escapado para JS
    $contenidoInicial = $wire ? "\$wire.entangle('{$name}')" : \Illuminate\Support\Js::from((string) $inputValue);
@endphp

<label for="{{ $inputId }}"
    class="form-label @if ($required) required @endif">{{ $label }}</label>

@if ($richText)
    {{-- wire:ignore si está activado para que no recargue el editor de texto --}}
    <div @if ($wire) wire:ignore @endif x-data="{ contenido: {!! $contenidoInicial !!} }" x-init="$nextTick(() => {
        window.initSummernote($refs.richTextEditor, {
            valorInicial: contenido,
            lang: @js(app()->getLocale()),
            @if($wire)
            onChange: (html) => { contenido = html },
            @endif
        });
        @if($wire)
        $watch('contenido', (valor) => window.actualizarSummernote($refs.richTextEditor, valor));
        @endif
    })">
        <textarea x-ref="richTextEditor" id="{{ $inputId }}"
            @if ($name) name="{{ $name }}" @endif
            class="form-control @if ($isInvalid) is-invalid @endif"
            @if ($required) required @endif
            @if ($showError && $errors->has($claveError)) aria-describedby="{{ $errorId }}" @endif></textarea>
    </div>
@else
    @if ($icon)
        {{-- Icono al principio del input --}}
        <div class="input-group @if ($isInvalid) has-validation @endif">
            <span class="input-group-text"><i class="{{ $icon }}" aria-hidden="true"></i></span>
    @endif
    <input type="{{ $type }}" id="{{ $inputId }}"
        @if ($name) name="{{ $name }}" @endif
        @if ($wire) wire:model="{{ $name }}" @else value="{{ $inputValue }}" @endif
        class="form-control @if ($type === 'color') form-control-color @endif @if ($isInvalid) is-invalid @endif"
        @if ($maxlength) maxlength="{{ $maxlength }}" @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($autofocus) autofocus @endif @if ($required) required @endif
        @if ($showError && $errors->has($claveError)) aria-describedby="{{ $errorId }}" @endif>
    @if ($icon)
        {{-- Cierra el div del icono al principio --}}
        </div>
    @endif
@endif

@if ($showError)
    @error($claveError)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@endif
