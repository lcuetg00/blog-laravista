@use('Mcamara\LaravelLocalization\Facades\LaravelLocalization')
@use('App\Enums\ThemeEnum')

{{-- Selector de idioma --}}
<div class="mx-1">
    <div class="dropdown">
        <button
            class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
            type="button" data-bs-toggle="dropdown" aria-expanded="false"
            title="{{ trans('public.idioma.selector') }}">
            <span id="idioma-seleccionado" class="idioma-texto">{{ strtoupper(app()->getLocale()) }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                @if (app()->getLocale() !== $localeCode)
                    <li>
                        <a class="dropdown-item"
                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                            {{ $properties['native'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

{{-- Selector de tema claro/oscuro --}}
<div class="mx-1">
    <div class="dropdown">
        <button
            class="btn btn-primary rounded-circle header-button btn-size-45 d-flex align-items-center justify-content-center p-0"
            type="button" data-bs-toggle="dropdown" aria-expanded="false"
            title="{{ trans('public.modo.selector') }}">
            <i id="modo-seleccionado" class="fa-solid fa-sun" aria-hidden="true"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li class="dropdown-item" onclick="changePageMode({{ ThemeEnum::LUZ->value }})">
                <i class="fa-solid fa-sun me-2" aria-hidden="true"></i>
                <span>{{ trans('public.modo.luz') }}</span>
            </li>
            <li class="dropdown-item" onclick="changePageMode({{ ThemeEnum::OSCURO->value }})">
                <i class="fa-solid fa-moon me-2" aria-hidden="true"></i>
                <span>{{ trans('public.modo.oscuro') }}</span>
            </li>
        </ul>
    </div>
</div>
