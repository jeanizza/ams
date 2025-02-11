<!-- CodeIgniter Sidebar Section -->
<li class="nav-item">
    <a href="{{ url('http://localhost:8080/admin?username=cdd') }}" class="nav-link">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<!-- Lists for PRs and POs -->
<li class="nav-item">
    <a href="#" class="nav-link">
      <i class="nav-icon fas fa-solid fa-list"></i>
      <p>Lists<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="{{ url('/user/pr') }}" class="nav-link">
          <i class="nav-icon fas fa-light fa-file"></i>
          <p>Purchase Requests</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/user/po') }}" class="nav-link">
          <i class="nav-icon fas fa-regular fa-clipboard"></i>
          <p>Purchase Orders</p>
        </a>
      </li>
    </ul>
</li>

<!-- Logbook Section -->
<li class="nav-item">
    <a href="#" class="nav-link">
      <i class="nav-icon fas fa-regular fa-book"></i>
      <p>Logbook<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="{{ url('/user/receiving') }}" class="nav-link">
          <i class="nav-icon fas fa-solid fa-arrow-right"></i>
          <p>Receiving</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/user/releasing') }}" class="nav-link">
          <i class="nav-icon fas fa-solid fa-arrow-left"></i>
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
          <i class="nav-icon fas fa-solid fa-folder"></i>
          <p>PPMP</p>
        </a> 
      </li>
      <li class="nav-item">
        <a href="{{ url('/user/suppliers') }}" class="nav-link">
          <i class="nav-icon fas fa-solid fa-store"></i>
          <p>Suppliers</p>
        </a>
      </li>
    </ul>
</li>
