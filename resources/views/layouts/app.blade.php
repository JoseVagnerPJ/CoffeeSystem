<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- ÍCONE DO NAVEGADOR (FAVICON) --}}
    <link rel="icon" type="image/png" href="{{ asset('flavicon.png') }}">
    <title>@yield('title') | Q-Grader</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root { --coffee-brown: #3e2723; --coffee-gold: #c0a16b; --coffee-cream: #f4f1ea; --coffee-text: #5d4037; --coffee-white: #ffffff; --coffee-gold-light: #e8e0d5; }
        body { background-color: var(--coffee-cream); color: var(--coffee-text); font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        
        .navbar-top { background-color: var(--coffee-brown); border-bottom: 2px solid var(--coffee-gold); min-height: 55px; }
        .sidebar { position: fixed; top: 55px; bottom: 0; left: 0; z-index: 1000; background-color: var(--coffee-brown); padding: 0; display: flex; flex-direction: column; }
        
        .nav-link { color: #cfd8dc; padding: 10px 20px; transition: 0.3s; border-left: 4px solid transparent; text-decoration: none; display: block; font-size: 0.9rem; }
        .nav-link:hover, .nav-link.active { color: var(--coffee-gold); background-color: rgba(255,255,255,0.05); border-left: 4px solid var(--coffee-gold); }
        
        .main-content { margin-top: 75px; padding-bottom: 40px; }
        .brand-text-style { color: var(--coffee-gold-light) !important; font-weight: 400; font-size: 1.8rem; letter-spacing: 0.75px; text-decoration: none; }
        .logo-bg-circle { background-color: var(--coffee-gold-light); width: 35px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 12px; }
        
        .user-avatar-nav { width: 49px; height: 49px; display: flex; align-items: center; justify-content: center; }
        .unit-logo-nav { height: 47px; width: 47px; object-fit: cover; border-radius: 6px; background-color: white; margin-right: 10px; }
        .btn-privacy-link { font-size: 0.65rem; color: var(--coffee-gold-light); text-decoration: none; opacity: 0.5; }

        /* Ajuste de tabelas */
        .table-responsive { overflow-x: hidden !important; border-radius: 12px; }
        .table-responsive table { width: 100% !important; min-width: auto !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark fixed-top navbar-top shadow-sm py-0">
        <div class="container-fluid d-flex align-items-center justify-content-between" style="height: 55px;">
            {{-- Lado Esquerdo: Empresa --}}
            <div class="d-none d-md-flex align-items-center" style="flex: 1;">
                @if(Auth::check() && Auth::user()->mainCompany && Auth::user()->mainCompany->logo)
                    <img src="{{ asset('storage/' . Auth::user()->mainCompany->logo) }}" class="unit-logo-nav">
                @endif
                <span class="text-white fw-bold opacity-75 text-truncate" style="font-size: 1.6rem;">
                    {{ Auth::check() && Auth::user()->mainCompany ? Auth::user()->mainCompany->name : 'Unique Cafés' }}
                </span>
            </div>

            {{-- Centro: Logo Sistema --}}
            <div class="d-flex justify-content-center" style="flex: 1;">
                <a class="d-flex align-items-center text-decoration-none" href="{{ route('inicio') }}">
                    <div class="logo-bg-circle"><img src="{{ asset('images/logo.png') }}" height="35"></div>
                    <span class="brand-text-style">Q-Grader</span>
                </a>
            </div>

            {{-- Lado Direito: Usuário --}}
            <div class="d-flex justify-content-end align-items-center" style="flex: 1;">
                <div class="text-end me-2 d-none d-sm-block text-white">
                    <div class="fw-bold mb-0">{{ Auth::user()->name ?? 'Visitante' }}</div>
                    <small style="color: #cfd8dc !important;">{{ Auth::user()->profile->name ?? '' }}</small>
                </div>

                {{-- LÓGICA DA FOTO AJUSTADA --}}
                @if(Auth::check() && Auth::user()->photo)
                    <div class="rounded-circle shadow-sm border border-2 border-white user-avatar-nav" style="overflow: hidden; background-color: white;">
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="User" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                @else
                    <div class="rounded-circle bg-white text-dark shadow-sm user-avatar-nav" style="border: 2px solid white;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="flex-grow-1">
                    <div class="sidebar-heading" style="opacity: 0.5; padding: 1rem; color: #fff; font-size: 0.65rem; font-weight: bold; text-transform: uppercase;">Menu Operacional</div>
                    <ul class="nav flex-column">
                        <li><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ route('inicio') }}"><i class="bi bi-house-door me-2"></i> Início</a></li>
                        
                        <li>
                            @php 
                                $hasModule = auth()->user()->mainCompany->mod_stock ?? false;
                                $hasPerm = auth()->user()->hasPermission('green_coffee_view') || auth()->user()->hasPermission('green_coffee_full'); 
                            @endphp
                            <a href="{{ ($hasModule && $hasPerm) ? route('estoque.index') : 'javascript:void(0)' }}" 
                               class="nav-link {{ Request::is('green-coffee*') ? 'active' : '' }} d-flex align-items-center" 
                               onclick="checkAccess(event, {{ $hasModule ? 'true' : 'false' }}, {{ $hasPerm ? 'true' : 'false' }})">
                                <i class="bi bi-box-seam me-2"></i> Estoque Verde 
                                @if(!$hasModule) 
                                    <i class="bi bi-lock-fill ms-auto text-danger" title="Módulo não contratado"></i>
                                @elseif(!$hasPerm)
                                    <i class="bi bi-lock-fill ms-auto opacity-50" title="Sem permissão"></i> 
                                @endif
                            </a>
                        </li>

                        <li>
                            @php 
                                $hasModule = auth()->user()->mainCompany->mod_lab ?? false;
                                $hasPerm = auth()->user()->hasPermission('lab_view') || auth()->user()->hasPermission('lab_full'); 
                            @endphp
                            <a href="{{ ($hasModule && $hasPerm) ? route('laboratorio') : 'javascript:void(0)' }}" 
                               class="nav-link {{ Request::is('laboratory*') ? 'active' : '' }} d-flex align-items-center" 
                               onclick="checkAccess(event, {{ $hasModule ? 'true' : 'false' }}, {{ $hasPerm ? 'true' : 'false' }})">
                                <i class="bi bi-clipboard-pulse me-2"></i> Laboratório
                                @if(!$hasModule) 
                                    <i class="bi bi-lock-fill ms-auto text-danger" title="Módulo não contratado"></i>
                                @elseif(!$hasPerm)
                                    <i class="bi bi-lock-fill ms-auto opacity-50" title="Sem permissão"></i> 
                                @endif
                            </a>
                        </li>

                        <li>
                            @php 
                                $hasModule = auth()->user()->mainCompany->mod_roast ?? false;
                                $hasPerm = auth()->user()->hasPermission('roast_view') || auth()->user()->hasPermission('roast_full'); 
                            @endphp
                            <a href="javascript:void(0)" class="nav-link d-flex align-items-center" 
                               onclick="devModuleAccess(event, '{{ route('roasting.index') }}', {{ $hasModule ? 'true' : 'false' }}, {{ $hasPerm ? 'true' : 'false' }})">
                                <i class="bi bi-fire me-2"></i> Torrefação 
                                @if(!$hasModule) 
                                    <i class="bi bi-lock-fill ms-auto text-danger" title="Módulo não contratado"></i>
                                @elseif(!$hasPerm)
                                    <i class="bi bi-lock-fill ms-auto opacity-50" title="Sem permissão"></i> 
                                @endif
                            </a>
                        </li>

                        <li>
                            @php 
                                $hasModule = auth()->user()->mainCompany->mod_prod ?? false;
                                $hasPerm = auth()->user()->hasPermission('production_view') || auth()->user()->hasPermission('production_full'); 
                            @endphp
                            <a href="javascript:void(0)" class="nav-link d-flex align-items-center" 
                               onclick="devModuleAccess(event, '#', {{ $hasModule ? 'true' : 'false' }}, {{ $hasPerm ? 'true' : 'false' }})">
                                <i class="bi bi-boxes me-2"></i> Produção 
                                @if(!$hasModule) 
                                    <i class="bi bi-lock-fill ms-auto text-danger" title="Módulo não contratado"></i>
                                @elseif(!$hasPerm)
                                    <i class="bi bi-lock-fill ms-auto opacity-50" title="Sem permissão"></i> 
                                @endif
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-heading" style="opacity: 0.5; padding: 1rem; color: #fff; font-size: 0.65rem; font-weight: bold; text-transform: uppercase;">Gestão & Cadastros</div>
                    <ul class="nav flex-column">
                        <li>
                            @php
                                $u = auth()->user();
                                $canReg = $u->hasPermission('supplier_view') || $u->hasPermission('supplier_full') || $u->hasPermission('sample_view') || $u->hasPermission('sample_full') || $u->hasPermission('machine_view') || $u->hasPermission('machine_full') || $u->hasPermission('sensory_view') || $u->hasPermission('sensory_full');
                            @endphp
                            <a href="{{ $canReg ? route('registries.index') : 'javascript:void(0)' }}" class="nav-link {{ Request::is('registries') ? 'active' : '' }}" onclick="if(!{{ $canReg ? 'true' : 'false' }}) alert('Você não tem permissão!')">
                                <i class="bi bi-people me-2"></i> Cadastros @if(!$canReg) <i class="bi bi-lock-fill ms-auto"></i> @endif
                            </a>
                        </li>
                        <li>
                            @php $hasSet = auth()->user()->hasPermission('settings_edit'); @endphp
                            <a href="javascript:void(0)" class="nav-link" 
                            onclick="devModuleAccess(event, '{{ route('settings') }}', true, {{ $hasSet ? 'true' : 'false' }})">
                                <i class="bi bi-gear me-2"></i> Configurações 
                                <i class="bi bi-lock-fill ms-auto" style="font-size: 0.8rem; color: {{ $hasSet ? 'inherit' : '#ff6b6b' }}"></i>
                            </a>
                        </li>
                    </ul>
                {{-- PORTAL MASTER - MOVIDO PARA CÁ --}}
                    @if(auth()->check() && auth()->user()->hasPermission('dev_access'))
                        <div class="sidebar-heading" style="opacity: 0.8; padding: 1rem; color: #ffca28; font-size: 0.65rem; font-weight: bold; text-transform: uppercase;">
                            <i class="bi bi-shield-shaded me-1"></i> Portal Master
                        </div>
                        <ul class="nav flex-column">
                            <li>
                                <a href="{{ route('master.companies.index') }}" 
                                   class="nav-link {{ Request::is('master-panel*') ? 'active' : '' }}" 
                                   style="color: #ffca28 !important;">
                                    <i class="bi bi-building-gear me-2"></i> Gerenciar Empresas
                                </a>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="px-3 mt-auto mb-3">
                    <form method="POST" action="{{ route('logout') }}"><button type="submit" class="btn btn-outline-danger btn-sm w-100 border-0 text-start ps-3"><i class="bi bi-box-arrow-right me-2"></i> Sair</button>@csrf</form>
                    <div class="text-center"><a href="{{ route('privacy') }}" class="bi bi-shield-lock btn-privacy-link">Privacidade</a></div>
                </div>
            </nav>
            @endauth
            <main class="{{ Auth::check() ? 'col-md-9 ms-sm-auto col-lg-10' : 'col-12' }} px-md-4 main-content">@yield('content')</main>
        </div>
    </div>

    {{-- MODAL SECRETO --}}
    <div class="modal fade" id="modalDevPass" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-body p-4 text-center">
                    <i class="bi bi-shield-lock-fill fs-1 text-warning mb-3"></i>
                    <h6 class="fw-bold mb-3">Módulo Protegido</h6>
                    <input type="password" id="devInputPass" class="form-control text-center border-0 bg-light" placeholder="Senha de Acesso">
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-light btn-sm w-100 fw-bold" onclick="bootstrap.Modal.getInstance(document.getElementById('modalDevPass')).hide()">Sair</button>
                        <button class="btn btn-dark btn-sm w-100 fw-bold" onclick="validateDevPass()">Entrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let devClickCount = 0;
        let devClickTimer;
        let pendingRoute = '';

        function checkAccess(event, hasModule, hasPermission) {
            if (!hasModule) {
                event.preventDefault();
                alert("Este módulo não faz parte do seu plano atual. Entre em contato com o suporte para habilitar.");
                return false;
            }
            if (!hasPermission) {
                event.preventDefault();
                alert("Seu usuário não possui permissão administrativa para acessar este módulo.");
                return false;
            }
            return true;
        }

        function devModuleAccess(event, route, hasModule, hasPermission) {
            event.preventDefault();
            if (!hasModule) {
                alert("Este módulo não faz parte do seu plano atual. Entre em contato com o suporte para habilitar.");
                return;
            }
            if (!hasPermission) {
                alert("Você não tem permissão para acessar esse módulo!");
                return;
            }
            pendingRoute = route;
            devClickCount++;
            clearTimeout(devClickTimer);

            if (devClickCount >= 5) {
                devClickCount = 0;
                document.getElementById('devInputPass').value = '';
                const m = new bootstrap.Modal(document.getElementById('modalDevPass'));
                m.show();
                setTimeout(() => document.getElementById('devInputPass').focus(), 500);
            } else {
                devClickTimer = setTimeout(() => {
                    if (devClickCount > 0) alert("Módulo em desenvolvimento! (🚧)");
                    devClickCount = 0;
                }, 1500);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
        // Seleciona todos os formulários e define autocomplete off
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.setAttribute('autocomplete', 'off');
        });

        // Seleciona todos os inputs e define autocomplete off
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.setAttribute('autocomplete', 'off');
        });
    });

        function validateDevPass() {
            if (document.getElementById('devInputPass').value === "1572") {
                window.location.href = pendingRoute;
            } else {
                alert("Senha incorreta!");
                document.getElementById('devInputPass').value = '';
            }
        }
        document.getElementById('devInputPass').addEventListener('keypress', (e) => { if(e.key === 'Enter') validateDevPass(); });
    </script>
    @stack('scripts')
</body>
</html>