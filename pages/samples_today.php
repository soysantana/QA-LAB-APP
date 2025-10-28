<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');
date_default_timezone_set('America/Santo_Domingo');

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Endpoint API
const NEXT_API = '../api/samples_today_and_next.php';

// Prefijos base (SIN año). La UI puede añadir -yy automáticamente.
$prefijos_base = [
  'SD3-258',
  'LLD-258',
  'S15',
  '265',
  'PVDJ-AGG',
  'PVDJ-AGG-INV',
  'PVDJ-AGG-DIO',
  'PVDJ-AGG-CF',
  'PVDJ-AGG-FF',
];
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Consecutivo por Prefijo (con año) + Lista de Hoy</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/pages/home.php">Home</a></li>
        <li class="breadcrumb-item active">Consecutivo</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row g-3">

      <!-- Bloque: Siguiente consecutivo -->
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-hash"></i>
              <strong>Buscar último y sugerir siguiente</strong>
            </div>
            <span class="text-muted small">API: <?= e(NEXT_API) ?></span>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Prefijo base (sin año)</label>
                <select id="baseSelect" class="form-select">
                  <option value="">-- Selecciona --</option>
                  <?php foreach ($prefijos_base as $p): ?>
                    <option value="<?= e($p) ?>"><?= e($p) ?></option>
                  <?php endforeach; ?>
                </select>
                <input id="baseInput" type="text" class="form-control mt-2" placeholder="O escribe: ej. SD3-258">
              </div>
              <div class="col-md-2">
                <label class="form-label">Agregar año (-yy)</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="autoYear" checked>
                  <label class="form-check-label" for="autoYear">Activado</label>
                </div>
              </div>
              <div class="col-md-2">
                <label class="form-label">Padding</label>
                <input id="padLen" type="number" class="form-control" value="4" min="1" max="10">
              </div>
              <div class="col-md-4 d-grid">
                <button id="btnNext" class="btn btn-primary"><i class="bi bi-search"></i> Obtener siguiente</button>
              </div>
            </div>

            <div id="errNext" class="alert alert-danger mt-3 d-none"></div>

            <div id="boxNext" class="row g-3 mt-3 d-none">
              <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                  <div class="fw-bold mb-2">Última encontrada</div>
                  <div class="mb-1"><b>Columna:</b> <span id="lastCol">–</span></div>
                  <div class="mb-1"><b>Valor:</b> <span id="lastVal">–</span></div>
                  <div class="mb-1"><b>Sufijo #:</b> <span id="lastN">–</span></div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                  <div class="fw-bold mb-2">Sugerencia</div>
                  <div class="mb-1"><b>Prefijo resuelto:</b> <span id="resolvedPrefix">–</span></div>
                  <div class="mb-1"><b>Próximo #:</b> <span id="nextN">–</span></div>
                  <div class="mb-1"><b>Padding:</b> <span id="nextPad">–</span></div>
                  <div class="mb-1"><b>Usar:</b> <code id="useThis">–</code></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Bloque: Lista de hoy + busqueda -->
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-calendar-day"></i>
              <strong>Registradas hoy</strong>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-6">
                <label class="form-label">Buscar (cliente, ID, número, material…)</label>
                <input id="quickSearch" class="form-control" placeholder="Escribe para filtrar la tabla en vivo">
              </div>
              <div class="col-md-4">
                <label class="form-label">Filtrar por prefijo base (opcional)</label>
                <div class="input-group">
                  <select id="todayBaseSelect" class="form-select">
                    <option value="">-- Todos --</option>
                    <?php foreach ($prefijos_base as $p): ?>
                      <option value="<?= e($p) ?>"><?= e($p) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button id="btnTodayFilter" class="btn btn-outline-primary"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="todayAutoYear">
                  <label class="form-check-label" for="todayAutoYear">Agregar año (-yy) al filtro</label>
                </div>
              </div>
              <div class="col-md-2 d-grid">
                <button id="btnTodayReload" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> Actualizar</button>
              </div>
            </div>

            <div id="errToday" class="alert alert-danger mt-3 d-none"></div>

            <div class="table-responsive mt-3">
              <table class="table table-sm table-striped align-middle" id="todayTable">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Sample Name</th>
                    <th>Sample ID</th>
                    <th>Sample Number</th>
                    <th>Test Type</th>
                    <th>Material</th>
                    <th>Registered</th>
                  </tr>
                </thead>
                <tbody id="todayBody">
                  <tr><td colspan="7" class="text-center text-muted">Cargando…</td></tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>
</main>

<script>
(function(){
  const $ = s=>document.querySelector(s);

  // --- NEXT ---
  const baseSel  = $('#baseSelect');
  const baseInp  = $('#baseInput');
  const autoYear = $('#autoYear');
  const padLen   = $('#padLen');
  const btnNext  = $('#btnNext');
  const errNext  = $('#errNext');
  const boxNext  = $('#boxNext');

  const lastCol = $('#lastCol'), lastVal = $('#lastVal'), lastN = $('#lastN');
  const resolvedPrefix = $('#resolvedPrefix'), nextN = $('#nextN'), nextPad = $('#nextPad'), useThis = $('#useThis');

  baseSel?.addEventListener('change', ()=>{ baseInp.value = baseSel.value || ''; });

  btnNext?.addEventListener('click', async ()=>{
    const base = (baseInp.value || baseSel.value || '').trim();
    if (!base) return showErr(errNext, 'Debes indicar un prefijo base (sin año).');
    await getNext(base, autoYear.checked, Math.max(1, parseInt(padLen.value||'4',10)));
  });

  async function getNext(basePrefix, addYear, pad){
    try{
      errNext.classList.add('d-none');
      const url = `<?= e(NEXT_API) ?>?action=next&prefix=${encodeURIComponent(basePrefix)}&auto_year=${addYear?1:0}&pad=${pad}`;
      const res = await fetch(url, { headers:{Accept:'application/json'}, credentials:'same-origin' });
      const ct  = (res.headers.get('content-type')||'').toLowerCase();
      const txt = await res.text();
      if (!ct.includes('application/json')) return showErr(errNext,'El endpoint devolvió HTML (login/notice).', txt);
      let data; try{ data=JSON.parse(txt);}catch{ return showErr(errNext,'JSON inválido.', txt); }
      if (!res.ok || !data.ok) return showErr(errNext, data?.error || `HTTP ${res.status}`);
      // pintar
      lastCol.textContent = data.last_found?.from_column ?? '—';
      lastVal.textContent = data.last_found?.value ?? '—';
      lastN.textContent   = (typeof data.last_found?.suffix_n==='number') ? data.last_found.suffix_n : '—';

      resolvedPrefix.textContent = data.params?.resolved ?? '—';
      nextN.textContent   = data.suggestion?.next_n ?? '—';
      nextPad.textContent = data.suggestion?.next_padded ?? '—';
      useThis.textContent = data.suggestion?.use_this ?? '—';

      boxNext.classList.remove('d-none');
    }catch(e){
      showErr(errNext, e.message || 'Error desconocido.');
    }
  }

  // --- TODAY ---
  const qsearch = $('#quickSearch');
  const todaySel= $('#todayBaseSelect');
  const todayAY = $('#todayAutoYear');
  const btnFilt = $('#btnTodayFilter');
  const btnRld  = $('#btnTodayReload');
  const errToday= $('#errToday');
  const tbody   = $('#todayBody');

  btnRld?.addEventListener('click', ()=> loadToday(null, false));
  btnFilt?.addEventListener('click', ()=>{
    const base = (todaySel.value || '').trim() || null;
    loadToday(base, todayAY.checked);
  });

  qsearch?.addEventListener('input', ()=> filterTable(qsearch.value.trim().toLowerCase()));

  async function loadToday(basePrefix=null, addYear=false){
    try{
      errToday.classList.add('d-none');
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Cargando…</td></tr>';
      let url = `<?= e(NEXT_API) ?>?action=today`;
      if (basePrefix) url += `&prefix=${encodeURIComponent(basePrefix)}&auto_year=${addYear?1:0}`;

      const res = await fetch(url, { headers:{Accept:'application/json'}, credentials:'same-origin' });
      const ct  = (res.headers.get('content-type')||'').toLowerCase();
      const txt = await res.text();
      if (!ct.includes('application/json')) return showErr(errToday,'El endpoint devolvió HTML (login/notice).', txt);
      let data; try{ data=JSON.parse(txt);}catch{ return showErr(errToday,'JSON inválido.', txt); }
      if (!res.ok || !data.ok) return showErr(errToday, data?.error || `HTTP ${res.status}`);

      const rows = Array.isArray(data.today) ? data.today : [];
      if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Sin registros hoy.</td></tr>';
        return;
      }
      tbody.innerHTML = rows.map((r, i)=>{
        const id  = (r.Sample_ID ?? '').toString();
        const num = (r.Sample_Number ?? '').toString();
        const name= `${id} | ${num}`;
        const tt  = (r.Test_Type ?? '').toString();
        const mt  = (r.Material_Type ?? '').toString();
        const rd  = (r.Registed_Date ?? '').toString();
        return `<tr>
          <td>${i+1}</td>
          <td>${escapeHtml(name)}</td>
          <td>${escapeHtml(id)}</td>
          <td>${escapeHtml(num)}</td>
          <td>${escapeHtml(tt)}</td>
          <td>${escapeHtml(mt)}</td>
          <td>${escapeHtml(rd)}</td>
        </tr>`;
      }).join('');
      filterTable(qsearch.value.trim().toLowerCase());
    }catch(e){
      showErr(errToday, e.message || 'Error desconocido.');
    }
  }

  function filterTable(q){
    const rows = Array.from(tbody.querySelectorAll('tr'));
    if (!rows.length) return;
    let shown = 0;
    rows.forEach(tr=>{
      const txt = tr.innerText.toLowerCase();
      const vis = !q || txt.includes(q);
      tr.style.display = vis ? '' : 'none';
      if (vis) shown++;
    });
    if (shown===0) {
      tbody.insertAdjacentHTML('beforeend', '<tr data-empty="1"><td colspan="7" class="text-center text-muted">Sin coincidencias</td></tr>');
    } else {
      tbody.querySelectorAll('tr[data-empty="1"]').forEach(n=>n.remove());
    }
  }

  function showErr(box, msg, detail){
    box.classList.remove('d-none');
    box.innerHTML = detail
      ? `${msg}<hr><div class="small border p-2" style="max-height:260px;overflow:auto;white-space:pre-wrap;">${escapeHtml(detail)}</div>`
      : msg;
  }
  function escapeHtml(s){
    return s.replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
  }

  // Cargas iniciales
  loadToday(null, false);
})();
</script>

<?php include_once('../components/footer.php'); ?>
