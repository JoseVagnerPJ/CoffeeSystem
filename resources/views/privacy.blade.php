@extends('layouts.app')

@section('title', 'Política de Privacidade')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white p-4 border-0">
            <h1 class="h3 fw-bold mb-0">Política de Privacidade</h1>
            <p class="mb-0 opacity-75">Última atualização: {{ date('d/m/Y') }}</p>
        </div>
        
        <div class="card-body p-4 p-md-5" style="background-color: white; line-height: 1.8; color: #333;">
            
            <section class="mb-5">
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">1. Introdução</h2>
                <p>A <strong>DigiGrão</strong> é uma plataforma voltada para a gestão técnica de cafés, desenvolvida por <strong>José Vagner Pereira Junior</strong>. Esta política tem como objetivo informar como tratamos os dados operacionais e de identificação necessários para o funcionamento do sistema.</p>
            </section>

            <section class="mb-5">
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">2. Informações que Coletamos</h2>
                <p>O sistema coleta apenas os dados essenciais para a rastreabilidade e gestão da qualidade:</p>
                <ul>
                    <li><strong>Dados de Identificação:</strong> Nome do usuário para exibição no perfil e relatórios internos.</li>
                    <li><strong>Dados Operacionais:</strong> Notas de avaliação sensorial (cupping), registros de processos de torrefação, movimentações de estoque e endereçamento de lotes no armazém.</li>
                    <li><strong>Uso de Hardware:</strong> O sistema <strong>não</strong> utiliza a câmera do dispositivo, microfone ou qualquer sensor de biometria para suas funções.</li>
                </ul>
            </section>

            <section class="mb-5">
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">3. Uso das Informações</h2>
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
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">4. Compartilhamento e Terceiros</h2>
                <p>Os dados inseridos na plataforma são de propriedade da unidade de trabalho vinculada. A DigiGrão <strong>não compartilha, não vende e não aluga</strong> informações operacionais ou nomes de usuários para terceiros. O sistema é independente e não utiliza integrações de login social (como Google ou Facebook).</p>
            </section>

            <section class="mb-5">
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">5. Seus Direitos (LGPD)</h2>
                <p>Em conformidade com a Lei Geral de Proteção de Dados (LGPD), o usuário ou a empresa contratante tem o direito de solicitar a correção de dados, acesso aos registros operacionais ou a exclusão definitiva da conta e de seus respectivos dados a qualquer momento.</p>
            </section>

            <section class="mb-5">
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">6. Segurança</h2>
                <p>Utilizamos criptografia de ponta (HTTPS) e práticas seguras de desenvolvimento para garantir que todos os dados de produção e estoque fiquem protegidos contra acessos não autorizados.</p>
            </section>

            <section>
                <h2 class="h5 fw-bold text-uppercase border-bottom pb-2 mb-3" style="color: var(--coffee-brown);">7. Contato e Desenvolvedor</h2>
                <p>Esta aplicação foi desenvolvida por <strong>José Vagner Pereira Junior</strong>. Para dúvidas sobre privacidade, suporte técnico ou solicitações de dados, entre em contato através dos canais abaixo:</p>
                <p class="mb-1">
                    <i class="bi bi-envelope-fill me-2"></i> <strong>E-mail:</strong> josevagnerpereirajunior@gmail.com
                </p>
                <p>
                    <i class="bi bi-whatsapp me-2"></i> <strong>Telefone/WhatsApp:</strong> +55 (35) 9 9738-5307
                </p>
            </section>

        </div>
        <div class="card-footer bg-light text-center py-3 border-0">
            <a href="{{ route('inicio') }}" class="btn btn-outline-secondary btn-sm rounded-pill">Voltar ao Sistema</a>
        </div>
    </div>
</div>
@endsection