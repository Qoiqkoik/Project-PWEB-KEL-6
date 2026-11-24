<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FauzanKos - @yield('title', 'Sistem Manajemen Kos')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">FAUZANKOS</a>
            @auth
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.kamar.index') }}">Kamar</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.penghuni.index') }}">Penghuni</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.pembayaran.index') }}">Pembayaran</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.pengaduan.index') }}">Pengaduan</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Laporan</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.pembayaran.laporan') }}">Laporan Keuangan</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.pengaduan.laporan') }}">Laporan Pengaduan</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('penghuni.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('penghuni.pembayaran.index') }}">Tagihan Saya</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('penghuni.pengaduan.index') }}">Pengaduan Saya</a></li>
                    @endif
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <span class="navbar-text text-white me-3">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
            @endauth
        </div>
    </nav>

    <div class="container mb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
