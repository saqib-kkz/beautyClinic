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
    </ul>

  </aside>
