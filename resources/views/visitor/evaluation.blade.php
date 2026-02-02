<?php
/**
 * Q-Grader: Sistema de Avaliação SCA
 * Ajuste: Pop-up persistente ao arrastar
 */

$session = [
    'id' => 123,
    'title' => 'Mesa Especial Sul de Minas',
    'is_blind' => true,
    'users_count' => 3,
    'finished_users_count' => 2,
    'profile' => [
        'eval_fragrance' => 1, 'eval_flavor' => 1, 'eval_aftertaste' => 1,
        'eval_acidity' => 1, 'eval_body' => 1, 'eval_balance' => 1,
        'eval_uniformity' => 1, 'eval_clean_cup' => 1, 'eval_sweetness' => 1,
        'eval_overall' => 1, 'eval_defects' => 1, 'show_roast_level' => 1,
        'show_fragrance_scales' => 1, 'show_intensity_scales' => 1, 'show_defect_details' => 1
    ],
    'items' => [
        ['id' => 10, 'position' => 1, 'nickname' => 'Amostra A', 'notes' => 'Nota de teste', 'sample' => ['name' => 'Bourbon Amarelo', 'variety' => 'Catuaí']],
        ['id' => 11, 'position' => 2, 'nickname' => 'Amostra B', 'notes' => '', 'sample' => ['name' => 'Geisha', 'variety' => 'Geisha']]
    ]
];

$scaConfig = [
    'eval_fragrance' => ['label' => 'Aroma', 'field' => 'fragrance_aroma', 'flag' => 'show_fragrance_scales', 'subs' => [['label' => 'Seco', 'field' => 'fragrance_dry'], ['label' => 'Quebra', 'field' => 'fragrance_break']]],
    'eval_flavor' => ['label' => 'Sabor', 'field' => 'flavor'],
    'eval_aftertaste' => ['label' => 'Retrogosto', 'field' => 'aftertaste'],
    'eval_acidity' => ['label' => 'Acidez', 'field' => 'acidity', 'flag' => 'show_intensity_scales', 'subs' => [['label' => 'Intensidade', 'field' => 'acidity_intensity']]],
    'eval_body' => ['label' => 'Corpo', 'field' => 'body', 'flag' => 'show_intensity_scales', 'subs' => [['label' => 'Intensidade', 'field' => 'body_level']]],
    'eval_balance' => ['label' => 'Equilíbrio', 'field' => 'balance'],
    'eval_uniformity' => ['label' => 'Uniformidade', 'field' => 'uniformity'],
    'eval_clean_cup' => ['label' => 'Limpeza', 'field' => 'clean_cup'],
    'eval_sweetness' => ['label' => 'Doçura', 'field' => 'sweetness'],
    'eval_overall' => ['label' => 'Geral', 'field' => 'overall'],
    'eval_defects' => ['label' => 'Defeitos', 'field' => 'defects', 'flag' => 'show_defect_details']
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Q-Grader SCA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --coffee-brown: #4E342E; --coffee-light: #8D6E63; }
        body { background: white; font-family: -apple-system, sans-serif; padding-bottom: 60px; overflow-x: hidden; -webkit-tap-highlight-color: transparent; }
        
        .app-bar { height: 45px; display: flex; align-items: center; padding: 0 15px; border-bottom: 1px solid #eee; color: var(--coffee-brown); font-weight: bold; }
        
        .cup-selector { display: flex; overflow-x: auto; padding: 10px; gap: 10px; border-bottom: 1px solid #f0f0f0; background: #fff; scrollbar-width: none; }
        .cup-card { min-width: 95px; border: 1px solid #eee; border-radius: 12px; padding: 8px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fff; }
        .cup-card.active { background: rgba(78, 52, 46, 0.05); border: 2px solid var(--coffee-brown); }
        .cup-card .score { font-size: 11px; font-weight: 900; color: var(--coffee-brown); }

        .info-block { padding: 16px; border-bottom: 1.5px solid #eee; margin-bottom: 12px; display: flex; gap: 10px; background: #fff; }
        .notes-preview { background: #f8f9fa; border-radius: 8px; padding: 8px; font-size: 11px; font-style: italic; color: #666; flex: 1; max-height: 50px; overflow: hidden; }

        .attribute-block { margin-bottom: 30px; position: relative; }
        .attr-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .attr-label { font-weight: bold; font-size: 16px; color: #3E2723; }
        .attr-value { color: var(--coffee-brown); font-weight: 900; font-size: 22px; }

        /* Sliders */
        .range-container { position: relative; padding: 30px 0 10px 0; touch-action: none; }
        
        /* Tooltip Persistente */
        .slider-tooltip { 
            position: absolute; 
            top: -10px; 
            left: 0;
            background: var(--coffee-brown); 
            color: white;
            width: 40px; 
            height: 40px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg) translateX(-50%);
            display: flex; /* Mudado para flex mas controlado por opacidade */
            align-items: center; 
            justify-content: center;
            font-weight: bold; 
            font-size: 14px; 
            z-index: 10;
            pointer-events: none; 
            transition: left 0.05s linear, opacity 0.2s;
            opacity: 0; /* Começa invisível */
        }
        .slider-tooltip.visible { opacity: 1; }
        .slider-tooltip span { transform: rotate(45deg); display: block; }

        input[type=range] { 
            -webkit-appearance: none;
            width: 100%; 
            height: 12px; 
            border-radius: 10px; 
            background: #eee;
            outline: none;
        }
        
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            background: var(--coffee-brown);
            cursor: pointer;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Roast e Binários */
        .roast-circle { width: 45px; height: 45px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid transparent; }
        .roast-circle.selected { border-color: #0d6efd; transform: scale(1.1); }
        
        .binary-container { display: flex; justify-content: space-between; margin-top: 10px; }
        .cup-icon { width: 18%; height: 50px; border: 2px solid #ddd; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #ccc; cursor: pointer; }
        .cup-icon.marked { background: var(--coffee-brown); color: white; border-color: var(--coffee-brown); }

        .defects-box { background: #fff5f5; border: 1px solid #ffcccc; border-radius: 12px; padding: 15px; }
        .btn-circle { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #ffcccc; background: white; color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="app-bar">Mesa: <?= htmlspecialchars($session['title']) ?></div>

<div class="cup-selector" id="cupList">
    <?php foreach($session['items'] as $index => $item): ?>
    <div class="cup-card <?= $index === 0 ? 'active' : '' ?>" onclick="switchCup(<?= $index ?>)" id="card-<?= $index ?>">
        <div style="font-size: 8px; color: #999; font-weight: bold;">#<?= $index + 1 ?></div>
        <div style="font-size: 9px; color: #666; white-space: nowrap; overflow: hidden;">
            <?= $session['is_blind'] ? '----' : ($item['nickname'] ?: 'Xícara') ?>
        </div>
        <div style="font-size: 22px; margin: 4px 0;">☕</div>
        <div class="score" id="score-<?= $index ?>">0.00</div>
    </div>
    <?php endforeach; ?>
</div>

<div class="info-block">
    <div style="width: 40px; text-align: center;">
        <span class="info-badge" id="displayPosition">#1</span><br>
        <span style="font-size: 24px; color: #0d6efd;">ⓘ</span>
    </div>
    <div style="flex: 1">
        <div style="font-weight: bold; font-size: 15px;" id="displayName">----</div>
        <div style="color: var(--coffee-light); font-size: 11px;" id="displaySample">----</div>
    </div>
    <div class="notes-preview" id="displayNotes">----</div>
</div>

<div class="container mt-2">
    <form id="cuppingForm">
        <?php if($session['profile']['show_roast_level']): ?>
        <div class="p-3 bg-light rounded mb-4 border text-center">
            <label class="text-secondary fw-bold mb-2 small d-block">COR DA TORRA</label>
            <div class="d-flex justify-content-around">
                <?php 
                $roastColors = ['#99702A', '#623C14', '#332112', '#17100E'];
                foreach($roastColors as $i => $color): ?>
                    <div class="roast-circle" style="background: <?= $color ?>" onclick="setRoast(<?= $i ?>)" id="roast-<?= $i ?>"></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div id="scaAttributes">
            <?php foreach($scaConfig as $key => $attr): 
                if($session['profile'][$key]): ?>
                    <div class="attribute-block">
                        <div class="attr-header">
                            <span class="attr-label"><?= $attr['label'] ?></span>
                            <span class="attr-value" id="val-<?= $attr['field'] ?>">6.00</span>
                        </div>
                        
                        <?php if(in_array($attr['field'], ['uniformity', 'clean_cup', 'sweetness'])): ?>
                            <div class="binary-container">
                                <?php for($i=0; $i<5; $i++): ?>
                                    <div class="cup-icon" onclick="toggleBinary('<?= $attr['field'] ?>', <?= $i ?>)" data-field="<?= $attr['field'] ?>" data-index="<?= $i ?>">☕</div>
                                <?php endfor; ?>
                            </div>
                        <?php elseif($attr['field'] === 'defects'): ?>
                            <div class="defects-box mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div style="font-size:11px; font-weight:bold; color:#d9534f">XÍCARAS</div>
                                        <button type="button" class="btn-circle" onclick="updateDefectCups(-1)">-</button>
                                        <span id="defect-cups" class="mx-2 fw-bold fs-5">0</span>
                                        <button type="button" class="btn-circle" onclick="updateDefectCups(1)">+</button>
                                    </div>
                                    <div class="text-end">
                                        <div style="font-size:11px; font-weight:bold; color:#d9534f">INTENSIDADE</div>
                                        <div class="btn-group btn-group-sm">
                                            <input type="radio" class="btn-check" name="defect_int" id="int2" value="2" checked onchange="calcDefects()">
                                            <label class="btn btn-outline-danger" for="int2">2</label>
                                            <input type="radio" class="btn-check" name="defect_int" id="int4" value="4" onchange="calcDefects()">
                                            <label class="btn btn-outline-danger" for="int4">4</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-2 border-top border-danger pt-2">
                                    <small class="text-danger fw-bold" id="defect-total">Subtrair: -0.0</small>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="range-container">
                                <div class="slider-tooltip" id="tip-<?= $attr['field'] ?>"><span>6.0</span></div>
                                <input type="range" min="6" max="10" step="0.01" value="6" 
                                       oninput="handleSlider('<?= $attr['field'] ?>', this)" 
                                       onmousedown="toggleTip('<?= $attr['field'] ?>', true)"
                                       onmouseup="toggleTip('<?= $attr['field'] ?>', false)"
                                       ontouchstart="toggleTip('<?= $attr['field'] ?>', true)"
                                       ontouchend="toggleTip('<?= $attr['field'] ?>', false)">
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light border text-danger" onclick="nudge('<?= $attr['field'] ?>', -0.1)">-0.1</button>
                                    <button type="button" class="btn btn-sm btn-light border text-danger" onclick="nudge('<?= $attr['field'] ?>', -0.01)">-0.01</button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light border text-success" onclick="nudge('<?= $attr['field'] ?>', 0.01)">+0.01</button>
                                    <button type="button" class="btn btn-sm btn-light border text-success" onclick="nudge('<?= $attr['field'] ?>', 0.1)">+0.1</button>
                                </div>
                            </div>

                            <?php if(isset($attr['subs']) && $session['profile'][$attr['flag']]): ?>
                                <div class="mt-3 ms-3 border-start ps-3">
                                    <?php foreach($attr['subs'] as $sub): ?>
                                        <div class="mb-3 position-relative">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted fw-bold"><?= $sub['label'] ?></small>
                                                <small class="fw-bold" id="val-<?= $sub['field'] ?>">6.0</small>
                                            </div>
                                            <div class="range-container py-2">
                                                <div class="slider-tooltip" id="tip-<?= $sub['field'] ?>" style="height:32px; width:32px; font-size:11px;"><span>6.0</span></div>
                                                <input type="range" min="6" max="10" step="0.1" value="6" 
                                                       oninput="handleSlider('<?= $sub['field'] ?>', this)"
                                                       onmousedown="toggleTip('<?= $sub['field'] ?>', true)"
                                                       onmouseup="toggleTip('<?= $sub['field'] ?>', false)"
                                                       ontouchstart="toggleTip('<?= $sub['field'] ?>', true)"
                                                       ontouchend="toggleTip('<?= $sub['field'] ?>', false)">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <hr class="text-light">
                    </div>
            <?php endif; endforeach; ?>
        </div>

        <button type="button" class="btn btn-success w-100 fw-bold py-3 mb-5" onclick="finalize()">FINALIZAR PROVA</button>
    </form>
</div>

<script>
const session = <?= json_encode($session) ?>;
const config = <?= json_encode($scaConfig) ?>;
let currentIndex = 0;
let evals = [];

function init() {
    const saved = localStorage.getItem('cupping_draft_' + session.id);
    evals = saved ? JSON.parse(saved) : session.items.map(item => {
        let obj = { cupping_item_id: item.id, tags: '', observations: '', defects_cups: 0, defects_intensity: 2, defects: 0, roast_level: 0 };
        Object.values(config).forEach(c => {
            if(c.field !== 'defects') {
                obj[c.field] = ['uniformity', 'clean_cup', 'sweetness'].includes(c.field) ? 10.0 : 6.0;
                if(c.subs) c.subs.forEach(s => obj[s.field] = 6.0);
            }
        });
        return obj;
    });
    switchCup(0);
}

function switchCup(index) {
    currentIndex = index;
    document.querySelectorAll('.cup-card').forEach((c, i) => c.classList.toggle('active', i === index));
    const item = session.items[index];
    const data = evals[index];

    document.getElementById('displayPosition').innerText = '#' + item.position;
    document.getElementById('displayName').innerText = session.is_blind ? '----' : (item.nickname || 'Xícara');
    document.getElementById('displaySample').innerText = session.is_blind ? '----' : (item.sample.name || 'Avulso');
    document.getElementById('displayNotes').innerText = session.is_blind ? '----' : (item.notes || 'Sem notas.');

    Object.keys(data).forEach(key => {
        const span = document.getElementById('val-' + key);
        if(span) span.innerText = Number(data[key]).toFixed(['uniformity', 'clean_cup', 'sweetness'].includes(key) ? 0 : 2);
        const slider = document.querySelector(`input[oninput*="'${key}'"]`);
        if(slider) {
            slider.value = data[key];
            updateTooltip(key, data[key], slider);
        }
    });

    updateBinaryUI();
    setRoast(data.roast_level, false);
    updateDefectCups(0);
    updateAllScores();
}

// Lógica do Pop-up (Tooltip)
function toggleTip(field, show) {
    const tip = document.getElementById('tip-' + field);
    if(tip) {
        if(show) tip.classList.add('visible');
        else setTimeout(() => tip.classList.remove('visible'), 300);
    }
}

function handleSlider(field, el) {
    const val = parseFloat(el.value);
    evals[currentIndex][field] = val;
    document.getElementById('val-' + field).innerText = val.toFixed(field.includes('intensity') || field.includes('level') ? 1 : 2);
    updateTooltip(field, val, el);
    updateAllScores();
    saveDraft();
}

function updateTooltip(field, val, el) {
    const tip = document.getElementById('tip-' + field);
    if(!tip) return;
    tip.querySelector('span').innerText = val.toFixed(1);
    const percent = (val - el.min) / (el.max - el.min) * 100;
    tip.style.left = `calc(${percent}% + (${8 - percent * 0.15}px))`;
}

function nudge(field, delta) {
    let val = (evals[currentIndex][field] || 6.0) + delta;
    val = Math.min(Math.max(val, 6.0), 10.0);
    const slider = document.querySelector(`input[oninput*="'${field}'"]`);
    if(slider) {
        slider.value = val;
        toggleTip(field, true);
        handleSlider(field, slider);
        setTimeout(() => toggleTip(field, false), 800);
    }
}

// Outras funções
function toggleBinary(field, i) {
    let current = evals[currentIndex][field];
    let clicked = (i + 1) * 2.0;
    evals[currentIndex][field] = (current === clicked) ? i * 2.0 : clicked;
    updateBinaryUI();
    saveDraft();
}

function updateBinaryUI() {
    ['uniformity', 'clean_cup', 'sweetness'].forEach(f => {
        const val = evals[currentIndex][f] || 0;
        document.querySelectorAll(`.cup-icon[data-field="${f}"]`).forEach((icon, idx) => {
            icon.classList.toggle('marked', idx < Math.round(val / 2));
        });
        if(document.getElementById('val-' + f)) document.getElementById('val-' + f).innerText = val.toFixed(0);
    });
    updateAllScores();
}

function updateDefectCups(d) {
    evals[currentIndex].defects_cups = Math.min(Math.max(evals[currentIndex].defects_cups + d, 0), 5);
    document.getElementById('defect-cups').innerText = evals[currentIndex].defects_cups;
    calcDefects();
}

function calcDefects() {
    const e = evals[currentIndex];
    e.defects_intensity = parseInt(document.querySelector('input[name="defect_int"]:checked').value);
    e.defects = e.defects_cups * e.defects_intensity;
    document.getElementById('defect-total').innerText = "Subtrair: -" + e.defects.toFixed(1);
    updateAllScores();
    saveDraft();
}

function setRoast(i, save = true) {
    evals[currentIndex].roast_level = i;
    document.querySelectorAll('.roast-circle').forEach((c, idx) => c.classList.toggle('selected', idx === i));
    if(save) saveDraft();
}

function updateAllScores() {
    evals.forEach((ev, i) => {
        let total = 0;
        Object.values(config).forEach(c => { if(c.field !== 'defects') total += parseFloat(ev[c.field] || 0); });
        total -= parseFloat(ev.defects || 0);
        if(document.getElementById('score-' + i)) document.getElementById('score-' + i).innerText = total.toFixed(2);
    });
}

function saveDraft() {
    localStorage.setItem('cupping_draft_' + session.id, JSON.stringify(evals));
}

function finalize() {
    if(confirm("Deseja enviar a prova?")) {
        console.log(evals);
        localStorage.removeItem('cupping_draft_' + session.id);
        alert("Enviado!");
    }
}

init();
</script>
</body>
</html>