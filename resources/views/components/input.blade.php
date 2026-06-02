@props([
    'name',
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
])

@php
    $inputId = $id ?? $name;
    $isInvalid = $invalid || $errors->has($name);
    $errorId = $inputId . '-error';
    $inputValue = old($name, $value);
@endphp

<label for="{{ $inputId }}"
    class="form-label @if ($required) required @endif">{{ $label }}</label>
<input type="{{ $type }}" id="{{ $inputId }}" name="{{ $name }}"
    class="form-control @if ($isInvalid) is-invalid @endif" value="{{ $inputValue }}"
    @if ($maxlength) maxlength="{{ $maxlength }}" @endif
    @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
    @if ($autofocus) autofocus @endif @if ($required) required @endif
    @if ($showError && $errors->has($name)) aria-describedby="{{ $errorId }}" @endif>
@if ($showError)
    @error($name)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
@endif
