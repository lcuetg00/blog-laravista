<nav class="sidebar border-end shadow col-12 col-md-2 p-0" aria-label="{{ trans('sidebar.nav') }}">
    <div class="offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">

        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2 w-100" id="sidebarMenuLabel">
                <span class="sidebar-menu-title flex-grow-1">{{ trans('sidebar.menu') }}</span>

                {{-- Botón para colapsar/expandir el sidebar (solo escritorio) --}}
                <button type="button" class="sidebar-collapse-btn d-none d-md-inline-flex" id="sidebarCollapseBtn"
                    aria-controls="sidebarMenu" aria-expanded="true" aria-label="{{ trans('sidebar.collapse') }}"
                    data-label-collapse="{{ trans('sidebar.collapse') }}"
                    data-label-expand="{{ trans('sidebar.expand') }}">
                    <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
                </button>
            </div>
            <button type="button" class="btn btn-primary sidebar-close d-md-none" data-bs-dismiss="offcanvas"
                data-bs-target="#sidebarMenu" aria-label="{{ trans('sidebar.close') }}">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3">
            <ul class="nav flex-column px-2 pt-2">
                <li class="nav-item mb-2">
                    <a class="nav-link sidebar-option popup-sidebar d-flex align-items-center gap-2 {{ request()->routeIs('panel.index') ? 'active' : '' }}"
                        aria-current="{{ request()->routeIs('panel.index') ? 'page' : 'false' }}"
                        href="{{ route('panel.index') }}"
                        aria-label="{{ trans('sidebar.dashboard') }}"
                        data-popup-sidebar="{{ trans('sidebar.dashboard') }}">
                        <i class="fa-solid fa-house-chimney" aria-hidden="true"></i>
                        <span class="sidebar-label">{{ trans('sidebar.dashboard') }}</span>
                    </a>
                </li>

                @can(\App\Helpers\PermissionHelper::USUARIOS_LISTADO_PERMISSION)
                    <li class="nav-item mb-2">
                        <a class="nav-link sidebar-option popup-sidebar d-flex align-items-center gap-2 {{ request()->routeIs('panel.usuarios.*') ? 'active' : '' }}"
                            aria-current="{{ request()->routeIs('panel.usuarios.*') ? 'page' : 'false' }}"
                            href="{{ route('panel.usuarios.index') }}"
                            aria-label="{{ trans('sidebar.usuarios') }}"
                            data-popup-sidebar="{{ trans('sidebar.usuarios') }}">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                            <span class="sidebar-label">{{ trans('sidebar.usuarios') }}</span>
                        </a>
                    </li>
                @endcan
            </ul>

            <hr class="my-2 mx-2">

            <ul class="nav flex-column px-2 pb-2 mb-auto">
                <li class="nav-item mb-2">
                    <a class="nav-link sidebar-option popup-sidebar d-flex align-items-center gap-2" href="#"
                        aria-label="{{ trans('sidebar.settings') }}"
                        data-popup-sidebar="{{ trans('sidebar.settings') }}">
                        <i class="fa-solid fa-gear" aria-hidden="true"></i>
                        <span class="sidebar-label">{{ trans('sidebar.settings') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
