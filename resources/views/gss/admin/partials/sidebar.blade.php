<!-- resources/views/gss/admin/partials/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('gss.admin.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">GSS Admin</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('gss.admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            Inventory Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>  
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('gss.admin.add_record') }}" class="nav-link">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Add Record</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gss.admin.list_serviceable') }}" class="nav-link">
                                <i class="fas fa-edit nav-icon circle-icon"></i>
                                <p>Update</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gss.admin.unserviceable') }}" class="nav-link">
                        <i class="nav-icon fas fa-times-circle"></i>
                        <p>Unserviceable</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gss.admin.maintenance_ledger') }}" class="nav-link">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>Maintenance Ledger</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gss.admin.reconciliation') }}" class="nav-link">
                        <i class="nav-icon fas fa-balance-scale"></i>
                        <p>Reconciliation</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
