@props([
    'name',
    // Texto del titulo del input
    'label',
    // Id del input (por defecto, el valor de 'name').
    'id' => null,
    'required' => false,
    // Valor del atributo autocomplete (por defecto 'new-password').
    'autocomplete' => 'new-password',
    // Si pinta el bloque de error correspondiente al name (por defecto true).
    'showError' => true,
    // Fuerza la clase is-invalid aunque no haya un error específico (útil en login, donde email y password se marcan a la vez para no revelar cuál falló).
    'invalid' => false,
    // Muestra debajo del input la lista de reglas de la contraseña (mínimo 8, mayús/minús, número, símbolo) y las va validando en vivo mientras se escribe.
    'rules' => false,
])

@php
    $inputId = $id ?? $name;
    $isInvalid = $invalid || $errors->has($name);
    $errorId = $inputId . '-error';
    $rulesId = $inputId . '-rules';

    $describedBy = [];
    if ($showError && $errors->has($name)) {
        $describedBy[] = $errorId;
    }
    if ($rules) {
        $describedBy[] = $rulesId;
    }
    $ariaDescribedBy = $describedBy ? implode(' ', $describedBy) : null;
@endphp

{{-- Las regex de abajo solo detectan ASCII (sin flag /u). El servidor (Password::defaults) sí acepta unicode con \p{Ll}, \p{Lu}, \pN, etc., así que "Ñoño123!" pasa en el backend aunque aquí salga ❌ en mayús/minús — desajuste consciente porque la lista en vivo es solo orientativa. --}}
<div x-data="{
    show: false,
    value: '',
    hasFocus: false,
    get checkMinLength() { return this.value.length >= 8; },
    get checkMixedCase() { return /[a-z]/.test(this.value) && /[A-Z]/.test(this.value); },
    get checkNumbers() { return /\d/.test(this.value); },
    get checkSymbols() { return /[^A-Za-z0-9]/.test(this.value); },
}">
    <label for="{{ $inputId }}"
        class="form-label @if ($required) required @endif">{{ $label }}</label>
    <div class="input-group password-toggle-group">
        <input :type="show ? 'text' : 'password'" id="{{ $inputId }}" name="{{ $name }}"
            class="form-control @if ($isInvalid) is-invalid @endif" autocomplete="{{ $autocomplete }}"
            @if ($rules) x-model="value" @focus="hasFocus = true" @blur="hasFocus = false" @endif
            @if ($required) required @endif
            @if ($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif>
        <button type="button" class="btn btn-outline-secondary password-toggle-btn" @click="show = !show"
            :aria-label="show ? '{{ trans('actions.hide_password') }}' : '{{ trans('actions.show_password') }}'"
            :aria-pressed="show.toString()">
            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
        </button>
    </div>
    @if ($showError)
        @error($name)
            <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    @endif
    @if ($rules)
        <ul id="{{ $rulesId }}" class="password-rules list-unstyled small mt-2 mb-0"
            x-show="value.length > 0 || hasFocus" x-cloak x-transition.opacity aria-live="polite">
            <li :class="checkMinLength ? 'text-success' : 'text-muted'">
                <i class="fa-solid me-1" :class="checkMinLength ? 'fa-circle-check' : 'fa-circle text-danger'"
                    aria-hidden="true"></i>
                {{ trans('fields.password_rules.min_length') }}
            </li>
            <li :class="checkMixedCase ? 'text-success' : 'text-muted'">
                <i class="fa-solid me-1" :class="checkMixedCase ? 'fa-circle-check' : 'fa-circle text-danger'"
                    aria-hidden="true"></i>
                {{ trans('fields.password_rules.mixed_case') }}
            </li>
            <li :class="checkNumbers ? 'text-success' : 'text-muted'">
                <i class="fa-solid me-1" :class="checkNumbers ? 'fa-circle-check' : 'fa-circle text-danger'" aria-hidden="true"></i>
                {{ trans('fields.password_rules.numbers') }}
            </li>
            <li :class="checkSymbols ? 'text-success' : 'text-muted'">
                <i class="fa-solid me-1" :class="checkSymbols ? 'fa-circle-check' : 'fa-circle text-danger'"
                    aria-hidden="true"></i>
                {{ trans('fields.password_rules.symbols') }}
            </li>
        </ul>
    @endif
</div>
