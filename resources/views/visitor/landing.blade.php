<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Q-Graders | Avaliação de Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root { --coffee-brown: #4E342E; --coffee-gold: #C6A664; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .bg-coffee { background-color: var(--coffee-brown); }
        .btn-coffee { background-color: var(--coffee-brown); color: white; border-radius: 12px; font-weight: bold; padding: 12px; transition: all 0.3s; }
        .btn-coffee:hover { background-color: #3b2722; color: white; transform: translateY(-2px); }
        .card-login { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #dee2e6; }
        .form-control:focus { border-color: var(--coffee-gold); box-shadow: 0 0 0 0.25 margin-bottom: 1.5rem; rgba(198, 166, 100, 0.25); }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-5">
                
                <div class="text-center mb-4">
                    <i class="bi bi-cup-hot-fill fs-1" style="color: var(--coffee-brown);"></i>
                    <h2 class="fw-bold mt-2" style="color: var(--coffee-brown);">Q-Graders</h2>
                    <p class="text-muted">Laboratório de Classificação e Prova</p>
                </div>

                <div class="card card-login p-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">{{ $session->title }}</h5>
                        <p class="text-muted small mb-4">Identifique-se para iniciar sua avaliação técnica.</p>

                        <form action="{{ route('visitor.join', $session->uuid) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0" placeholder="Nome e Sobrenome" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-uppercase text-muted">CPF</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-card-text text-muted"></i></span>
                                    <input type="tel" id="cpf" name="cpf" class="form-control border-start-0" placeholder="000.000.000-00" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-coffee w-100 shadow-sm">
                                ACESSAR MESA DE PROVA <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <p class="text-center mt-4 text-muted small">
                    Ambiente seguro e técnico de degustação.
                </p>

            </div>
        </div>
    </div>

    {{-- Script de Máscara de CPF --}}
    <script>
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d{1,3})/, "$1.$2");
            }
            e.target.value = value;
        });
    </script>
</body>
</html>