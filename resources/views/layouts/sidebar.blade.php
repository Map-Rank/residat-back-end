<div class="sidebar-header border-bottom">
    <div class="sidebar-brand">
        <svg class="sidebar-brand-full" width="88" height="32" alt="CoreUI Logo">
            <use xlink:href="{{ asset('assets/brand/coreui.svg#full') }}"></use>
        </svg>
        <svg class="sidebar-brand-narrow" width="32" height="32" alt="CoreUI Logo">
            <use xlink:href="{{ asset('assets/brand/coreui.svg#signet') }}"></use>
        </svg>
    </div>
    <button class="btn-close d-lg-none" type="button" data-coreui-dismiss="offcanvas" data-coreui-theme="dark"
        aria-label="Close"
        onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
</div>
<ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-speedometer') }}"></use>
            </svg> Dashboard<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
        </svg> Users<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('permission') ? 'active' : '' }}" href="">  {{-- {{ route('permission.index') }} --}}
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-beach-access') }}"></use>
        </svg> Permissions<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
    </li>
</ul>
<div class="sidebar-footer border-top d-none d-md-flex">
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
