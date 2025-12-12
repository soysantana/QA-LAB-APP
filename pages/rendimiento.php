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

  <!-- 2 COLUMNAS: Ranking + Chart -->
  <section class="mb-3">
    <div class="row g-3">

      <!-- Ranking -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
              <strong>Ranking por técnico</strong>
              <div class="small text-muted">Clic para ver el perfil abajo.</div>
            </div>
            <span class="badge bg-light text-muted border small">
              Técnicos: <?= count($statsSorted) ?>
            </span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Técnico</th>
                    <th class="text-end">Reg</th>
                    <th class="text-end">Prep</th>
                    <th class="text-end">Real</th>
                    <th class="text-end">Ent</th>
                    <th class="text-end">Dig</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">% Rep</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($statsSorted)): ?>
                    <tr><td colspan="8" class="text-muted text-center py-3">Sin datos en el rango seleccionado.</td></tr>
                  <?php else: ?>
                    <?php foreach ($statsSorted as $alias => $st): ?>
                      <?php
                        $isSel = ($selectedAlias === $alias);
                        $name  = $st['name'];
                        $url = "?quick=".urlencode($quick)."&from=".urlencode($from)."&to=".urlencode($to)."&tech=".urlencode($alias)."&ttype=".urlencode($filterType);
                      ?>
                      <tr class="<?= $isSel ? 'table-primary' : '' ?>">
                        <td>
                          <a href="<?= $url ?>" class="d-flex align-items-center gap-2 text-decoration-none text-reset">
                            <div class="avatar-tech"><?= $h(mb_substr($name,0,1,'UTF-8')) ?></div>
                            <div>
                              <div class="fw-semibold"><?= $h($name) ?></div>
                              <div class="small text-muted"><?= $st['avg_per_day'] ?> ensayos/día</div>
                            </div>
                          </a>
                        </td>
                        <td class="text-end"><?= $st['reg'] ?: '' ?></td>
                        <td class="text-end"><?= $st['pre'] ?: '' ?></td>
                        <td class="text-end"><?= $st['rea'] ?: '' ?></td>
                        <td class="text-end"><?= $st['ent'] ?: '' ?></td>
                        <td class="text-end"><?= $st['dig'] ?: '' ?></td>
                        <td class="text-end fw-semibold"><?= (int)$st['total'] ?></td>
                        <td class="text-end <?= $st['rep_pct']>0?'text-danger':'text-success' ?>">
                          <?= $st['rep'] ? (int)$st['rep'].' ('.$st['rep_pct'].'%)' : '0%' ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer bg-white small text-muted">
            Selecciona un técnico para ver “Perfil + Mix + Últimos ensayos”.
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white">
            <strong>Distribución por etapa (Top 10)</strong>
            <div class="small text-muted">Barra apilada por técnico.</div>
          </div>
          <div class="card-body">
            <div id="chartByTech" style="height: 360px;"></div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- PERFIL -->
  <section>
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
          <strong>Perfil del técnico</strong>
          <div class="small text-muted">Mix de ensayos (Realización) + últimos registros.</div>
        </div>
        <span class="badge bg-light text-muted border small">
          Seleccionado:
          <strong><?= $selectedAlias!=='' ? $h($selectedName).' ('.$h($selectedAlias).')' : '—' ?></strong>
        </span>
      </div>

      <div class="card-body">
        <?php if ($selectedAlias==='' || !isset($stats[$selectedAlias])): ?>
          <div class="text-muted text-center py-4">Selecciona un técnico en el ranking.</div>
        <?php else:
          $stSel = $stats[$selectedAlias];
        ?>
        <div class="row g-3">

          <!-- Perfil + Donut -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="avatar-tech avatar-lg"><?= $h(mb_substr($selectedName,0,1,'UTF-8')) ?></div>
                  <div>
                    <div class="fw-semibold"><?= $h($selectedName) ?></div>
                    <div class="small text-muted"><?= $h($aliasMap[$selectedAlias]['job'] ?? 'Técnico de laboratorio') ?></div>
                  </div>
                </div>

                <ul class="list-unstyled mb-2 small">
                  <li><strong>Total:</strong> <?= (int)$stSel['total'] ?></li>
                  <li><strong>Promedio/día:</strong> <?= $stSel['avg_per_day'] ?></li>
                  <li><strong>Repetidos:</strong> <?= (int)$stSel['rep'] ?> (<?= $stSel['rep_pct'] ?>%)</li>
                </ul>

                <hr>
                <div class="small text-muted mb-1">Etapas:</div>
                <ul class="list-unstyled small mb-0">
                  <li>Registradas: <strong><?= (int)$stSel['reg'] ?></strong></li>
                  <li>Preparadas: <strong><?= (int)$stSel['pre'] ?></strong></li>
                  <li>Realizadas: <strong><?= (int)$stSel['rea'] ?></strong></li>
                  <li>Entregadas: <strong><?= (int)$stSel['ent'] ?></strong></li>
                  <li>Digitadas: <strong><?= (int)$stSel['dig'] ?></strong></li>
                </ul>
              </div>
            </div>

            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="small text-muted mb-2">Mix de ensayos (Realización)</div>
                <div id="chartTechDonut" style="height:220px;"></div>
              </div>
            </div>
          </div>

          <!-- Últimos ensayos -->
          <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="small text-muted">Últimos ensayos del técnico</div>
                  <span class="badge bg-light text-muted border small">Máx. 50</span>
                </div>

                <div class="table-responsive" style="max-height:340px; overflow:auto;">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Fecha</th>
                        <th>Sample ID</th>
                        <th>#</th>
                        <th>Ensayo</th>
                        <th>Etapa</th>
                        <th>Repetido</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(empty($lastRows)): ?>
                        <tr><td colspan="6" class="text-muted text-center py-3">Sin registros recientes.</td></tr>
                      <?php else: ?>
                        <?php foreach($lastRows as $r): ?>
                          <?php
                            $stage = (string)$r['Stage'];
                            $badgeClass = 'bg-secondary-subtle text-secondary border';
                            if ($stage==='Registrada')   $badgeClass = 'bg-light text-muted border';
                            if ($stage==='Preparación') $badgeClass = 'bg-primary-subtle text-primary border';
                            if ($stage==='Realización') $badgeClass = 'bg-info-subtle text-info border';
                            if ($stage==='Entrega')     $badgeClass = 'bg-success-subtle text-success border';
                            if ($stage==='Digitado')    $badgeClass = 'bg-warning-subtle text-warning border';
                          ?>
                          <tr>
                            <td><?= $h(substr((string)$r['Dt'],0,10)) ?></td>
                            <td><?= $h($r['Sample_ID']) ?></td>
                            <td><?= $h($r['Sample_Number']) ?></td>
                            <td><code><?= $h($r['Test_Type']) ?></code></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $h($stage) ?></span></td>
                            <td>
                              <?php if(!empty($r['is_rep'])): ?>
                                <span class="badge bg-danger-subtle text-danger border">Sí</span>
                              <?php else: ?>
                                —
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
          </div>

        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
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

(function(){
  const el = document.getElementById('chartTechDonut');
  if(!el) return;
  const chart = echarts.init(el);

  const dataDonut = <?= json_encode($vm['charts']['donut'], JSON_UNESCAPED_UNICODE); ?>;

  if(!dataDonut || dataDonut.length === 0){
    chart.setOption({
      title:{ text:'Sin datos', left:'center', top:'middle' }
    });
    return;
  }

  chart.setOption({
    tooltip: { trigger: 'item' },
    legend: { bottom: 0 },
    series: [{
      name: 'Ensayos',
      type: 'pie',
      radius: ['40%','70%'],
      avoidLabelOverlap: false,
      itemStyle: { borderRadius: 6, borderWidth: 2 },
      label: { show: false, position: 'center' },
      emphasis: { label: { show: true, fontSize: 14, fontWeight: 'bold' } },
      labelLine: { show: false },
      data: dataDonut
    }]
  });

  window.addEventListener('resize', () => chart.resize());
})();
</script>

<style>
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
    font-size:1.4rem;
    font-weight:700;
    color:#0f172a;
    line-height:1.1;
  }
  .kpi-mini{ font-size:0.8rem; font-weight:600; margin-left:0.2rem; }
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

  .avatar-tech{
    width:28px;
    height:28px;
    border-radius:999px;
    background:#e0f2fe;
    color:#0369a1;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:0.8rem;
    font-weight:700;
  }
  .avatar-tech.avatar-lg{
    width:40px;
    height:40px;
    font-size:1.1rem;
  }
</style>

<?php include_once('../components/footer.php'); ?>
