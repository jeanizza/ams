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
                            Serviceable
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
                                <p>List of Serviceable</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gss.admin.transferred_items') }}" class="nav-link">
                        <i class="fas fa-exchange-alt nav-icon"></i>
                        <p>Transferred Items</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gss.admin.unserviceable_items') }}" class="nav-link">
                        <i class="nav-icon fas fa-times-circle"></i>
                        <p>Unserviceable</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gss.admin.ledger') }}" class="nav-link">
                        <i class="nav-icon fas fa-wrench"></i>
                        <p>Maintenance Ledger</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-sync-alt"></i>
                        <p>
                            Items Reconciliation
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('gss.admin.reconciliation') }}" class="nav-link">
                                <i class="fas fa-tools nav-icon"></i>
                                <p>Reconcile Items</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gss.admin.add_disposal_value') }}" class="nav-link">
                                <i class="fas fa-hand-holding-usd nav-icon"></i>
                                <p>Add Disposal Value</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('gss.admin.disposal_details') }}" class="nav-link">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Disposal Details</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
