<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">DENR-X AMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('user.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- General Services Menu -->
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
                                <li class="nav-item">
                                    <a href="{{ route('user.general-services.returned_unserviceable_form') }}" class="nav-link {{ request()->routeIs('user.general-services.returned_unserviceable_form') ? 'active' : '' }}">
                                        <p>Receipt of Returned Unserviceable Form</p>
                                    </a>
                                </li>
                                <!--
                                <li class="nav-item">
                                    <a href="{{ route('user.general-services.gate_pass_form') }}" class="nav-link {{ request()->routeIs('user.general-services.gate_pass_form') ? 'active' : '' }}">
                                        <p>Gate Pass Form</p>
                                    </a>
                                </li>
                                -->
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
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Procurement Services</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>

<style>
/* Ensure that submenu items under "Request Forms" are properly indented and aligned */
.nav-treeview > .nav-item > .nav-link {
    padding-left: 40px; /* Adjust padding to control the indentation */
}

.nav-treeview > .nav-item > .nav-link p {
    white-space: normal; /* Allow text to wrap */
    text-indent: 0;  /* Remove negative indent */
    margin-left: 0; /* Align text directly under the icon */
}

.nav-treeview > .nav-item > .nav-link i.nav-icon {
    margin-right: 10px; /* Add space between the icon and the text */
}

.menu-open .nav-treeview {
    display: block; /* Ensure submenu stays expanded when active */
}

.nav-treeview > .nav-item > .nav-link {
    height: auto; /* Allow height to adjust to content */
    line-height: 1.2; /* Adjust line height for better spacing */
    padding-top: 8px;
    padding-bottom: 8px;
}

.sidebar .nav-link p {
    margin: 0;
    padding: 0;
}
</style>
