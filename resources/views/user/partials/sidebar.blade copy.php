<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text font-weight-light">DENR-X AMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                
                <!-- Laravel Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('user.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Laravel General Services Menu -->
                <li class="nav-item {{ request()->routeIs('user.general-services.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('user.general-services.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            General Services
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Inventory -->
                        <li class="nav-item">
                            <a href="{{ route('user.general-services.inventory') }}" class="nav-link {{ request()->routeIs('user.general-services.inventory') ? 'active' : '' }}">
                                <i class="fas fa-boxes nav-icon"></i>
                                <p>Inventory</p>
                            </a>
                        </li>

                        <!-- Request Forms -->
                        <li class="nav-item {{ request()->routeIs('user.general-services.request-forms.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('user.general-services.request-forms.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>
                                    Request Forms
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('user.general-services.defects_and_complaints_form') }}" class="nav-link {{ request()->routeIs('user.general-services.defects_and_complaints_form') ? 'active' : '' }}">
                                        <p>Defects and Complaints Form</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('user.general-services.job_request_form') }}" class="nav-link {{ request()->routeIs('user.general-services.job_request_form') ? 'active' : '' }}">
                                        <p>Job Request Form</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- View Request -->
                        <li class="nav-item">
                            <a href="{{ route('user.general-services.view_request') }}" class="nav-link {{ request()->routeIs('user.general-services.view_request') ? 'active' : '' }}">
                                <i class="fas fa-eye nav-icon"></i>
                                <p>View Request</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Procurement Services (Main Menu) -->
                <li class="nav-item {{ request()->is('user/procurement*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('user/procurement*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Procurement Services
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <!-- Submenu: CodeIgniter List, Logbook, Services -->
                    <ul class="nav nav-treeview">
                        <!-- Lists for PRs and POs -->
                        <li class="nav-item">
                            <a href="{{ url('http://localhost/ams_psts/user/pr') }}" class="nav-link">
                                <i class="nav-icon fas fa-file"></i>
                                <p>Purchase Requests</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('http://localhost/ams_psts/user/po') }}" class="nav-link">
                                <i class="nav-icon fas fa-clipboard"></i>
                                <p>Purchase Orders</p>
                            </a>
                        </li>

                        <!-- Logbook -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Logbook<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/user/receiving') }}" class="nav-link">
                                        <i class="nav-icon fas fa-arrow-right"></i>
                                        <p>Receiving</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/user/releasing') }}" class="nav-link">
                                        <i class="nav-icon fas fa-arrow-left"></i>
                                        <p>Releasing</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Procurement Services -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Services<i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/user/ppmp') }}" class="nav-link">
                                        <i class="nav-icon fas fa-folder"></i>
                                        <p>PPMP</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/user/suppliers') }}" class="nav-link">
                                        <i class="nav-icon fas fa-store"></i>
                                        <p>Suppliers</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>

<!-- Blade styling -->
<style>
/* Same as your existing styles */
</style>
