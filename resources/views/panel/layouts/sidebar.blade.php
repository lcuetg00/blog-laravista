<nav class="sidebar border-end col-md-3 col-lg-2 p-0 bg-body-tertiary"
     aria-label="{{ trans('panel.sidebar_nav') }}">
    <div class="offcanvas-md offcanvas-start bg-body-tertiary"
         tabindex="-1"
         id="sidebarMenu"
         aria-labelledby="sidebarMenuLabel">

        <div class="offcanvas-header border-bottom">
            <a href="{{ route('panel.index') }}" class="d-flex align-items-center" id="sidebarMenuLabel">
                <img class="logo-nav logo-header" src="{{ asset('images/laravistaLogoSmaller.png') }}"
                    alt="{{ config('app.name') }}" title="{{ config('app.name') }}">
            </a>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarMenu"
                    aria-label="{{ trans('panel.close') }}">
            </button>
        </div>

        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column px-2 pt-2">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('panel.index') ? 'active' : '' }}"
                       aria-current="{{ request()->routeIs('panel.index') ? 'page' : 'false' }}"
                       href="{{ route('panel.index') }}">
                        <i class="fa-solid fa-house-chimney" aria-hidden="true"></i>
                        {{ trans('panel.dashboard') }}
                    </a>
                </li>
            </ul>

            <hr class="my-2 mx-2">

            <ul class="nav flex-column px-2 pb-2 mb-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="#">
                        <i class="fa-solid fa-gear" aria-hidden="true"></i>
                        {{ trans('panel.settings') }}
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-logout-btn">
                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            {{ trans('panel.sign_out') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
