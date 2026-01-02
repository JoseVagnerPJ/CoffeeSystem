<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') | Unique Cafés</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
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
        
        /* Select2 Customizado */
        .select2-container--default .select2-selection--single {
            height: 45px !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--coffee-gold) !important;
            color: white !important;
        }

        /* Estilo da Logo da Empresa - Quadrada com cantos arredondados */
        .company-logo-nav {
            height: 42px;
            width: 42px;
            object-fit: cover;
            border-radius: 8px; /* Cantos levemente arredondados */
            border: 1px solid rgba(255,255,255,0.2);
            background-color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark fixed-top navbar-top shadow">
        <div class="container-fluid">
            {{-- Lado Esquerdo: Logo Quadrada e Nome da Empresa --}}
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('inicio') }}">
                @if(Auth::check() && Auth::user()->mainCompany->logo)
                    <img src="{{ asset('storage/' . Auth::user()->mainCompany->logo) }}" alt="Logo" class="me-2 company-logo-nav shadow-sm">
                @else
                    <div class="me-2 company-logo-nav d-flex align-items-center justify-content-center bg-white shadow-sm">
                        <i class="bi bi-cup-hot-fill" style="color: var(--coffee-brown); font-size: 1.2rem;"></i>
                    </div>
                @endif
                <span>{{ Auth::check() ? Auth::user()->mainCompany->name : 'Unique Cafés' }}</span>
            </a>

            {{-- Lado Direito: Usuário com Foto Circular --}}
            <div class="d-flex align-items-center text-white">
                <div class="text-end me-3 d-none d-sm-block">
                    <div class="small fw-bold mb-0" style="line-height: 1;">{{ Auth::user()->name ?? 'Visitante' }}</div>
                    <small class="text-muted" style="font-size: 0.7rem; color: #cfd8dc !important;">{{ Auth::user()->profile->name ?? '' }}</small>
                </div>
                
                @if(Auth::check() && Auth::user()->photo)
                    <div class="rounded-circle shadow-sm border border-2 border-white" style="width: 40px; height: 40px; overflow: hidden; background-color: white;">
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="User" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                @else
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-dark shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-heading" style="opacity: 0.5; padding: 1.5rem 1.25rem 0.5rem; color: #fff; font-size: 0.7rem; font-weight: bold; text-transform: uppercase;">Menu Operacional</div>
                <ul class="nav flex-column">
                    <li><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ route('inicio') }}"><i class="bi bi-house-door me-2"></i> Início</a></li>
                    <li><a class="nav-link {{ Request::is('green-coffee*') ? 'active' : '' }}" href="{{ route('estoque.index') }}"><i class="bi bi-box-seam me-2"></i> Estoque Verde</a></li>
                    <li><a class="nav-link {{ Request::is('laboratory*') ? 'active' : '' }}" href="{{ route('laboratorio') }}"><i class="bi bi-clipboard-pulse me-2"></i> Laboratório</a></li>
                    <li><a class="nav-link {{ Request::is('roasting*') ? 'active' : '' }}" href="{{ route('torrefacao') }}"><i class="bi bi-fire me-2"></i> Torrefação</a></li>
                </ul>

                <div class="sidebar-heading" style="opacity: 0.5; padding: 1.5rem 1.25rem 0.5rem; color: #fff; font-size: 0.7rem; font-weight: bold; text-transform: uppercase;">Gestão & Cadastros</div>
                <ul class="nav flex-column">
                    <li><a class="nav-link {{ Request::is('registries') ? 'active' : '' }}" href="{{ route('registries.index') }}"><i class="bi bi-people me-2"></i> Cadastros</a></li>
                    <li><a class="nav-link {{ Request::is('settings*') ? 'active' : '' }}" href="{{ route('settings') }}"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                </ul>

                <div class="px-3 mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 border-0 text-start ps-3">
                            <i class="bi bi-box-arrow-right me-2"></i> Sair
                        </button>
                    </form>
                </div>
            </nav>
            @endauth

            <main class="{{ Auth::check() ? 'col-md-9 ms-sm-auto col-lg-10' : 'col-12' }} px-md-4 main-content">
                @yield('content') 
            </main>
        </div>
    </div>

    {{-- JS do Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- AQUI É O PULO DO GATO: Direciona os scripts das páginas filhas para cá --}}
    @stack('scripts')
</body>
</html>