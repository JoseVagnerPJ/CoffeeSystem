<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade - DigiGrão</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --coffee-brown: #4b3621;
            --coffee-gold: #c6a664;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .policy-card {
            max-width: 900px;
            margin: 40px auto;
        }
        .section-title {
            color: var(--coffee-brown);
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-size: 1.1rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="policy-card card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white p-4 p-md-5 border-0 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 60px; margin-bottom: 15px;">
            <h1 class="h2 fw-bold mb-2">Política de Privacidade</h1>
            <p class="mb-0 opacity-75">Plataforma DigiGrão | Última atualização: {{ date('d/m/Y') }}</p>
        </div>
        
        <div class="card-body p-4 p-md-5" style="background-color: white; line-height: 1.8; color: #333;">
            
            <section class="mb-5">
                <h2 class="section-title">1. Introdução</h2>
                <p>A <strong>DigiGrão</strong> é uma plataforma voltada para a gestão técnica de cafés, desenvolvida por <strong>José Vagner Pereira Junior</strong>. Esta política tem como objetivo informar como tratamos os dados operacionais e de identificação necessários para o funcionamento do sistema.</p>
            </section>

            <section class="mb-5">
                <h2 class="section-title">2. Informações que Coletamos</h2>
                <p>O sistema coleta apenas os dados essenciais para a rastreabilidade e gestão da qualidade:</p>
                <ul>
                    <li><strong>Dados de Identificação:</strong> Nome do usuário para exibição no perfil e relatórios internos.</li>
                    <li><strong>Dados Operacionais:</strong> Notas de avaliação sensorial (cupping), registros de processos de torrefação, movimentações de estoque e endereçamento de lotes no armazém.</li>
                    <li><strong>Uso de Hardware:</strong> O sistema <strong>não</strong> utiliza a câmera do dispositivo, microfone ou qualquer sensor de biometria para suas funções.</li>
                </ul>
            </section>

            <section class="mb-5">
                <h2 class="section-title">3. Uso das Informações</h2>
                <p>As informações são processadas estritamente para fins de gestão técnica:</p>
                <ul>
                    <li>Identificação do provador responsável por cada nota de café.</li>
                    <li>Controle preciso de entrada e saída de estoque verde.</li>
                    <li>Monitoramento do fluxo de trabalho no setor de torrefação.</li>
                    <li>Geração de históricos de auditoria e inventário.</li>
                </ul>
                <p><strong>Importante:</strong> Não coletamos, não solicitamos e não armazenamos endereços de e-mail dos usuários para fins de comunicação ou marketing.</p>
            </section>

            <section class="mb-5">
                <h2 class="section-title">4. Compartilhamento e Terceiros</h2>
                <p>Os dados inseridos na plataforma são de propriedade da unidade de trabalho vinculada. A DigiGrão <strong>não compartilha, não vende e não aluga</strong> informações operacionais ou nomes de usuários para terceiros. O sistema é independente e não utiliza integrações de login social (como Google ou Facebook).</p>
            </section>

            <section class="mb-5">
                <h2 class="section-title">5. Seus Direitos (LGPD)</h2>
                <p>Em conformidade com a Lei Geral de Proteção de Dados (LGPD), o usuário ou a empresa contratante tem o direito de solicitar a correção de dados, acesso aos registros operacionais ou a exclusão definitiva da conta e de seus respectivos dados a qualquer momento.</p>
            </section>

            <section class="mb-5">
                <h2 class="section-title">6. Segurança</h2>
                <p>Utilizamos criptografia de ponta (HTTPS) e práticas seguras de desenvolvimento para garantir que todos os dados de produção e estoque fiquem protegidos contra acessos não autorizados.</p>
            </section>

            <section>
                <h2 class="section-title">7. Contato e Desenvolvedor</h2>
                <p>Esta aplicação foi desenvolvida por <strong>José Vagner Pereira Junior</strong>. Para dúvidas sobre privacidade, suporte técnico ou solicitações de dados, entre em contato através dos canais abaixo:</p>
                <p class="mb-1">
                    <i class="bi bi-envelope-fill me-2" style="color: var(--coffee-brown);"></i> <strong>E-mail:</strong> josevagnerpereirajunior@gmail.com
                </p>
                <p>
                    <i class="bi bi-whatsapp me-2" style="color: #25d366;"></i> <strong>Telefone/WhatsApp:</strong> +55 (35) 9 9738-5307
                </p>
            </section>

        <div class="card-footer bg-light text-center py-4 border-0">
    @if(auth()->check())
        {{-- Usuário está Logado --}}
        <a href="{{ route('inicio') }}" class="btn btn-dark px-4 rounded-pill fw-bold" style="background-color: var(--coffee-brown);">
            <i class="bi bi-arrow-left me-2"></i>Voltar ao Sistema
        </a>
    @else
        {{-- Usuário está Deslogado --}}
        <a href="{{ url('/') }}" class="btn btn-dark px-4 rounded-pill fw-bold" style="background-color: var(--coffee-brown);">
            <i class="bi bi-person-lock me-2"></i>Voltar ao Login
        </a>
    @endif
</div>
    </div>
</div>

</body>
</html>