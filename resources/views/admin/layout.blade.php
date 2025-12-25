<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Dashboard - PerpusKampus</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }

        /* Sidebar Modern (Putih) */
        .bg-gradient-primary {
            background-color: #ffffff !important;
            background-image: none !important;
            border-right: 1px solid #e3e6f0;
        }

        .sidebar .nav-item .nav-link {
            color: #666 !important;
            font-weight: 500;
        }

        .sidebar .nav-item .nav-link i {
            color: #b7b9cc !important;
        }

        .sidebar .nav-item.active .nav-link {
            color: #2c3e50 !important;
            background: #F8FBFF;
            border-right: 3px solid #AEDEFC;
        }

        .sidebar .nav-item.active .nav-link i {
            color: #AEDEFC !important;
        }

        .sidebar-brand-text {
            color: #2c3e50;
        }

        .sidebar-brand-icon {
            color: #AEDEFC;
        }

        /* Card Stats Modern */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .text-primary {
            color: #5dade2 !important;
        }

        /* Table Modern */
        .table thead th {
            background-color: #F8FBFF;
            color: #666;
            border-bottom: 2px solid #e3e6f0;
            font-weight: 600;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-book"></i></div>
                <div class="sidebar-brand-text mx-3">Perpus Admin</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.books*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.books.index') }}">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Kelola Buku</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Transaksi</div>

            <li class="nav-item {{ request()->routeIs('admin.borrows.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.borrows.index') }}">
                    <i class="fas fa-fw fa-hand-holding"></i>
                    <span>Peminjaman</span>

                    @php $pendingCount = \App\Models\Borrowing::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="badge badge-danger badge-counter" style="margin-left: 5px;">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.borrows.verification') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.borrows.verification') }}">
                    <i class="fas fa-fw fa-clipboard-check"></i>
                    <span>Verifikasi Kembali</span>

                    @php $returnCount = \App\Models\Borrowing::where('status', 'return_pending')->count(); @endphp
                    @if($returnCount > 0)
                    <span class="badge badge-warning badge-counter" style="background-color: #f6c23e; color: #fff; margin-left: 5px;">
                        {{ $returnCount }}
                    </span>
                    @endif
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.reservations.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.reservations.index') }}">
                    <i class="fas fa-fw fa-bookmark"></i>
                    <span>Antrian Booking</span>

                    @php $resCount = \App\Models\Reservation::where('status', 'pending')->count(); @endphp
                    @if($resCount > 0)
                    <span class="badge badge-danger badge-counter" style="margin-left: 5px;">{{ $resCount }}</span>
                    @endif
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Pengguna</div>

            <li class="nav-item {{ request()->routeIs('admin.students.listanggota') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.students.listanggota') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Daftar Anggota</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.students.verification') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.students.verification') }}">
                    <i class="fas fa-fw fa-id-card"></i>
                    <span>Verifikasi KTM</span>

                    @php $ktmPending = \App\Models\Student::where('verification_status', 'pending')->count(); @endphp
                    @if($ktmPending > 0)
                    <span class="badge badge-info badge-counter" style="margin-left: 5px;">{{ $ktmPending }}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.students.create') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.students.create') }}">
                    <i class="fas fa-fw fa-user-plus"></i>
                    <span>Input Mahasiswa</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Laporan & Cetak
            </div>

            <li class="nav-item {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Laporan Perpustakaan</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.requests.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.requests.index') }}">
                    <i class="fas fa-fw fa-lightbulb"></i>
                    <span>Usulan Buku</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrator</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=Admin&background=AEDEFC&color=fff">
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; 2025 PerpusKampus Admin</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Logout" di bawah jika Anda ingin mengakhiri sesi admin ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>

                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="background: #2c3e50; border:none;">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
    @stack('scripts')

</body>

</html>