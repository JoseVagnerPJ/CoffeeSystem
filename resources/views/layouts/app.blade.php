<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- ÍCONE DO NAVEGADOR (FAVICON) --}}
    <link rel="icon" type="image/png" href="{{ asset('flavicon.png') }}">
    
    <title>@yield('title') | DigiGrão</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --coffee-brown: #3e2723; 
            --coffee-gold: #c0a16b;
            --coffee-cream: #f4f1ea; 
            --coffee-text: #5d4037;
            --coffee-white: #ffffff;
            --coffee-gold-light: #e8e0d5; 
        }
        body { background-color: var(--coffee-cream); color: var(--coffee-text); font-family: 'Segoe UI', sans-serif; }
        
        /* --- NAVBAR TOP --- */
        .navbar-top { background-color: var(--coffee-brown); border-bottom: 2px solid var(--coffee-gold); min-height: 55px; }
        .sidebar { position: fixed; top: 55px; bottom: 0; left: 0; z-index: 1000; background-color: var(--coffee-brown); padding: 0; }
        
        .nav-link { color: #cfd8dc; padding: 10px 20px; transition: 0.3s; border-left: 4px solid transparent; text-decoration: none; display: block; font-size: 0.9rem; }
        .nav-link:hover, .nav-link.active { color: var(--coffee-gold); background-color: rgba(255,255,255,0.05); border-left: 4px solid var(--coffee-gold); }
        
        .main-content { margin-top: 75px; padding-bottom: 40px; }
        
        /* --- BRANDING / LOGO AJUSTADA CONFORME SOLICITADO --- */
        .brand-center-pill {
            background-color: transparent; /* Fundo removido de tudo */
            padding: 3px 15px 3px 5px;
            border-radius: 25px;
            border: none; /* Borda removida */
            display: flex;
            align-items: center;
            box-shadow: none; /* Sombra removida */
        }

        /* O fundo claro agora fica APENAS no círculo da logo */
        .logo-bg-circle {
            background-color: var(--coffee-gold-light); /* Cor bege aplicada aqui */
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 12px;
        }

        .system-logo-central { height: 30px; width: auto; }

        .brand-text-style { 
            color: var(--coffee-gold-light) !important; /* Texto agora tem a cor do fundo antigo */
            font-weight: 500;
            font-size: 1.8rem; /* Aumentado levemente para destaque */
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        /* --- RESPONSIVIDADE GLOBAL PARA TABELAS (NOTEBOOKS) --- */
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
        }

        /* Força qualquer tabela dentro de um container responsivo a ter largura mínima */
        .table-responsive table {
            min-width: 950px !important;
        }

        /* --- OUTROS ELEMENTOS --- */
        .unit-logo-nav {
            height: 40px; width: 40px; object-fit: cover;
            border-radius: 6px; border: 1px solid rgba(255,255,255,0.2);
            background-color: white; margin-right: 10px;
        }

        .user-avatar-nav { width: 40px; height: 40px; }

        .select2-container--default .select2-selection--single {
            height: 40px !important; border: 1px solid #dee2e6 !important;
            border-radius: 8px !important; display: flex !important; align-items: center !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark fixed-top navbar-top shadow-sm py-0">
        <div class="container-fluid d-flex align-items-center justify-content-between" style="height: 55px;">
            
            {{-- Lado Esquerdo: Unidade --}}
            <div class="d-none d-md-flex align-items-center" style="flex: 1;">
                @if(Auth::check() && Auth::user()->mainCompany && Auth::user()->mainCompany->logo)
                    <img src="{{ asset('storage/' . Auth::user()->mainCompany->logo) }}" alt="Logo Unidade" class="unit-logo-nav">
                @endif
                <span class="text-white fw-bold opacity-75 text-truncate" style="font-size: 1.6rem;">
                    {{ Auth::check() && Auth::user()->mainCompany ? Auth::user()->mainCompany->name : 'Unique Cafés' }}
                </span>
            </div>

            {{-- CENTRO: Branding DigiGrão (Refatorado conforme solicitado) --}}
            <div class="d-flex justify-content-center" style="flex: 1;">
                <div class="brand-center-pill">
                    <a class="d-flex align-items-center text-decoration-none" href="{{ route('inicio') }}">
                        <div class="logo-bg-circle">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="system-logo-central">
                        </div>
                        <span class="brand-text-style">DigiGrão</span>
                    </a>
                </div>
            </div>

            {{-- Lado Direito: Usuário --}}
            <div class="d-flex justify-content-end align-items-center" style="flex: 1;">
                <div class="text-end me-2 d-none d-sm-block text-white">
                    <div class="fw-bold mb-0" style="line-height: 1; font-size: 0.8rem;">{{ Auth::user()->name ?? 'Visitante' }}</div>
                    <small style="font-size: 0.65rem; color: #cfd8dc !important;">{{ Auth::user()->profile->name ?? '' }}</small>
                </div>
                
                @if(Auth::check() && Auth::user()->photo)
                    <div class="rounded-circle shadow-sm border border-2 border-white user-avatar-nav" style="overflow: hidden; background-color: white;">
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="User" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                @else
                    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center text-dark shadow-sm user-avatar-nav">
                        <i class="bi bi-person-fill" style="font-size: 0.9rem;"></i>
                    </div>
                @endif
            </div>

        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-heading" style="opacity: 0.5; padding: 1rem 1.25rem 0.4rem; color: #fff; font-size: 0.65rem; font-weight: bold; text-transform: uppercase;">Menu Operacional</div>
                <ul class="nav flex-column">
                    <li><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ route('inicio') }}"><i class="bi bi-house-door me-2"></i> Início</a></li>
                    <li><a class="nav-link {{ Request::is('green-coffee*') ? 'active' : '' }}" href="{{ route('estoque.index') }}"><i class="bi bi-box-seam me-2"></i> Estoque Verde</a></li>
                    <li><a class="nav-link {{ Request::is('laboratory*') ? 'active' : '' }}" href="{{ route('laboratorio') }}"><i class="bi bi-clipboard-pulse me-2"></i> Laboratório</a></li>
                    <li><a class="nav-link {{ Request::is('roasting*') ? 'active' : '' }}" href="{{ route('torrefacao') }}"><i class="bi bi-fire me-2"></i> Torrefação</a></li>
                </ul>

                <div class="sidebar-heading" style="opacity: 0.5; padding: 1rem 1.25rem 0.4rem; color: #fff; font-size: 0.65rem; font-weight: bold; text-transform: uppercase;">Gestão & Cadastros</div>
                <ul class="nav flex-column">
                    <li><a class="nav-link {{ Request::is('registries') ? 'active' : '' }}" href="{{ route('registries.index') }}"><i class="bi bi-people me-2"></i> Cadastros</a></li>
                    <li><a class="nav-link {{ Request::is('settings*') ? 'active' : '' }}" href="{{ route('settings') }}"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                </ul>

                <div class="px-3 mt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 border-0 text-start ps-3" style="font-size: 0.8rem;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>