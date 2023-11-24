<div class="sidebar-header border-bottom">
    <div class="sidebar-brand">
        <img class="img-fluid" width="88" height="32" src="{{ asset('assets/brand/logo.jpg') }}" alt="LOGO" srcset="">
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
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('permissions.index') ? 'active' : '' }}" href="{{ route('permissions.index') }}">  
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-beach-access') }}"></use>
        </svg> Permissions<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('posts.index') ? 'active' : '' }}" href="{{ route('posts.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
        </svg> Posts<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
    </li>
</ul>
<div class="sidebar-footer border-top d-none d-md-flex">
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
