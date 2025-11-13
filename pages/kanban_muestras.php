<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>
<main id="main" class="main">
  <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
    <h2 style="margin:0;">Seguimiento de Muestras</h2>

    <input id="search" type="search" placeholder="Buscar ID / Número / Test"
           style="flex:1; padding:8px 10px; border-radius:10px; border:1px solid #ddd;">

    <select id="testFilter" style="padding:8px 10px; border-radius:10px; border:1px solid #ddd;">
      <option value="">Todos los ensayos</option>
    </select>

    <button id="refresh" style="padding:8px 14px; border-radius:10px; border:0; background:#111; color:#fff; cursor:pointer;">
      Actualizar
    </button>

    <button id="exportExcel"
            style="padding:8px 14px; border-radius:10px; border:0; background:#0a7; color:#fff; cursor:pointer; display:flex; align-items:center; gap:8px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M5 20h14v-2H5v2zm7-18-5.5 5.5 1.41 1.41L11 6.83V16h2V6.83l3.09 3.08 1.41-1.41L12 2z"/>
      </svg>
      Exportar Excel
    </button>
  </div>

  <div id="board" class="board-grid">
    <?php
      $columns = ['Registrado','#008FFB', 'Preparación','#FEB019', 'Realización','#00E396', 'Entrega','#775DD0'];
      for ($i=0; $i<count($columns); $i+=2):
        $name = $columns[$i]; $color = $columns[$i+1];
    ?>
      <section class="kan-col" data-status="<?php echo htmlspecialchars($name); ?>">
        <header class="kan-col-head" style="background:<?php echo $color; ?>;">
          <?php echo htmlspecialchars($name); ?>
          <span class="count" data-count-for="<?php echo htmlspecialchars($name); ?>">0</span>
        </header>
        <div class="dropzone" data-status="<?php echo htmlspecialchars($name); ?>"></div>
      </section>
    <?php endfor; ?>
  </div>
</main>

<div id="moveModal" class="modal hidden" aria-hidden="true">
  <div class="modal-body">
    <h3 class="modal-title">Mover Muestra</h3>
    <div class="modal-row">
      <label>Destino</label>
      <input id="mm_to" class="input" type="text" readonly>
    </div>
    <div class="modal-row">
      <label>Técnicos (separados por coma)</label>
      <input id="mm_techs" class="input" type="text">
    </div>
    <div class="modal-row">
      <label>Nota (opcional)</label>
      <textarea id="mm_note" class="input" rows="3" placeholder="Detalle de la transición..."></textarea>
    </div>
    <div class="modal-actions">
      <button id="mm_cancel" class="btn ghost">Cancelar</button>
      <button id="mm_ok" class="btn primary">Confirmar</button>
    </div>
  </div>
</div>

<style>
  .board-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; }
  .kan-col { background:#fff; border:1px solid #eee; border-radius:14px; overflow:hidden; display:flex; flex-direction:column; min-height:72vh; }
  .kan-col-head { color:#fff; padding:10px 12px; font-weight:600; display:flex; justify-content:space-between; align-items:center; }
  .kan-col .dropzone { padding:10px; display:flex; flex-direction:column; gap:10px; flex:1; min-height:120px; }
  .card { background:#fff; border:1px solid #eaeaea; border-radius:12px; padding:10px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
  .card h4 { margin:0 0 6px 0; font-size:14px; display:flex; gap:8px; align-items:center; justify-content:space-between; }
  .chip  { font-size:11px; padding:2px 8px; border-radius:10px; background:#f5f5f5; border:1px solid #eaeaea; }
  .sla   { font-size:11px; padding:2px 8px; border-radius:10px; color:#fff; background:#e11d48; }
  .meta  { font-size:12px; color:#666; display:flex; gap:8px; flex-wrap:wrap; margin-top:4px; }
  .dragging { opacity:.6; transform: scale(0.98); }
  .dropzone.over { outline:2px dashed #999; outline-offset:-6px; }

  .substage-row { margin-top:6px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
  .substage-label { font-size:11px; color:#555; }
  .substage-chip { font-size:11px; padding:2px 6px; border-radius:10px; border:1px solid #e5e7eb; background:#f9fafb; }
  .substage-chip.active { background:#111; color:#fff; border-color:#111; }

  .substage-select { font-size:11px; border-radius:8px; border:1px solid #ddd; padding:2px 6px; }

  .modal { position:fixed; inset:0; background:rgba(0,0,0,.45); display:flex; align-items:center; justify-content:center; z-index:9999; }
  .modal.hidden { display:none; }
  .modal-body { width:min(560px, 92vw); background:#fff; border-radius:16px; padding:16px; box-shadow:0 10px 30px rgba(0,0,0,.25); }
  .modal-title { margin:0 0 8px 0; }
  .modal-row { display:flex; flex-direction:column; gap:6px; margin:10px 0; }
  .input { border:1px solid #ddd; border-radius:10px; padding:8px 10px; }
  .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:8px; }
  .btn { padding:8px 14px; border-radius:10px; border:0; cursor:pointer; }
  .btn.primary { background:#111; color:#fff; }
  .btn.ghost { background:#f3f4f6; color:#111; }
  .count { background:rgba(255,255,255,.25); color:#fff; padding:2px 8px; border-radius:999px; font-size:12px; }

  @media (max-width: 1200px){ .board-grid{ grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 640px){ .board-grid{ grid-template-columns: 1fr; } }
</style>

<script>
const $search     = document.getElementById('search');
const $testFilter = document.getElementById('testFilter');
const $refresh    = document.getElementById('refresh');

const $moveModal  = document.getElementById('moveModal');
const $mm_to      = document.getElementById('mm_to');
const $mm_techs   = document.getElementById('mm_techs');
const $mm_note    = document.getElementById('mm_note');
const $mm_cancel  = document.getElementById('mm_cancel');
const $mm_ok      = document.getElementById('mm_ok');

let cache = null;
let pendingMove = null;

const SUBSTAGES = {
  'Preparación': ['P1','P2','P3','P4'],
  'Realización': ['R1','R2','R3','R4'],
  'Entrega':     ['E1']
};

function labelSubStage(code) {
  switch (code) {
    case 'P1': return 'P1 – Cuarteo';
    case 'P2': return 'P2 – Secado';
    case 'P3': return 'P3 – Lavado';
    case 'P4': return 'P4 – Otro';
    case 'R1': return 'R1 – Secado';
    case 'R2': return 'R2 – Tamizado';
    case 'R3': return 'R3 – Ejecucion';
    case 'R4': return 'R4 – Pesaje';
    case 'E1': return 'E1 – Entregada';
    default:   return code || '—';
  }
}


function escapeHtml(s) {
  return (s ?? '').toString().replace(/[&<>"']/g, function(m) {
    return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m];
  });
}

function debounce(fn,ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; }

function fetchData() {
  const q = encodeURIComponent($search.value.trim());
  const t = encodeURIComponent($testFilter.value);
  const url = `/api/kanban_list.php?q=${q}&test=${t}`;
  return fetch(url, { credentials:'same-origin' })
    .then(r => r.text())
    .then(txt => {
      try { return JSON.parse(txt); }
      catch { console.error('BAD JSON', txt); return { ok:false, error:'BAD_JSON', raw:txt }; }
    })
    .catch(err => ({ ok:false, error:String(err) }));
}

function move(id, toStatus, technicians, note) {
  return fetch('/api/kanban_move.php', {
    method:'POST',
    headers:{ 'Content-Type':'application/json' },
    credentials:'same-origin',
    body: JSON.stringify({ id, to: toStatus, technicians, note })
  })
  .then(r => r.text())
  .then(txt => {
    try { return JSON.parse(txt); }
    catch { console.error('BAD JSON', txt); return { ok:false, error:'BAD_JSON', raw:txt }; }
  })
  .catch(err => ({ ok:false, error:String(err) }));
}

function changeSubStage(id, subStage) {
  // si luego quieres manejar técnico por sub-etapa,
  // aquí se puede llamar a un endpoint separado.
  return fetch('../api/kanban_substage.php', {
    method:'POST',
    headers:{ 'Content-Type':'application/json' },
    credentials:'same-origin',
    body: JSON.stringify({ id, sub_stage: subStage })
  })
  .then(r => r.text())
  .then(txt => {
    try { return JSON.parse(txt); }
    catch { console.error('BAD JSON', txt); return { ok:false, error:'BAD_JSON', raw:txt }; }
  })
  .catch(err => ({ ok:false, error:String(err) }));
}

function render(data) {
  cache = data;
  document.querySelectorAll('.dropzone').forEach(z => z.innerHTML = '');
  document.querySelectorAll('.count').forEach(s => s.textContent = '0');

  if (!data || !data.ok) {
    const any = document.querySelector('.dropzone');
    if (any) {
      any.innerHTML = `<div style="padding:12px;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;border-radius:10px;">
        Error/ vacío: ${escapeHtml((data && data.error) ? data.error : 'Sin datos')}
      </div>`;
    }
    return;
  }

  if ($testFilter.options.length === 1) {
    const set = new Set();
    for (const col of Object.keys(data.data)) {
      (data.data[col]||[]).forEach(it => set.add(it.Test_Type));
    }
    [...set].sort().forEach(tt => {
      const opt = document.createElement('option');
      opt.value = tt;
      opt.textContent = tt;
      $testFilter.appendChild(opt);
    });
  }

  const statuses = ['Registrado','Preparación','Realización','Entrega'];
  statuses.forEach(status => {
    const zone = document.querySelector(`.dropzone[data-status="${status}"]`);
    let count = 0;
    (data.data[status] || []).forEach(it => {
      const card = document.createElement('article');
      card.className = 'card';
      card.draggable = true;
      card.dataset.id = it.id;

      const subList = SUBSTAGES[status] || [];  
      const currentSub = it.Sub_Stage || '';

      let subChips = '';
      if (subList.length > 0) {
        subChips = `
          <div class="substage-row">
            <span class="substage-label">Sub-etapa:</span>
            ${subList.map(code => `
              <span class="substage-chip ${code === currentSub ? 'active' : ''}">
                ${escapeHtml(labelSubStage(code))}
              </span>
            `).join('')}
            <select class="substage-select" data-id="${it.id}">
              <option value="">Cambiar…</option>
              ${subList.map(code => `
                <option value="${code}" ${code === currentSub ? 'selected' : ''}>
                  ${escapeHtml(labelSubStage(code))}
                </option>
              `).join('')}
            </select>
          </div>
        `;
      }

      card.innerHTML = `
        <h4>
          <span>${escapeHtml(it.Sample_ID)}</span>
          <span class="chip">${escapeHtml(it.Test_Type)}</span>
          ${it.Alert ? '<span class="sla">SLA</span>' : ''}
        </h4>
        <div class="meta">
          <span class="chip"># ${escapeHtml(it.Sample_Number || '—')}</span>
          <span class="chip">Desde: ${escapeHtml(it.Since || '')}</span>
          <span class="chip">Por: ${escapeHtml(it.Updated_By || '—')}</span>
          <span class="chip" title="Horas en estado / Límite SLA">${it.Dwell_Hours}h / ${it.SLA_Hours}h</span>
        </div>
        ${subChips}
      `;

      card.addEventListener('dragstart', ev => {
        ev.dataTransfer.setData('text/plain', it.id);
        setTimeout(() => card.classList.add('dragging'), 0);
      });
      card.addEventListener('dragend', () => card.classList.remove('dragging'));

      zone.appendChild(card);
      count++;
    });
    const $count = document.querySelector(`.count[data-count-for="${status}"]`);
    if ($count) $count.textContent = String(count);
  });

  document.querySelectorAll('.substage-select').forEach(sel => {
    sel.addEventListener('change', () => {
      const id = sel.getAttribute('data-id');
      const val = sel.value;
      if (!id || !val) return;
      changeSubStage(id, val).then(res => {
        if (!res || !res.ok) {
          alert('No se pudo actualizar la sub-etapa: ' + (res && res.error ? res.error : 'Error desconocido'));
        } else {
          fetchData().then(render);
        }
      });
    });
  });
}

document.querySelectorAll('.dropzone').forEach(zone => {
  zone.addEventListener('dragover', ev => { ev.preventDefault(); zone.classList.add('over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('over'));
  zone.addEventListener('drop', ev => {
    ev.preventDefault();
    zone.classList.remove('over');
    const id = ev.dataTransfer.getData('text/plain');
    const to = zone.dataset.status;
    const el = document.querySelector(`[data-id="${CSS.escape(id)}"]`);
    openMoveModal({ id, to, el });
  });
});

function openMoveModal(pm){
  pendingMove = pm;
  $mm_to.value = pm.to;
  $mm_techs.value = '';
  $mm_note.value = '';
  $moveModal.classList.remove('hidden');
  $moveModal.setAttribute('aria-hidden','false');
}
function closeMoveModal(){
  $moveModal.classList.add('hidden');
  $moveModal.setAttribute('aria-hidden','true');
  pendingMove = null;
}
$mm_cancel.addEventListener('click', closeMoveModal);
$mm_ok.addEventListener('click', () => {
  if (!pendingMove) return closeMoveModal();
  const techs = $mm_techs.value.split(',').map(s => s.trim()).filter(Boolean);
  const note  = $mm_note.value.trim();
  try {
    if (pendingMove.el) {
      const zone = document.querySelector(`.dropzone[data-status="${pendingMove.to}"]`);
      zone && zone.prepend(pendingMove.el);
    }
  } catch(e){ }
  move(pendingMove.id, pendingMove.to, techs, note)
    .then(res => {
      if (!res || !res.ok) {
        alert('No se pudo mover: ' + (res && res.error ? res.error : 'Error desconocido'));
      }
    })
    .catch(err => alert('Error de red: ' + err))
    .finally(() => fetchData().then(render));
  closeMoveModal();
});

$search.addEventListener('input', debounce(()=> fetchData().then(render), 300));
$testFilter.addEventListener('change', ()=> fetchData().then(render));
$refresh.addEventListener('click', ()=> fetchData().then(render));

fetchData().then(render);
</script>

<script>
  (function () {
    const $ = (id) => document.getElementById(id);

    const buildExcelUrl = () => {
      const params = new URLSearchParams();
      const q = ($('search')?.value || '').trim();
      const test = ($('testFilter')?.value || '').trim();

      if (q)    params.set('q', q);
      if (test) params.set('test', test);

      ['anio','mes','cliente','proyecto'].forEach(id => {
        const el = $(id);
        if (el && el.value && String(el.value).trim() !== '') {
          params.set(id, el.value.trim());
        }
      });

      const base = '../pages/sumary/export_workflow_excel.php';
      return params.toString() ? `${base}?${params.toString()}` : base;
    };

    $('exportExcel')?.addEventListener('click', () => {
      const url = buildExcelUrl();
      window.open(url, '_blank');
    });
  })();
</script>

<?php include_once('../components/footer.php'); ?>
