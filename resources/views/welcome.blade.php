<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CoffeeSystem - Gestão de Torrefação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { height: 100vh; background: #212529; color: white; padding: 20px; }
        .card-stat { border: none; border-radius: 10px; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4>CoffeeSystem</h4>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="#" class="nav-link text-white">Dashboard</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white">Estoque Verde</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white">Laboratório</a></li>
                    <li class="nav-item"><a href="#" class="nav-link text-white">Torrefação</a></li>
                </ul>
            </div>

            <div class="col-md-10 p-4">
                <h2>Resumo Operacional</h2>
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card card-stat bg-primary text-white p-3">
                            <h5>Lotes em Estoque</h5>
                            <h3>{{ $stats['lotes'] }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat bg-success text-white p-3">
                            <h5>Máquinas Ativas</h5>
                            <h3>{{ $stats['maquinas'] }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stat bg-warning text-dark p-3">
                            <h5>Empresas</h5>
                            <h3>{{ $stats['empresas'] }}</h3>
                        </div>
                    </div>
                </div>
                
                <div class="mt-5">
                    <h4>Rastreabilidade Rápida</h4>
                    <div class="input-group mb-3" style="max-width: 400px;">
                        <input type="text" class="form-control" placeholder="Digite o ID do Lote ou Amostra...">
                        <button class="btn btn-dark">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>