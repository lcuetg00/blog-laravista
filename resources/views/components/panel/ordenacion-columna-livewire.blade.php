<th scope="col" class="align-top {{ $clase }}" aria-sort="{{ $ariaSort }}">
    <button type="button" class="ordenacion-columna text-decoration-none d-inline-flex align-items-center gap-2 btn btn-link p-0"
        wire:click="{{ $metodo }}('{{ $columna->value }}')" aria-label="{{ $ariaLabel }}">
        <span>{{ $etiqueta }}</span>
        <span class="d-inline-flex align-items-end">
            <i class="fa-solid {{ $icono }} {{ $iconoClase }}" aria-hidden="true"></i>
            @if ($posicionOrden !== null)
                <span class="ordenacion-columna-orden" aria-hidden="true">{{ $posicionOrden }}</span>
            @endif
        </span>
    </button>
</th>
