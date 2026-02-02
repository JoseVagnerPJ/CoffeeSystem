<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: a4 landscape; }
        body { margin: 0; padding: 0; font-family: 'Helvetica', sans-serif; }
        
        .page-container {
            position: relative;
            width: 297mm;
            height: 210mm;
            background: url('{{ public_path('images/sca_background.png') }}') no-repeat;
            background-size: cover;
        }

        .data {
            position: absolute;
            font-weight: bold;
            color: #000;
        }

        /* Coordenadas aproximadas baseadas no formulário SCA [cite: 5, 7, 8] */
        .provador-nome { top: 68px; left: 115px; font-size: 14px; }
        .data-sessao { top: 68px; left: 880px; font-size: 14px; }
        
        /* Notas dos Atributos Principais [cite: 8-16] */
        .aroma { top: 155px; left: 242px; width: 40px; text-align: center; }
        .sabor { top: 155px; left: 345px; width: 40px; text-align: center; }
        .retrogosto { top: 155px; left: 450px; width: 40px; text-align: center; }
        .acidez { top: 155px; left: 555px; width: 40px; text-align: center; }
        .corpo { top: 155px; left: 660px; width: 40px; text-align: center; }
        .equilibrio { top: 155px; left: 765px; width: 40px; text-align: center; }

        .total-score { top: 155px; left: 955px; font-size: 20px; color: #4b3621; }

        /* Marcas "X" nas réguas verticais (Intensidade) [cite: 69-74] */
        .x-mark { position: absolute; color: #d9534f; font-size: 18px; }
    </style>
</head>
<body>
    @foreach($evaluations as $cup)
    <div class="page-container" style="{{ !$loop->first ? 'page-break-before: always;' : '' }}">
        {{-- Cabeçalho --}}
        <div class="data provador-nome">{{ $user->name }}</div>
        <div class="data data-sessao">{{ $mesa->session_date->format('d/m/Y') }}</div>

        {{-- Notas Atributos [cite: 18-24] --}}
        <div class="data aroma">{{ number_format($cup->fragrance_aroma, 2) }}</div>
        <div class="data sabor">{{ number_format($cup->flavor, 2) }}</div>
        <div class="data retrogosto">{{ number_format($cup->aftertaste, 2) }}</div>
        <div class="data acidez">{{ number_format($cup->acidity, 2) }}</div>
        <div class="data corpo">{{ number_format($cup->body, 2) }}</div>
        <div class="data equilibrio">{{ number_format($cup->balance, 2) }}</div>

        {{-- Intensidades (Réguas Verticais) [cite: 122, 187] --}}
        {{-- O cálculo do 'top' posiciona o X na altura certa da régua --}}
        <div class="x-mark" style="top: {{ 280 - ($cup->body_level * 18) }}px; left: 642px;">X</div>
        <div class="x-mark" style="top: {{ 280 - ($cup->acidity_intensity * 18) }}px; left: 537px;">X</div>

        {{-- Pontuação Final [cite: 97, 140] --}}
        <div class="data total-score">{{ number_format($cup->final_score, 2) }}</div>
    </div>
    @endforeach
</body>
</html>