<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
date_default_timezone_set('America/Santo_Domingo');

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// RUTA DEL ENDPOINT (misma carpeta del API)
$NEXT_API = '../api/samples_today_and_next.php';

// Prefijos base (SIN año pegado; la UI aplica la regla en el endpoint)
$prefijos = [
  'PVDJ-AGG',
  'PVDJ-AGG-INV',
  'PVDJ-AGG-DIO',
  'LBOR',
  'PVDJ-MISC',
  'LLD-258',
  'SD3-258',
  'SD2-258',
  'SD1-258',
];
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Consecutivo por Prefijo + Listas (Hoy / Semana)</h1>
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
              <i class="bi bi-123"></i>
              <strong>Obtener Siguiente (considerando reglas de año)</strong>
            </div>
            <span class="text-muted small">API: <?= e($NEXT_API) ?></span>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-6">
                <label class="form-label">Prefijo base</label>
                <select id="prefSelect" class="form-select">
                  <option value="">-- Selecciona --</option>
                  <?php foreach($prefijos as $p): ?>
                    <option value="<?= e($p) ?>"><?= e($p) ?></option>
                  <?php endforeach; ?>
                </select>
                <input id="prefInput" type="text" class="form-control mt-2" placeholder="O escribe: ej. PVDJ-AGG o LLD-258">
                <div class="form-text">
                
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
                  <div class="mb-1"><b>Numero de Muestra:</b> <span id="lastVal">–</span></div>
                  <
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

      <!-- Bloque: Registradas HOY -->
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
                <label class="form-label">Búsqueda rápida</label>
                <input id="quickSearch" class="form-control" placeholder="Escribe para filtrar (ID, número, test, material...)">
              </div>
              <div class="col-md-4">
                <label class="form-label">Filtrar por prefijo base</label>
                <div class="input-group">
                  <select id="todaySelect" class="form-select">
                    <option value="">-- Todos --</option>
                    <?php foreach($prefijos as $p): ?>
                      <option value="<?= e($p) ?>"><?= e($p) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button id="btnTodayFilt" class="btn btn-outline-primary"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="form-text">
                  Se aplican las mismas reglas de año que arriba.
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

      <!-- Bloque: Registradas ESTA SEMANA -->
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-calendar-week"></i>
              <strong>Registradas esta semana (últimos 7 días)</strong>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-6">
                <label class="form-label">Búsqueda rápida</label>
                <input id="weekSearch" class="form-control" placeholder="Escribe para filtrar (ID, número, test, material...)">
              </div>
              <div class="col-md-4">
                <label class="form-label">Filtrar por prefijo base</label>
                <div class="input-group">
                  <select id="weekSelect" class="form-select">
                    <option value="">-- Todos --</option>
                    <?php foreach($prefijos as $p): ?>
                      <option value="<?= e($p) ?>"><?= e($p) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button id="btnWeekFilt" class="btn btn-outline-primary"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="form-text">
                  Aplica las mismas reglas de año.
                </div>
              </div>
              <div class="col-md-2 d-grid">
                <button id="btnWeekReload" class="btn btn-outline-secondary"><i class="bi bi-arrow-repeat"></i> Actualizar</button>
              </div>
            </div>

            <div id="errWeek" class="alert alert-danger mt-3 d-none"></div>

            <div class="table-responsive mt-3">
              <table class="table table-sm table-striped align-middle" id="weekTable">
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
                <tbody id="weekBody">
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
  const NEXT_API = <?= json_encode($NEXT_API) ?>;
  const $ = s=>document.querySelector(s);

  // --------- Siguiente ----------
  const sel = $('#prefSelect'), inp = $('#prefInput'), pad = $('#padLen'), btn = $('#btnNext');
  const errNext = $('#errNext'), boxNext = $('#boxNext');
  const lastCol = $('#lastCol'), lastVal = $('#lastVal'), lastN = $('#lastN');
  const resolvedPrefix = $('#resolvedPrefix'), nextN = $('#nextN'), nextPad = $('#nextPad'), useThis = $('#useThis');

  sel?.addEventListener('change', ()=>{ inp.value = sel.value || ''; });

  btn?.addEventListener('click', async ()=>{
    const base = (inp.value || sel.value || '').trim();
    if (!base) return showErr(errNext, 'Debes indicar un prefijo.');
    const p = Math.max(1, parseInt(pad.value||'4',10));
    await doNext(base, p);
  });

  async function doNext(basePrefix, pad){
    try{
      errNext.classList.add('d-none');
      const url = `${NEXT_API}?action=next&prefix=${encodeURIComponent(basePrefix)}&pad=${pad}`;
      const res = await fetch(url, { headers:{Accept:'application/json'}, credentials:'same-origin' });
      const ct = (res.headers.get('content-type')||'').toLowerCase();
      const txt = await res.text();
      if (!ct.includes('application/json')) return showErr(errNext,'El endpoint devolvió HTML (ruta/sesión incorrecta).', txt);

      let data; try{ data=JSON.parse(txt);}catch{ return showErr(errNext,'JSON inválido.', txt); }
      if (!res.ok || !data.ok) return showErr(errNext, data?.error || `HTTP ${res.status}`);

      // soporta ambas claves del API (compatibilidad)
      const lf = data.last_found || {};
      lastCol.textContent = lf.from_column ?? lf.column ?? '—';
      lastVal.textContent = lf.value ?? '—';
      lastN.textContent   = (typeof lf.suffix_n==='number') ? lf.suffix_n : (typeof lf.suffix==='number' ? lf.suffix : '—');

      const resolved = data.params?.resolved ?? data.params?.prefix_resolved ?? '—';
      resolvedPrefix.textContent = resolved;

      const sug = data.suggestion || data.next || {};
      nextN.textContent   = sug.next_n ?? sug.next_suffix ?? '—';
      nextPad.textContent = sug.next_padded ?? '—';
      useThis.textContent = sug.use_this ?? sug.use_code ?? '—';

      boxNext.classList.remove('d-none');
    }catch(e){
      showErr(errNext, e.message || 'Error desconocido.');
    }
  }

  // --------- HOY + buscador ----------
  const q = $('#quickSearch');
  const todaySel = $('#todaySelect');
  const btnFilt = $('#btnTodayFilt');
  const btnRld  = $('#btnTodayReload');
  const errToday= $('#errToday');
  const tbody   = $('#todayBody');

  btnRld?.addEventListener('click', ()=> loadToday(null));
  btnFilt?.addEventListener('click', ()=>{
    const base = (todaySel.value || '').trim() || null;
    loadToday(base);
  });
  q?.addEventListener('input', ()=> filterTable(tbody, q.value.trim().toLowerCase()));

  async function loadToday(basePrefix=null){
    try{
      errToday.classList.add('d-none');
      tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Cargando…</td></tr>';
      let url = `${NEXT_API}?action=today`;
      if (basePrefix) url += `&prefix=${encodeURIComponent(basePrefix)}`;

      const res = await fetch(url, { headers:{Accept:'application/json'}, credentials:'same-origin' });
      const ct = (res.headers.get('content-type')||'').toLowerCase();
      const txt = await res.text();
      if (!ct.includes('application/json')) return showErr(errToday,'El endpoint devolvió HTML (ruta/sesión incorrecta).', txt);

      let data; try{ data=JSON.parse(txt);}catch{ return showErr(errToday,'JSON inválido.', txt); }
      if (!res.ok || !data.ok) return showErr(errToday, data?.error || `HTTP ${res.status}`);

      const rows = Array.isArray(data.today) ? data.today : [];
      if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Sin registros hoy.</td></tr>';
        return;
      }
      tbody.innerHTML = rows.map((r,i)=>{
        const id  = (r.Sample_ID ?? '').toString();
        const sn  = (r.Sample_Number ?? '').toString();
        const name= `${id} | ${sn}`;
        const tt  = (r.Test_Type ?? '').toString();
        const mt  = (r.Material_Type ?? '').toString();
        const rd  = (r.Registed_Date ?? '').toString();
        return `<tr>
          <td>${i+1}</td>
          <td>${esc(name)}</td>
          <td>${esc(id)}</td>
          <td>${esc(sn)}</td>
          <td>${esc(tt)}</td>
          <td>${esc(mt)}</td>
          <td>${esc(rd)}</td>
        </tr>`;
      }).join('');
      filterTable(tbody, q.value.trim().toLowerCase());
    }catch(e){
      showErr(errToday, e.message || 'Error desconocido.');
    }
  }

  // --------- SEMANA + buscador ----------
  const wq = $('#weekSearch');
  const weekSel = $('#weekSelect');
  const btnWeekFilt = $('#btnWeekFilt');
  const btnWeekRld  = $('#btnWeekReload');
  const errWeek= $('#errWeek');
  const wbody  = $('#weekBody');

  btnWeekRld?.addEventListener('click', ()=> loadWeek(null));
  btnWeekFilt?.addEventListener('click', ()=>{
    const base = (weekSel.value || '').trim() || null;
    loadWeek(base);
  });
  wq?.addEventListener('input', ()=> filterTable(wbody, wq.value.trim().toLowerCase()));

  async function loadWeek(basePrefix=null){
    try{
      errWeek.classList.add('d-none');
      wbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Cargando…</td></tr>';
      let url = `${NEXT_API}?action=week`;
      if (basePrefix) url += `&prefix=${encodeURIComponent(basePrefix)}`;

      const res = await fetch(url, { headers:{Accept:'application/json'}, credentials:'same-origin' });
      const ct = (res.headers.get('content-type')||'').toLowerCase();
      const txt = await res.text();
      if (!ct.includes('application/json')) return showErr(errWeek,'El endpoint devolvió HTML (ruta/sesión incorrecta).', txt);

      let data; try{ data=JSON.parse(txt);}catch{ return showErr(errWeek,'JSON inválido.', txt); }
      if (!res.ok || !data.ok) return showErr(errWeek, data?.error || `HTTP ${res.status}`);

      const rows = Array.isArray(data.week) ? data.week : [];
      if (!rows.length) {
        wbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Sin registros en los últimos 7 días.</td></tr>';
        return;
      }
      wbody.innerHTML = rows.map((r,i)=>{
        const id  = (r.Sample_ID ?? '').toString();
        const sn  = (r.Sample_Number ?? '').toString();
        const name= `${id} | ${sn}`;
        const tt  = (r.Test_Type ?? '').toString();
        const mt  = (r.Material_Type ?? '').toString();
        const rd  = (r.Registed_Date ?? '').toString();
        return `<tr>
          <td>${i+1}</td>
          <td>${esc(name)}</td>
          <td>${esc(id)}</td>
          <td>${esc(sn)}</td>
          <td>${esc(tt)}</td>
          <td>${esc(mt)}</td>
          <td>${esc(rd)}</td>
        </tr>`;
      }).join('');
      filterTable(wbody, wq.value.trim().toLowerCase());
    }catch(e){
      showErr(errWeek, e.message || 'Error desconocido.');
    }
  }

  // ---------- utils ----------
  function filterTable(tbody, q){
    const rows = Array.from(tbody.querySelectorAll('tr'));
    if (!rows.length) return;
    rows.forEach(tr=>{
      const txt = tr.innerText.toLowerCase();
      tr.style.display = (!q || txt.includes(q)) ? '' : 'none';
    });
  }
  function showErr(box, msg, detail){
    box.classList.remove('d-none');
    box.innerHTML = detail
      ? `${msg}<hr><div class="small border p-2" style="max-height:260px;overflow:auto;white-space:pre-wrap;">${esc(detail)}</div>`
      : msg;
  }
  function esc(s){ return s.replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

  // Carga inicial
  loadToday(null);
  loadWeek(null);
})();
</script>

<?php include_once('../components/footer.php'); ?>
