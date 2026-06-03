<th scope="col" class="align-top {{ $clase }}" aria-sort="{{ $ariaSort }}">
    <a href="{{ $url }}" class="sort-header text-decoration-none d-inline-flex align-items-center gap-2"
        aria-label="{{ $ariaLabel }}">
        <span>{{ $etiqueta }}</span>
        <span class="d-inline-flex align-items-end">
            <i class="fa-solid {{ $icono }} {{ $iconoClase }}" aria-hidden="true"></i>
            @if ($posicionOrden !== null)
                <span class="sort-header-orden" aria-hidden="true">{{ $posicionOrden }}</span>
            @endif
        </span>
    </a>
</th>
