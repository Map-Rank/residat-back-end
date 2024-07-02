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
            </svg> Dashboard</a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
        </svg> Users</a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('permissions.index') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-beach-access') }}"></use>
        </svg>Roles </a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('all.permissions') ? 'active' : '' }}" href="{{ route('all.permissions') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-beach-access') }}"></use>
        </svg>Permissions  </a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('posts.index') ? 'active' : '' }}" href="{{ route('posts.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
        </svg> Posts</a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('zones.index') ? 'active' : '' }}" href="{{ route('zones.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
        </svg> Zones<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
    </li>
    <li class="nav-group">
        {{-- {{route('reports.index')}} --}}
        <a class="nav-link nav-group-toggle" href="">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
            </svg> 
            Reports
        </a>
        <ul class="nav-group-items compact">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('health-report-items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Health Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agriculture.report.items.create') ? 'active' : '' }}" href="{{ route('agriculture.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Agriculture Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('infrastructure.report.items.create') ? 'active' : '' }}" href="{{ route('infrastructure.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Infrastructure Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('social.report.items.create') ? 'active' : '' }}" href="{{ route('social.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Social Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('food.security.report.items.create') ? 'active' : '' }}" href="{{ route('food.security.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Food Security Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ressource.completion.report.items.create') ? 'active' : '' }}" href="{{ route('ressource.completion.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Completion Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('fishing.vulnerability.report.items.create') ? 'active' : '' }}" href="{{ route('fishing.vulnerability.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Fishing Vulnerability Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('water.stress.report.items.create') ? 'active' : '' }}" href="{{ route('water.stress.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Water Stress Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('migration.report.items.create') ? 'active' : '' }}" href="{{ route('migration.report.items.create') }}">
                    <span class="nav-icon"><span class="nav-icon-bullet"></span></span> Migration Reports
                </a>
            </li>
        </ul>
        {{-- <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('health-report-items.create') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-calendar') }}"></use>
                    </svg> Health Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('agriculture.report.items.create') }}">
                    <svg class="nav-icon">
                        <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-calendar-check') }}"></use>
                    </svg> Agriculture Reports
                </a>
            </li>
        </ul> --}}
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('feedbacks.index') ? 'active' : '' }}" href="{{ route('feedbacks.index') }}">
        <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/@coreui/icons/sprites/free.svg#cil-user') }}"></use>
        </svg> Feedbacks<span class="badge badge-sm bg-info ms-auto">Important</span></a>
    </li>
</ul>
<div class="sidebar-footer border-top d-none d-md-flex">
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
