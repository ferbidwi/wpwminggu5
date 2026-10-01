<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/admin">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('admin') ? 'active' : '' }}">
        <a class="nav-link" href="/admin"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
    </li>

    <li class="nav-item {{ Request::is('kasir') ? 'active' : '' }}">
        <a class="nav-link" href="/kasir"><i class="fas fa-fw fa-cash-register"></i><span>Kasir</span></a>
    </li>

    <li class="nav-item {{ Request::is('kasir/riwayat*') ? 'active' : '' }}">
        <a class="nav-link" href="/kasir/riwayat"><i class="fas fa-fw fa-history"></i><span>Riwayat Transaksi</span></a>
    </li>

    <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
        <a class="nav-link" href="/buku"><i class="fas fa-fw fa-book"></i><span>Data Buku</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Interface</div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true">
            <i class="fas fa-fw fa-cog"></i><span>Components</span>
        </a>
        <div id="collapseTwo" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Components:</h6>
                <a class="collapse-item" href="/buttons">Buttons</a>
                <a class="collapse-item" href="/cards">Cards</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true">
            <i class="fas fa-fw fa-wrench"></i><span>Utilities</span>
        </a>
        <div id="collapseUtilities" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a class="collapse-item" href="/utilities-color">Colors</a>
                <a class="collapse-item" href="/utilities-border">Borders</a>
                <a class="collapse-item" href="/utilities-animation">Animations</a>
                <a class="collapse-item" href="/utilities-other">Other</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Addons</div>

    <li class="nav-item {{ Request::is('tables') ? 'active' : '' }}">
        <a class="nav-link" href="/tables"><i class="fas fa-fw fa-table"></i><span>Tables</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>