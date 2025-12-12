<?php
// /pages/rendimiento.php
$page_title = 'Desempeño de Técnicos';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

require_once __DIR__ . '/../controllers/rendimiento_controller.php';

// Shortcuts
$h = 'perf_h';
$vmk = $vm['kpis'];
$quick = $vm['quick'];
$from  = $vm['from'];
$to    = $vm['to'];
$filterAlias = $vm['filterAlias'];
$filterType  = $vm['filterType'];
$techList = $vm['techList'];
$ttList   = $vm['ttList'];
$statsSorted = $vm['statsSorted'];
$stats = $vm['stats'];
$selectedAlias = $vm['selected']['alias'];
$selectedName  = $vm['selected']['name'];
$aliasMap = $vm['aliasMap'];
$lastRows = $vm['lastRows'];
?>
<main id="main" class="main" style="padding:18px;">

  <!-- HEADER -->
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="mb-0">Desempeño de Técnicos</h1>
      <small class="text-muted">
        Productividad por etapa, mix de ensayos (Realización) y repetición por técnico.
      </small>
    </div>
    <span class="badge bg-light text-dark border d-none d-md-inline-flex align-items-center gap-1">
      <i class="bi bi-calendar-range"></i>
      Rango: <strong><?= $h($from) ?></strong> a <strong><?= $h($to) ?></strong>
    </span>
  </div>

  <!-- FILTROS -->
  <section class="section mb-3">
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
          <div class="col-12 col-md-3">
            <label class="form-label form-label-sm">Intervalo rápido</label>
            <select name="quick" class="form-select form-select-sm">
              <option value="today"  <?= $quick==='today'?'selected':''; ?>>Hoy</option>
              <option value="7d"     <?= $quick==='7d'?'selected':''; ?>>Últimos 7 días</option>
              <option value="30d"    <?= $quick==='30d'?'selected':''; ?>>Últimos 30 días</option>
              <option value="12m"    <?= $quick==='12m'?'selected':''; ?>>Últimos 12 meses</option>
              <option value="custom" <?= $quick==='custom'?'selected':''; ?>>Personalizado</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Desde</label>
            <input type="date" class="form-control form-control-sm" name="from" value="<?= $h($from) ?>">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Hasta</label>
            <input type="date" class="form-control form-control-sm" name="to" value="<?= $h($to) ?>">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Técnico</label>
            <select name="tech" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($techList as $alias => $name): ?>
                <option value="<?= $h($alias) ?>" <?= $filterAlias===$alias?'selected':''; ?>>
                  <?= $h($name) ?> (<?= $h($alias) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Tipo de ensayo</label>
            <select name="ttype" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach ($ttList as $tt): ?>
                <option value="<?= $h($tt) ?>" <?= strtoupper(trim($filterType))===strtoupper(trim($tt))?'selected':''; ?>>
                  <?= $h($tt) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">
              <i class="bi bi-funnel me-1"></i>Aplicar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-3">
      <?php
        $kpisCards = [
          ['label'=>'Ensayos totales', 'value'=>number_format($vmk['total']), 'icon'=>'bi-collection', 'sub'=>'Total en el rango (todas las etapas).'],
          ['label'=>'Registradas',     'value'=>number_format($vmk['reg']),   'icon'=>'bi-clipboard-plus', 'sub'=>'Ingresadas al sistema.'],
          ['label'=>'Preparadas',      'value'=>number_format($vmk['pre']),   'icon'=>'bi-hammer', 'sub'=>'Pasaron por preparación.'],
          ['label'=>'Realizadas',      'value'=>number_format($vmk['rea']),   'icon'=>'bi-activity', 'sub'=>'Ejecutadas en laboratorio.'],
          ['label'=>'Entregadas',      'value'=>number_format($vmk['ent']),   'icon'=>'bi-box-arrow-up-right', 'sub'=>'Hojas / resultados entregados.'],
          ['label'=>'Digitadas',       'value'=>number_format($vmk['dig']),   'icon'=>'bi-keyboard', 'sub'=>'Digitadas / revisadas.'],
          ['label'=>'Repetidos',       'value'=>number_format($vmk['rep'])." <span class='kpi-mini text-danger'>(".$vmk['rep_pct_global']."%)</span>", 'icon'=>'bi-arrow-repeat', 'sub'=>'Ensayos con repetición (global).'],
        ];
      ?>
      <?php foreach ($kpisCards as $c): ?>
        <div class="col-6 col-md">
          <div class="kpi-card">
            <div class="kpi-card-main">
              <div>
                <div class="kpi-label"><?= $c['label'] ?></div>
                <div class="kpi-value"><?= $c['value'] ?></div>
              </div>
              <div class="kpi-icon">
                <i class="bi <?= $c['icon'] ?>"></i>
              </div>
            </div>
            <div class="kpi-subtext text-muted small"><?= $c['sub'] ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- BLOQUE MODERNO: Cards + Chart -->
  <section class="mb-3">
    <div class="row g-3">

      <!-- Cards -->
      <div class="col-lg-7">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div>
              <strong>Leaderboard (Cards)</strong>
              <div class="small text-muted">Clic en un técnico para abrir el panel lateral (drawer).</div>
            </div>
            <div class="d-flex gap-2">
              <input id="techSearch" class="form-control form-control-sm" style="width:220px" placeholder="Buscar..." />
              <select id="sortMode" class="form-select form-select-sm" style="width:190px">
                <option value="total">Orden: Total</option>
                <option value="rep">Orden: % Repetición</option>
                <option value="rea">Orden: Realización</option>
              </select>
            </div>
          </div>

          <div class="card-body">
            <div id="cardsGrid" class="cards-grid"></div>

            <?php if(empty($statsSorted)): ?>
              <div class="text-muted text-center py-3">Sin datos en el rango seleccionado.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white">
            <strong>Distribución por etapa (Top 10)</strong>
            <div class="small text-muted">Barra apilada por técnico.</div>
          </div>
          <div class="card-body">
            <div id="chartByTech" style="height: 420px;"></div>
          </div>
        </div>
      </div>

    </div>
  </section>

</main>

<!-- Drawer -->
<div id="drawerBackdrop" class="drawer-backdrop"></div>

<aside id="drawer" class="drawer">
  <div class="drawer-header">
    <div>
      <div class="drawer-title" id="drawerTitle">Técnico</div>
      <div class="drawer-subtitle" id="drawerSub">Detalle</div>
    </div>
    <button class="btn btn-sm btn-outline-secondary" id="drawerClose">Cerrar</button>
  </div>

  <div class="drawer-body">
    <div class="drawer-kpis" id="drawerKpis"></div>

    <div class="drawer-block">
      <div class="drawer-block-title">Mix de ensayos (Realización)</div>
      <div id="drawerDonut" style="height:220px;"></div>
      <div class="small text-muted mt-2">
        Nota: por ahora el donut se actualiza solo con el técnico seleccionado por URL (como tu lógica actual).
      </div>
    </div>

    <div class="drawer-block">
      <div class="drawer-block-title">Últimos ensayos</div>
      <div class="drawer-table-wrap">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th>Sample</th>
              <th>#</th>
              <th>Ensayo</th>
              <th>Etapa</th>
              <th>Rep</th>
            </tr>
          </thead>
          <tbody id="drawerLastBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</aside>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
  // Model desde backend (MISMA LOGICA)
  const PERF_MODEL = {
    from: <?= json_encode($from) ?>,
    to: <?= json_encode($to) ?>,
    quick: <?= json_encode($quick) ?>,
    filterType: <?= json_encode($filterType) ?>,
    selectedAlias: <?= json_encode($selectedAlias) ?>,

    stats: <?= json_encode($stats, JSON_UNESCAPED_UNICODE) ?>,

    // Estos 2 vienen de tu lógica actual (solo seleccionado)
    lastRowsSelected: <?= json_encode($lastRows, JSON_UNESCAPED_UNICODE) ?>,
    donutSelected: <?= json_encode($vm['charts']['donut'], JSON_UNESCAPED_UNICODE) ?>
  };
</script>

<script>
/* ===========================
   Cards + Drawer (Frontend)
=========================== */
(function(){
  const stats = PERF_MODEL.stats || {};

  const grid = document.getElementById('cardsGrid');
  const search = document.getElementById('techSearch');
  const sortMode = document.getElementById('sortMode');

  function toItems(){
    return Object.keys(stats).map(alias => {
      const st = stats[alias];
      return {
        alias,
        name: st.name || alias,
        job: st.job || 'Técnico',
        reg: Number(st.reg||0),
        pre: Number(st.pre||0),
        rea: Number(st.rea||0),
        ent: Number(st.ent||0),
        dig: Number(st.dig||0),
        total: Number(st.total||0),
        rep: Number(st.rep||0),
        rep_pct: Number(st.rep_pct||0),
      };
    });
  }

  let items = toItems();

  function sortItems(mode){
    const arr = [...items];
    if(mode === 'rep') arr.sort((a,b)=> (b.rep_pct - a.rep_pct) || (b.total - a.total));
    else if(mode === 'rea') arr.sort((a,b)=> (b.rea - a.rea) || (b.total - a.total));
    else arr.sort((a,b)=> (b.total - a.total));
    return arr;
  }

  function filterItems(q, arr){
    q = (q||'').toLowerCase().trim();
    if(!q) return arr;
    return arr.filter(x =>
      (x.name||'').toLowerCase().includes(q) ||
      (x.alias||'').toLowerCase().includes(q)
    );
  }

  function stageBar(st){
    const sum = st.reg+st.pre+st.rea+st.ent+st.dig;
    const pct = (v)=> sum>0 ? Math.round((v/sum)*100) : 0;

    return `
      <div class="mini-bars" title="Distribución por etapa">
        <span style="width:${pct(st.reg)}%"></span>
        <span style="width:${pct(st.pre)}%"></span>
        <span style="width:${pct(st.rea)}%"></span>
        <span style="width:${pct(st.ent)}%"></span>
        <span style="width:${pct(st.dig)}%"></span>
      </div>
    `;
  }

  function escapeHtml(str){
    return (str ?? '').toString()
      .replaceAll('&','&amp;')
      .replaceAll('<','&lt;')
      .replaceAll('>','&gt;')
      .replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  function cardHtml(st){
    const badgeClass = st.rep_pct>0 ? 'badge-danger' : 'badge-ok';
    const repText = st.rep>0 ? `${st.rep} (${st.rep_pct}%)` : `0%`;

    return `
      <div class="tech-card" data-alias="${escapeHtml(st.alias)}">
        <div class="tech-card-top">
          <div class="tech-avatar">${escapeHtml((st.name||'?').trim().slice(0,1).toUpperCase())}</div>
          <div class="tech-meta">
            <div class="tech-name">${escapeHtml(st.name)} <span class="tech-alias">(${escapeHtml(st.alias)})</span></div>
            <div class="tech-job">${escapeHtml(st.job || 'Técnico')}</div>
          </div>
        </div>

        <div class="tech-metrics">
          <div class="metric">
            <div class="metric-label">Total</div>
            <div class="metric-value">${st.total}</div>
          </div>
          <div class="metric">
            <div class="metric-label">Real</div>
            <div class="metric-value">${st.rea}</div>
          </div>
          <div class="metric">
            <div class="metric-label">Rep</div>
            <div class="metric-value"><span class="${badgeClass}">${repText}</span></div>
          </div>
        </div>

        ${stageBar(st)}

        <div class="tech-card-footer">
          <span class="chip">Reg ${st.reg}</span>
          <span class="chip">Prep ${st.pre}</span>
          <span class="chip">Ent ${st.ent}</span>
          <span class="chip">Dig ${st.dig}</span>
        </div>
      </div>
    `;
  }

  function render(){
    const mode = sortMode.value;
    const q = search.value;
    const arr = filterItems(q, sortItems(mode));

    if(arr.length === 0){
      grid.innerHTML = `<div class="text-muted p-3">No hay técnicos para mostrar.</div>`;
      return;
    }

    grid.innerHTML = arr.map(cardHtml).join('');
  }

  // Drawer
  const drawer = document.getElementById('drawer');
  const backdrop = document.getElementById('drawerBackdrop');
  const closeBtn = document.getElementById('drawerClose');
  const titleEl = document.getElementById('drawerTitle');
  const subEl = document.getElementById('drawerSub');
  const kpisEl = document.getElementById('drawerKpis');
  const lastBody = document.getElementById('drawerLastBody');

  let donutChart = null;

  function openDrawer(){
    drawer.classList.add('open');
    backdrop.classList.add('open');
  }
  function closeDrawer(){
    drawer.classList.remove('open');
    backdrop.classList.remove('open');
  }

  closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  function renderLast(rows){
    if(!rows || rows.length===0){
      lastBody.innerHTML = `<tr><td colspan="6" class="text-muted text-center py-3">Sin registros.</td></tr>`;
      return;
    }

    lastBody.innerHTML = rows.map(r=>{
      const dt = (r.Dt||'').toString().substring(0,10);
      const rep = r.is_rep ? 'Sí' : '—';
      const repBadge = r.is_rep ? 'badge bg-danger-subtle text-danger border' : '';
      const stage = r.Stage || '';
      return `
        <tr>
          <td>${escapeHtml(dt)}</td>
          <td>${escapeHtml(r.Sample_ID||'')}</td>
          <td>${escapeHtml(r.Sample_Number||'')}</td>
          <td><code>${escapeHtml(r.Test_Type||'')}</code></td>
          <td><span class="badge bg-light text-muted border">${escapeHtml(stage)}</span></td>
          <td>${r.is_rep ? `<span class="${repBadge}">Sí</span>` : '—'}</td>
        </tr>
      `;
    }).join('');
  }

  function renderDonut(data){
    const el = document.getElementById('drawerDonut');
    if(!el) return;

    if(typeof echarts === 'undefined'){
      el.innerHTML = `<div class="text-muted small">ECharts no disponible.</div>`;
      return;
    }

    if(!donutChart) donutChart = echarts.init(el);

    if(!data || data.length===0){
      donutChart.clear();
      donutChart.setOption({ title:{ text:'Sin datos', left:'center', top:'middle' } });
      return;
    }

    donutChart.setOption({
      tooltip: { trigger: 'item' },
      legend: { bottom: 0 },
      series: [{
        type: 'pie',
        radius: ['40%','70%'],
        label: { show:false },
        labelLine: { show:false },
        data: data
      }]
    });
  }

  function renderDrawer(alias){
    const st = stats[alias];
    if(!st) return;

    titleEl.textContent = `${st.name || alias} (${alias})`;
    subEl.textContent = `Rango: ${PERF_MODEL.from} → ${PERF_MODEL.to}`;

    kpisEl.innerHTML = `
      <div class="drawer-kpi"><div class="dk-label">Total</div><div class="dk-val">${st.total}</div></div>
      <div class="drawer-kpi"><div class="dk-label">Registradas</div><div class="dk-val">${st.reg}</div></div>
      <div class="drawer-kpi"><div class="dk-label">Preparadas</div><div class="dk-val">${st.pre}</div></div>
      <div class="drawer-kpi"><div class="dk-label">Realizadas</div><div class="dk-val">${st.rea}</div></div>
      <div class="drawer-kpi"><div class="dk-label">Entregadas</div><div class="dk-val">${st.ent}</div></div>
      <div class="drawer-kpi"><div class="dk-label">Digitadas</div><div class="dk-val">${st.dig}</div></div>
      <div class="drawer-kpi"><div class="dk-label">Repetidos</div><div class="dk-val">${st.rep} (${st.rep_pct}%)</div></div>
    `;

    // Con tu lógica actual, estos datos solo existen para el seleccionado por URL
    const donutData = (alias === PERF_MODEL.selectedAlias) ? (PERF_MODEL.donutSelected || []) : [];
    const last = (alias === PERF_MODEL.selectedAlias) ? (PERF_MODEL.lastRowsSelected || []) : [];

    renderDonut(donutData);
    renderLast(last);

    openDrawer();
  }

  // Click en card -> drawer
  grid.addEventListener('click', (e)=>{
    const card = e.target.closest('.tech-card');
    if(!card) return;
    const alias = card.getAttribute('data-alias');
    renderDrawer(alias);
  });

  search.addEventListener('input', render);
  sortMode.addEventListener('change', render);

  render();

  // Abre drawer si hay técnico seleccionado (opcional)
  if(PERF_MODEL.selectedAlias){
    renderDrawer(PERF_MODEL.selectedAlias);
  }

})();
</script>

<script>
/* ===========================
   Chart apilado por técnico
=========================== */
(function(){
  const el = document.getElementById('chartByTech');
  if(!el) return;
  const chart = echarts.init(el);

  const techs  = <?= json_encode($vm['charts']['labels'], JSON_UNESCAPED_UNICODE); ?>;
  const reg    = <?= json_encode($vm['charts']['reg']); ?>;
  const pre    = <?= json_encode($vm['charts']['pre']); ?>;
  const rea    = <?= json_encode($vm['charts']['rea']); ?>;
  const ent    = <?= json_encode($vm['charts']['ent']); ?>;
  const dig    = <?= json_encode($vm['charts']['dig']); ?>;

  chart.setOption({
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: { top: 0 },
    grid: { left: 10, right: 10, bottom: 10, top: 40, containLabel: true },
    xAxis: { type: 'value' },
    yAxis: { type: 'category', data: techs },
    series: [
      { name:'Registradas', type:'bar', stack:'total', data:reg },
      { name:'Preparadas',  type:'bar', stack:'total', data:pre },
      { name:'Realizadas',  type:'bar', stack:'total', data:rea },
      { name:'Entregadas',  type:'bar', stack:'total', data:ent },
      { name:'Digitadas',   type:'bar', stack:'total', data:dig }
    ]
  });

  window.addEventListener('resize', () => chart.resize());
})();
</script>

<style>
  /* KPIs */
  .kpi-card{
    border-radius:14px;
    border:1px solid #e5e7eb;
    background:#ffffff;
    box-shadow:0 4px 12px rgba(15,23,42,0.04);
    padding:0.75rem 0.9rem;
    height:100%;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
  }
  .kpi-card-main{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:0.75rem;
  }
  .kpi-label{
    font-size:0.75rem;
    text-transform:uppercase;
    letter-spacing:0.06em;
    color:#64748b;
  }
  .kpi-value{
    font-size:1.35rem;
    font-weight:800;
    color:#0f172a;
    line-height:1.1;
  }
  .kpi-mini{ font-size:0.8rem; font-weight:700; margin-left:0.2rem; }
  .kpi-icon{
    width:38px;
    height:38px;
    border-radius:999px;
    background:#f1f5f9;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.1rem;
    color:#0f172a;
  }
  .kpi-subtext{ margin-top:0.35rem; }

  /* Cards Grid */
  .cards-grid{
    display:grid;
    grid-template-columns: repeat( auto-fit, minmax(260px, 1fr) );
    gap:12px;
  }
  .tech-card{
    border:1px solid #e5e7eb;
    border-radius:16px;
    background:#fff;
    box-shadow:0 6px 18px rgba(15,23,42,0.05);
    padding:14px;
    cursor:pointer;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .tech-card:hover{
    transform: translateY(-2px);
    box-shadow:0 10px 26px rgba(15,23,42,0.08);
  }
  .tech-card-top{
    display:flex;
    gap:10px;
    align-items:center;
    margin-bottom:10px;
  }
  .tech-avatar{
    width:42px;
    height:42px;
    border-radius:999px;
    background:#e0f2fe;
    color:#0369a1;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    font-size:1.05rem;
  }
  .tech-name{ font-weight:900; color:#0f172a; line-height:1.1; }
  .tech-alias{ font-weight:800; color:#64748b; font-size:0.9rem; }
  .tech-job{ font-size:0.85rem; color:#64748b; }

  .tech-metrics{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:10px;
    margin-bottom:10px;
  }
  .metric{
    border:1px solid #eef2f7;
    border-radius:12px;
    padding:10px;
    background:#fbfdff;
  }
  .metric-label{
    font-size:0.72rem;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:#64748b;
  }
  .metric-value{
    font-size:1.1rem;
    font-weight:900;
    color:#0f172a;
    margin-top:2px;
  }

  .badge-ok{
    display:inline-block;
    padding:4px 8px;
    border-radius:999px;
    background:#ecfdf3;
    color:#15803d;
    border:1px solid #bbf7d0;
    font-weight:900;
    font-size:0.82rem;
  }
  .badge-danger{
    display:inline-block;
    padding:4px 8px;
    border-radius:999px;
    background:#fef2f2;
    color:#b91c1c;
    border:1px solid #fecaca;
    font-weight:900;
    font-size:0.82rem;
  }

  .mini-bars{
    height:8px;
    border-radius:999px;
    overflow:hidden;
    display:flex;
    background:#f1f5f9;
    margin-bottom:10px;
  }
  .mini-bars span{ height:100%; display:block; }
  .mini-bars span:nth-child(1){ background:#cbd5e1; } /* reg */
  .mini-bars span:nth-child(2){ background:#93c5fd; } /* pre */
  .mini-bars span:nth-child(3){ background:#67e8f9; } /* rea */
  .mini-bars span:nth-child(4){ background:#86efac; } /* ent */
  .mini-bars span:nth-child(5){ background:#fde68a; } /* dig */

  .tech-card-footer{
    display:flex;
    flex-wrap:wrap;
    gap:6px;
  }
  .chip{
    padding:6px 10px;
    border-radius:999px;
    border:1px solid #e5e7eb;
    background:#f8fafc;
    font-weight:800;
    font-size:0.8rem;
    color:#334155;
  }

  /* Drawer */
  .drawer-backdrop{
    position:fixed;
    inset:0;
    background:rgba(15,23,42,.35);
    opacity:0;
    pointer-events:none;
    transition:.2s;
    z-index:1040;
  }
  .drawer-backdrop.open{
    opacity:1;
    pointer-events:auto;
  }
  .drawer{
    position:fixed;
    top:0;
    right:-420px;
    width:420px;
    height:100%;
    background:#fff;
    border-left:1px solid #e5e7eb;
    box-shadow:-10px 0 30px rgba(15,23,42,.12);
    transition:.25s ease;
    z-index:1050;
    display:flex;
    flex-direction:column;
  }
  .drawer.open{ right:0; }

  .drawer-header{
    padding:14px;
    border-bottom:1px solid #eef2f7;
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
  }
  .drawer-title{ font-size:1.05rem; font-weight:900; color:#0f172a; }
  .drawer-subtitle{ font-size:.85rem; color:#64748b; }

  .drawer-body{
    padding:14px;
    overflow:auto;
  }
  .drawer-kpis{
    display:grid;
    grid-template-columns: repeat(2, 1fr);
    gap:8px;
    margin-bottom:12px;
  }
  .drawer-kpi{
    border:1px solid #eef2f7;
    border-radius:12px;
    padding:10px;
    background:#fbfdff;
  }
  .dk-label{
    font-size:0.72rem;
    text-transform:uppercase;
    letter-spacing:.06em;
    color:#64748b;
  }
  .dk-val{
    font-size:1rem;
    font-weight:900;
    color:#0f172a;
    margin-top:2px;
  }
  .drawer-block{
    border:1px solid #eef2f7;
    border-radius:14px;
    padding:12px;
    background:#fff;
    margin-bottom:12px;
  }
  .drawer-block-title{
    font-weight:900;
    color:#0f172a;
    margin-bottom:8px;
  }
  .drawer-table-wrap{
    max-height:320px;
    overflow:auto;
  }
  @media (max-width: 520px){
    .drawer{ width:100%; }
  }
</style>

<?php include_once('../components/footer.php'); ?>
