<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'dashboard') === 0) ? 'active' : 'collapsed' }}" href="{{route('dashboard')}}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'clients') === 0) ? 'show' : 'collapsed' }}" data-bs-target="#core-nav" data-bs-toggle="collapse" href="javascript:;">
                <i class="ri-account-circle-line"></i><span>Clients</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="core-nav" class="nav-content collapse {{(strpos(Route::currentRouteName(), 'clients') === 0) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('clients.index')}}" class="{{ (strpos(Route::currentRouteName(), 'module') > 0) ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Clients List</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'products') === 0) ? 'show' : 'collapsed' }}" data-bs-target="#products-nav" data-bs-toggle="collapse" href="javascript:;">
                <i class="bi bi-gem"></i><span>Products</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="products-nav" class="nav-content collapse {{(strpos(Route::currentRouteName(), 'products') === 0) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('products.index')}}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>All Products</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'treatments') === 0) ? 'show' : 'collapsed' }}" data-bs-target="#treatments-nav" data-bs-toggle="collapse" href="javascript:;">
                <i class="bi bi-heart-pulse"></i><span>Treatments</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="treatments-nav" class="nav-content collapse {{(strpos(Route::currentRouteName(), 'treatments') === 0) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('treatments.index')}}" class="{{ request()->routeIs('treatments.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>All Treatments</span>
                    </a>
                </li>
                <li>
                    <a href="{{route('treatments.create')}}" class="{{ request()->routeIs('treatments.create') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Add Treatment</span>
                    </a>
                </li>
            </ul>
        </li>

        @if(Auth::user() && Auth::user()->isAdmin())
        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'staff') === 0) ? 'show' : 'collapsed' }}" data-bs-target="#staff-nav" data-bs-toggle="collapse" href="javascript:;">
                <i class="bi bi-people"></i><span>Staff Management</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="staff-nav" class="nav-content collapse {{(strpos(Route::currentRouteName(), 'staff') === 0) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('staff.index')}}" class="{{ request()->routeIs('staff.index') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>All Staff</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif
    </ul>

  </aside>
