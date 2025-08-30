<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'dashboard') === 0) ? 'active' : 'collapsed' }}" href="{{route('dashboard')}}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'core') === 0) ? 'show' : 'collapsed' }}" data-bs-target="#core-nav" data-bs-toggle="collapse" href="javascript:;">
                <i class="bx bx-cog"></i><span>Core</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="core-nav" class="nav-content collapse {{(strpos(Route::currentRouteName(), 'core') === 0) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="" class="{{ (strpos(Route::currentRouteName(), 'module') > 0) ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Modules</span>
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
            <a class="nav-link {{(strpos(Route::currentRouteName(), 'ComplaintsHelp') === 0) ? 'show' : 'collapsed' }}" data-bs-target="#core-nav" data-bs-toggle="collapse" href="javascript:;">
                <i class="bi bi-menu-button-wide"></i><span>Complaints & Help</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="core-nav" class="nav-content collapse {{(strpos(Route::currentRouteName(), 'ComplaintsHelp') === 0) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{url('ComplaintsHelp/category')}}" class="{{ request()->is('ComplaintsHelp/category') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{url('ComplaintsHelp/newsPromotions')}}" class="{{ request()->is('ComplaintsHelp/newsPromotions') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>News & Promotions</span>
                    </a>
                </li>
                <li>
                    <a href="{{url('ComplaintsHelp/masjids')}}" class="{{ request()->is('ComplaintsHelp/masjids') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Masjid Timings</span>
                    </a>
                </li>
                <li>
                    <a href="{{url('ComplaintsHelp/anouncements')}}" class="{{ request()->is('ComplaintsHelp/anouncements') ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Anouncements</span>
                    </a>
                </li>
            </ul>
        </li>

        

    </ul>

  </aside>
