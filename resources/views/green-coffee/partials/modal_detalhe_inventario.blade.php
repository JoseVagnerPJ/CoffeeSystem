<div class="modal fade" id="modalHistoricoDetalhe" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-light border-0 py-3">
                <h5 class="fw-bold mb-0">Detalhes do Inventário #<span id="det-id">--</span></h5>
                <div class="ms-auto d-flex gap-2">
                    {{-- BOTÃO IMPRIMIR --}}
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 no-print" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Imprimir
                    </button>
                    <button type="button" class="btn-close no-print" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Responsável</label>
                        <span id="det-user" class="fw-bold text-dark">--</span>
                    </div>
                    <div class="col-6 text-end">
                        <label class="text-muted small text-uppercase fw-bold d-block">Data de Realização</label>
                        <span id="det-data" class="fw-bold text-dark">--</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th>LOTE</th>
                                <th class="text-center">SISTEMA</th>
                                <th class="text-center">APURADO</th>
                                <th class="text-center">DIFERENÇA</th>
                                <th>JUSTIFICATIVA</th>
                            </tr>
                        </thead>
                        <tbody id="det-body">
                            {{-- Preenchido via JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* 1. Esconde tudo o que não é o modal */
    body * {
        visibility: hidden;
    }

    /* 2. Garante que o modal e o conteúdo interno sejam visíveis */
    #modalHistoricoDetalhe, 
    #modalHistoricoDetalhe * {
        visibility: visible;
    }

    /* 3. Reseta o Modal para se comportar como uma folha de papel */
    #modalHistoricoDetalhe {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: block !important; /* Mata o flexbox do Bootstrap */
    }

    /* 4. Mata a centralização vertical e as margens do diálogo */
    #modalHistoricoDetalhe .modal-dialog {
        display: block !important;      /* Mata o flexbox que centraliza */
        margin: 0 !important;           /* Remove margens que empurram para baixo */
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        transform: none !important;     /* Remove qualquer deslocamento de animação */
        min-height: auto !important;    /* Garante que não tente ocupar a tela toda */
    }

    /* 5. Remove sombras e bordas do conteúdo */
    #modalHistoricoDetalhe .modal-content {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    /* 6. Ajustes de cabeçalho para parecer relatório */
    #modalHistoricoDetalhe .modal-header {
        border-bottom: 2px solid #000 !important;
        margin-bottom: 15px !important;
        padding: 0 0 10px 0 !important; /* Remove paddings laterais e foca no topo */
    }

    .no-print {
        display: none !important;
    }

    /* 7. Ajuste de tabelas para preencher a largura e mostrar cores */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .table-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
    }

    th, td {
        border-bottom: 1px solid #dee2e6 !important;
        padding: 8px !important;
    }
}
</style>

<script>
window.viewInventoryDetail = function(id) {
    $.ajax({
        url: `/green-coffee/inventory/history/${id}`,
        method: 'GET',
        success: function(data) {
            $('#det-id').text(data.session.id);
            $('#det-user').text(data.session.user ? data.session.user.name : 'Sistema');
            
            // Formatação de data preservada
            if(data.session.inventory_date) {
                const partes = data.session.inventory_date.split('-');
                $('#det-data').text(`${partes[2]}/${partes[1]}/${partes[0]}`);
            }
            
            let html = '';
            data.adjustments.forEach(adj => {
                // CORREÇÃO: Cálculo da diferença em tempo real para evitar o erro de mostrar 0.0kg
                const systemWeight = parseFloat(adj.system_weight) || 0;
                const physicalWeight = parseFloat(adj.physical_weight) || 0;
                const difference = physicalWeight - systemWeight;

                // Lógica de cores baseada na diferença calculada
                const diffCol = difference < 0 ? 'text-danger' : (difference > 0 ? 'text-primary' : 'text-success');
                const diffSignal = difference > 0 ? '+' : '';

                html += `
                    <tr>
                        <td><small class="fw-bold text-uppercase">${adj.reference}</small></td>
                        <td class="text-center small">${systemWeight.toFixed(1)}kg</td>
                        <td class="text-center small">${physicalWeight.toFixed(1)}kg</td>
                        <td class="text-center small fw-bold ${diffCol}">
                            ${diffSignal}${difference.toFixed(1)}kg
                        </td>
                        <td class="small text-muted italic">${adj.reason || '-'}</td>
                    </tr>`;
            });
            
            $('#det-body').html(html);

            // Abre o modal após carregar os dados
            var myModal = new bootstrap.Modal(document.getElementById('modalHistoricoDetalhe'));
            myModal.show();
        },
        error: function() {
            alert('Erro ao carregar detalhes do inventário.');
        }
    });
};
</script>