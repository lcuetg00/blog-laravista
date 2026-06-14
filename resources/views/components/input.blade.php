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
])

@php
    // La clave de error/estado es siempre el name (en modo Livewire es también la propiedad enlazada por wire:model)
    $claveError = $name;
    $inputId = $id ?? $name;
    $isInvalid = $invalid || $errors->has($claveError);
    $errorId = $inputId . '-error';
    $inputValue = old($name, $value);
@endphp

<label for="{{ $inputId }}"
    class="form-label @if ($required) required @endif">{{ $label }}</label>
<input type="{{ $type }}" id="{{ $inputId }}"
    @if ($name) name="{{ $name }}" @endif
    @if ($wire) wire:model="{{ $name }}" @else value="{{ $inputValue }}" @endif
    class="form-control @if ($isInvalid) is-invalid @endif"
    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
    @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    @if ($autofocus) autofocus @endif @if ($required) required @endif
    @if ($showError && $errors->has($claveError)) aria-describedby="{{ $errorId }}" @endif>
@if ($showError)
    @error($claveError)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@endif
