<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa Encerrada | Q-Graders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --coffee-brown: #4E342E; }
        body { background-color: #f8f9fa; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6 p-4">
                <i class="bi bi-lock-fill display-1 text-muted opacity-50"></i>
                <h2 class="fw-bold mt-3" style="color: var(--coffee-brown);">Mesa Encerrada</h2>
                <p class="text-muted">Sinto muito, as avaliações para a sessão <strong>"{{ $session->title }}"</strong> já foram concluídas e o acesso está fechado.</p>
                <hr class="my-4 mx-auto w-25">
                <p class="small text-muted">Aguarde uma nova mesa técnica ou entre em contato com o coordenador do laboratório.</p>
            </div>
        </div>
    </div>
</body>
</html>