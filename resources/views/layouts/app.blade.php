<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Unique Cafés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --coffee-brown: #3e2723; --coffee-gold: #c0a16b;
            --coffee-cream: #f4f1ea; --coffee-text: #5d4037;
            --coffee-white: #ffffff;
        }
        body { background-color: var(--coffee-cream); color: var(--coffee-text); font-family: 'Segoe UI', sans-serif; }
        .navbar-top { background-color: var(--coffee-brown); border-bottom: 3px solid var(--coffee-gold); min-height: 60px; }
        .sidebar { position: fixed; top: 60px; bottom: 0; left: 0; z-index: 1000; background-color: var(--coffee-brown); padding: 0; }
        .nav-link { color: #cfd8dc; padding: 12px 20px; transition: 0.3s; border-left: 4px solid transparent; text-decoration: none; display: block; }
        .nav-link:hover, .nav-link.active { color: var(--coffee-gold); background-color: rgba(255,255,255,0.05); border-left: 4px solid var(--coffee-gold); }
        .main-content { margin-top: 80px; padding-bottom: 40px; }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark fixed-top navbar-top shadow">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}"><i class="bi bi-cup-hot-fill me-2"></i>Unique Cafés</a>
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle text-white fs-4"></i>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="p-3 text-white border-bottom border-secondary mb-2 small opacity-50">MENU</div>
                <ul class="nav flex-column">
                    <li><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li><a class="nav-link {{ Request::is('estoque*') ? 'active' : '' }}" href="{{ url('/estoque') }}"><i class="bi bi-box-seam me-2"></i> Estoque Verde</a></li>
                    <li><a class="nav-link {{ Request::is('laboratorio*') ? 'active' : '' }}" href="{{ url('/laboratorio') }}"><i class="bi bi-clipboard-pulse me-2"></i> Laboratório</a></li>
                </ul>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                @yield('content') 
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>