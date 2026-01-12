<svg width="100%" height="100%" viewBox="0 0 1780 1885" fill="none"  xmlns="http://www.w3.org/2000/svg" style="background: #F7F3F0; border-radius: 20px;" class="no-select">
    {{-- CSS PARA IMPEDIR SELEÇÃO VISUAL --}}
    <style>
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            outline: none;
            -webkit-tap-highlight-color: transparent;
        }
        .slot-group {
            outline: none;
        }
    </style>

    {{-- FUNDO --}}
    <path d="M1778 2V1883H2V2H1778Z" fill="#EBE3D9" stroke="#402318" stroke-width="4"/>

    {{-- ESTRUTURA FIXA (PORTAS E BALCÕES) --}}
    <g id="estrutura-fixa">
        <rect x="1758" y="1000" width="17" height="165" fill="#8B7E74" stroke="#402318" stroke-width="4" rx="4"/> {{-- Porta Lateral direita --}}
        <rect x="2" y="1350" width="17" height="495" fill="#8B7E74" stroke="#402318" stroke-width="4" rx="4"/> {{-- Porta Lateral esquerda --}}
        <rect x="60" y="1860" width="877" height="17" fill="#8B7E74" stroke="#402318" stroke-width="4" rx="4"/> {{-- Porta Frontal --}}
        <rect x="2" y="1180" width="609" height="148" fill="#8B7E74" stroke="#402318" stroke-width="4" rx="10"/> {{-- Balcão Esq --}}
        <rect x="1073" y="1180" width="702" height="148" fill="#8B7E74" stroke="#402318" stroke-width="4" rx="10"/> {{-- Balcão Dir --}}
    </g>

    {{-- MAQUINÁRIO --}}
    <g id="maquinario">
        {{-- Torrador Maior Probat (Esquerda) --}}
        <rect x="360" y="350" width="407" height="566" fill="#8B7E74" stroke="#BFA37E" stroke-width="6" rx="20"/> {{-- Retângulo principal --}}
        <ellipse cx="563" cy="900" rx="207.5" ry="197" fill="#8B7E74"/> {{-- Circulo da frente/resfriador --}}
        <rect x="360" y="40" width="407" height="220" fill="#8B7E74" stroke="#BFA37E" stroke-width="4" rx="15"/> {{-- Parte de trás do torrador --}}
        <rect x="600" y="255" width="100" height="100" fill="#8B7E74" /> {{-- Tubo que conecta a parte de trás do torrador --}}
        
        {{-- Balança --}}
        <rect x="775" y="765" width="140" height="140" fill="#8B7E74" stroke="#402318" stroke-width="4" rx="10"/>

        {{-- Torrador Menor CarmoMaq (Direita) --}}
        <rect x="1170" y="330" width="311" height="535" fill="#8B7E74" stroke="#BFA37E" stroke-width="6" rx="20"/>
        <ellipse cx="1325" cy="850" rx="159.5" ry="164.5" fill="#8B7E74"/>
    </g>

    {{-- PALETES DINÂMICOS --}}
    @php
        $offX = 450; 
        $offY = 550;

        // AJUSTE PARA EVITAR O ERRO DE REDECLARAÇÃO
        if (!function_exists('renderSlot')) {
            function renderSlot($id, $x, $y, $w, $h, $mapData, $offX, $offY) {
                $lots = $mapData[$id] ?? [];
                
                // Pega cores únicas dos cafés neste endereço
                $uniqueColors = array_values(array_unique(array_column($lots, 'color')));
                $colorCount = count($uniqueColors);
                
                $fillColor = '#D4B996'; // Cor padrão (vazio)
                $defs = "";

                if ($colorCount === 1) {
                    $fillColor = $uniqueColors[0];
                } elseif ($colorCount > 1) {
                    $gradId = "grad-" . $id;
                    $fillColor = "url(#{$gradId})";
                    
                    // Geração dinâmica de faixas de cores proporcionais (Hard Stops)
                    $stops = "";
                    $step = 100 / $colorCount;
                    
                    for ($i = 0; $i < $colorCount; $i++) {
                        $start = $i * $step;
                        $end = ($i + 1) * $step;
                        $color = $uniqueColors[$i];
                        
                        // Dois pontos com a mesma porcentagem criam a linha nítida entre as cores
                        $stops .= "<stop offset='{$start}%' stop-color='{$color}' />";
                        $stops .= "<stop offset='{$end}%' stop-color='{$color}' />";
                    }

                    $defs = "<defs>
                                <linearGradient id='{$gradId}' x1='0' y1='0' x2='1' y2='1'>
                                    {$stops}
                                </linearGradient>
                             </defs>";
                }

                $finalX = $x + $offX;
                $finalY = $y + $offY;

                echo $defs;
                echo "<g id='pallet-{$id}' class='slot-group no-select' style='cursor: pointer;'>";
                echo "<rect x='{$finalX}' y='{$finalY}' width='{$w}' height='{$h}' fill='{$fillColor}' stroke='#402318' stroke-width='4' rx='15' class='pallet-rect' />";
                
                // TEXTO COM BORDA BRANCA E COR 100% PRETO - Ajustado com pointer-events: none
                echo "<text x='".($finalX + $w/2)."' y='".($finalY + $h/2 + 30)."' 
                        font-family='Inter, sans-serif' font-size='110' font-weight='normal' text-anchor='middle' 
                        fill='#ffffff' stroke='#000000' stroke-width='3.7' 
                        style='pointer-events: none; paint-order: stroke; stroke-linejoin: round; user-select: none;'>{$id}</text>";
                
                echo "</g>";
            }
        }
    @endphp

    {{-- Renderização por fileiras conforme seu alinhamento --}}
    {{-- Fileira A --}}
    @php
        renderSlot('A1', -430, 450, 248, 168, $mapData, $offX, $offY);
        renderSlot('A2', -430, 273, 248, 168, $mapData, $offX, $offY);
        renderSlot('A3', -430, 96, 248, 168, $mapData, $offX, $offY);
        renderSlot('A4', -430, -81, 248, 168, $mapData, $offX, $offY);
        renderSlot('A5', -430, -258, 248, 168, $mapData, $offX, $offY);

        // Fileira B
        renderSlot('B1', -430, -517, 161, 250, $mapData, $offX, $offY);
        renderSlot('B2', -261, -517, 161, 250, $mapData, $offX, $offY);

        // Fileira C
        renderSlot('C3', 327, -517, 361, 247, $mapData, $offX, $offY);
        renderSlot('C2', 697, -517, 361, 247, $mapData, $offX, $offY);
        renderSlot('C1', 1067, -517, 248, 247, $mapData, $offX, $offY);

        // Fileira D
        renderSlot('D4', 1067, -260, 248, 168, $mapData, $offX, $offY);
        renderSlot('D3', 1067, -83, 248, 168, $mapData, $offX, $offY);
        renderSlot('D2', 1067, 94, 248, 168, $mapData, $offX, $offY);
        renderSlot('D1', 1067, 271, 248, 168, $mapData, $offX, $offY);

        // Fileira E
        renderSlot('E1', 323, -61, 142, 253, $mapData, $offX, $offY);
        renderSlot('E2', 473, -61, 161, 253, $mapData, $offX, $offY);
        renderSlot('E3', 473, 200, 161, 164, $mapData, $offX, $offY);

        // Fileira F 
        renderSlot('F1', 551, 788, 249, 165, $mapData, $offX, $offY);
        renderSlot('F2', 809, 788, 249, 165, $mapData, $offX, $offY);
        renderSlot('F3', 1067, 788, 249, 165, $mapData, $offX, $offY);
        renderSlot('F4', 1067, 963, 249, 165, $mapData, $offX, $offY);
        renderSlot('F5', 1067, 1140, 249, 165, $mapData, $offX, $offY);
        renderSlot('F6', 809, 1140, 249, 165, $mapData, $offX, $offY);
        renderSlot('F7', 551, 1140, 249, 165, $mapData, $offX, $offY);
    @endphp
</svg>