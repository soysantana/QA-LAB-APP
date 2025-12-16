<?php
// /pages/desempeno_tecnicos.php
$page_title = 'Desempeño de Técnicos';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
function N($v){ return strtoupper(trim((string)$v)); }

// ======================================================
// 1) Filtro de fechas
// ======================================================
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$fromEsc = $db->escape($from . " 00:00:00");
$toEsc   = $db->escape($to   . " 23:59:59");

// ======================================================
// 2) Mapa de nombres de ensayos (ajusta a tu gusto)
// ======================================================
$testNames = [
  "GS" => "Grain Size",
  "GS_CF" => "Grain Size (Coarse Filter)",
  "GS_FF" => "Grain Size (Fine Filter)",
  "GS_LPF" => "Grain Size (LPF)",
  "GS_UTF" => "Grain Size (UTF)",
  "AL" => "Atterberg Limit",
  "MC" => "Moisture Content",
  "MC_OVEN" => "Moisture Content (Oven)",
  "MC_MICROWAVE" => "Moisture Content (Microwave)",
  "MC_CONSTANT_MASS" => "Moisture Content (Constant Mass)",
  "MC_SCALE" => "Moisture Content (Scale)",
  "SG" => "Specific Gravity",
  "SG_COARSE" => "Specific Gravity (Coarse)",
  "SG_FINE" => "Specific Gravity (Fine)",
  "HY" => "Hydrometer",
  "DHY" => "Double Hydrometer",
  "SP" => "Standard Proctor",
  "MP" => "Modified Proctor",
  "UCS" => "UCS",
  "PLT" => "Point Load Test",
  "BTS" => "Brazilian (BTS)",
  "SND" => "Soundness",
  "AR" => "Acid Reactivity",
];

// ======================================================
// 3) QUERY UNIFICADO (UNION ALL) — Etapas por técnico + por tipo
//    NOTA: en Registro usamos Register_By como "Technician"
// ======================================================
$sqlUnified = "
SELECT
  x.Technician,
  x.Test_Type,
  SUM(CASE WHEN x.Stage='REG'  THEN 1 ELSE 0 END)  AS reg,
  SUM(CASE WHEN x.Stage='PREP' THEN 1 ELSE 0 END)  AS prep,
  SUM(CASE WHEN x.Stage='REAL' THEN 1 ELSE 0 END)  AS rea,
  SUM(CASE WHEN x.Stage='DEL'  THEN 1 ELSE 0 END)  AS del,
  COUNT(*) AS total
FROM (
  /* =========================
     REGISTRO (lab_test_requisition_form)
     ========================= */
  SELECT
    TRIM(IFNULL(r.Register_By,'')) AS Technician,
    TRIM(IFNULL(tt.Test,''))       AS Test_Type,
    'REG'                          AS Stage,
    r.Registed_Date                AS Dt
  FROM lab_test_requisition_form r
  JOIN (
    SELECT
      id,
      TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(Test_Type, ',', n.n), ',', -1)) AS Test
    FROM lab_test_requisition_form
    JOIN (
      SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
      UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
      UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
      UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
    ) n
      ON n.n <= 1 + LENGTH(IFNULL(Test_Type,'')) - LENGTH(REPLACE(IFNULL(Test_Type,''), ',', ''))
  ) tt
    ON tt.id = r.id
  WHERE r.Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* =========================
     PREPARATION (test_preparation)
     ========================= */
  SELECT
    TRIM(IFNULL(p.Technician,'')) AS Technician,
    TRIM(IFNULL(p.Test_Type,'')) AS Test_Type,
    'PREP' AS Stage,
    p.Register_Date AS Dt
  FROM test_preparation p
  WHERE p.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* =========================
     REALIZATION (test_realization)
     ========================= */
  SELECT
    TRIM(IFNULL(a.Technician,'')) AS Technician,
    TRIM(IFNULL(a.Test_Type,'')) AS Test_Type,
    'REAL' AS Stage,
    a.Register_Date AS Dt
  FROM test_realization a
  WHERE a.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* =========================
     DELIVERY (test_delivery)
     ========================= */
  SELECT
    TRIM(IFNULL(d.Technician,'')) AS Technician,
    TRIM(IFNULL(d.Test_Type,'')) AS Test_Type,
    'DEL' AS Stage,
    d.Register_Date AS Dt
  FROM test_delivery d
  WHERE d.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
) x
WHERE TRIM(IFNULL(x.Technician,'')) <> ''
  AND TRIM(IFNULL(x.Test_Type,'')) <> ''
GROUP BY x.Technician, x.Test_Type
ORDER BY x.Technician, total DESC
";


// Ejecutar
$rows = find_by_sql($sqlUnified);
if (!is_array($rows)) $rows = [];

// ======================================================
// 4) Construir:
//    A) resumen por técnico
//    B) detalle por técnico+tipo
// ======================================================
$byTech = [];            // KPIs por técnico
$detail = [];            // detalle por técnico+tipo
$techList = [];          // lista para selector

foreach ($rows as $r) {
  $tech = trim((string)$r['Technician']);
  $tt   = trim((string)$r['Test_Type']);

  if ($tech === '' || $tt === '') continue;

  if (!isset($byTech[$tech])) {
    $byTech[$tech] = [
      'reg'=>0,'prep'=>0,'real'=>0,'del'=>0,'total'=>0,
      'mix_real'=>[], // mix SOLO de Realización
      'mix_all'=>[]   // mix de todas las etapas
    ];
  }

  $reg  = (int)$r['reg'];
  $prep = (int)$r['prep'];
  $real = (int)$r['rea'];
  $del  = (int)$r['del'];
  $tot  = (int)$r['total'];

  $byTech[$tech]['reg']   += $reg;
  $byTech[$tech]['prep']  += $prep;
  $byTech[$tech]['rea']  += $real;
  $byTech[$tech]['del']   += $del;
  $byTech[$tech]['total'] += $tot;

  // Mix por tipos:
  // - mix_real: solo cuenta lo que sea REAL
  // - mix_all : cuenta total (reg+prep+real+del)
  if (!isset($byTech[$tech]['mix_all'][$tt])) $byTech[$tech]['mix_all'][$tt] = 0;
  $byTech[$tech]['mix_all'][$tt] += $tot;

  if ($real > 0) {
    if (!isset($byTech[$tech]['mix_real'][$tt])) $byTech[$tech]['mix_real'][$tt] = 0;
    $byTech[$tech]['mix_real'][$tt] += $real;
  }

  $detail[] = [
    'Technician'=>$tech,
    'Test_Type'=>$tt,
    'Test_Name'=>$testNames[$tt] ?? $tt,
    'reg'=>$reg,'prep'=>$prep,'real'=>$real,'del'=>$del,'total'=>$tot
  ];

  $techList[$tech] = $tech;
}

ksort($techList);

// Ordenar técnicos por total DESC
uasort($byTech, function($a,$b){
  return ($b['total'] <=> $a['total']);
});

// ======================================================
// 5) KPI Global
// ======================================================
$kpi = ['reg'=>0,'prep'=>0,'rea'=>0,'del'=>0,'total'=>0];
foreach ($byTech as $t => $st){
  $kpi['reg']   += $st['reg'];
  $kpi['prep']  += $st['prep'];
  $kpi['rea']  += $st['rea'];
  $kpi['del']   += $st['del'];
  $kpi['total'] += $st['total'];
}

// ======================================================
// 6) Técnico seleccionado para donut (por URL)
// ======================================================
$selectedTech = $_GET['tech'] ?? '';
$selectedTech = trim($selectedTech);
if ($selectedTech === '' && !empty($byTech)) {
  // el top
  $selectedTech = array_key_first($byTech);
}

// Donut: mix_real (por defecto) — si no hay, cae a mix_all
$donutMap = [];
if ($selectedTech && isset($byTech[$selectedTech])) {
  $donutMap = $byTech[$selectedTech]['mix_real'];
  if (array_sum($donutMap) <= 0) $donutMap = $byTech[$selectedTech]['mix_all'];
}
arsort($donutMap);

// Top 12 para que no sea infinito
$donutMap = array_slice($donutMap, 0, 12, true);
$donutData = [];
foreach ($donutMap as $code=>$val){
  $donutData[] = ['name'=>($testNames[$code] ?? $code), 'value'=>$val];
}

// Datos para barra apilada (Top 10 técnicos)
$labels = [];
$regA=[]; $prepA=[]; $realA=[]; $delA=[];
$i=0;
foreach ($byTech as $tech=>$st){
  $labels[] = $tech;
  $regA[]   = $st['reg'];
  $prepA[]  = $st['prep'];
  $realA[]  = $st['rea'];
  $delA[]   = $st['del'];
  $i++;
  if ($i>=10) break;
}
?>
<main id="main" class="main" style="padding:18px;">

  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="mb-0">Desempeño de Técnicos</h1>
      <small class="text-muted">Contadores por etapa + mix por tipo de ensayo.</small>
    </div>
    <span class="badge bg-light text-dark border">
      Rango: <strong><?=h($from)?></strong> → <strong><?=h($to)?></strong>
    </span>
  </div>

  <!-- Filtros -->
  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="GET">
        <div class="col-6 col-md-2">
          <label class="form-label form-label-sm">Desde</label>
          <input type="date" name="from" class="form-control form-control-sm" value="<?=h($from)?>">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label form-label-sm">Hasta</label>
          <input type="date" name="to" class="form-control form-control-sm" value="<?=h($to)?>">
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label form-label-sm">Técnico (para donut)</label>
          <select name="tech" class="form-select form-select-sm">
            <option value="">(Top automáticamente)</option>
            <?php foreach($techList as $t): ?>
              <option value="<?=h($t)?>" <?= $t===$selectedTech?'selected':''; ?>>
                <?=h($t)?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Aplicar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- KPIs globales -->
  <div class="row g-3 mb-3">
    <?php
      $cards = [
        ['label'=>'Total', 'value'=>number_format($kpi['total']), 'icon'=>'bi-collection'],
        ['label'=>'Registro', 'value'=>number_format($kpi['reg']), 'icon'=>'bi-clipboard-plus'],
        ['label'=>'Preparación', 'value'=>number_format($kpi['prep']), 'icon'=>'bi-hammer'],
        ['label'=>'Realización', 'value'=>number_format($kpi['rea']), 'icon'=>'bi-activity'],
        ['label'=>'Entrega', 'value'=>number_format($kpi['del']), 'icon'=>'bi-box-arrow-up-right'],
      ];
    ?>
    <?php foreach($cards as $c): ?>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="kpi-label"><?=h($c['label'])?></div>
              <div class="kpi-value"><?= $c['value'] ?></div>
            </div>
            <div class="kpi-icon"><i class="bi <?=h($c['icon'])?>"></i></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="row g-3">
    <!-- Tabla resumen por técnico -->
    <div class="col-lg-7">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <strong>Resumen por Técnico</strong>
          <div class="small text-muted">Totales por etapa en el rango.</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Técnico</th>
                  <th class="text-center">Registro</th>
                  <th class="text-center">Prep</th>
                  <th class="text-center">Real</th>
                  <th class="text-center">Ent</th>
                  <th class="text-center">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if(empty($byTech)): ?>
                  <tr><td colspan="6" class="text-center text-muted py-3">Sin datos en el rango.</td></tr>
                <?php else: ?>
                  <?php foreach($byTech as $tech=>$st): ?>
                    <tr>
                      <td>
                        <a href="?from=<?=h(substr($fromEsc,0,10))?>&to=<?=h(substr($toEsc,0,10))?>&tech=<?=urlencode($tech)?>">
                          <?=h($tech)?>
                        </a>
                      </td>
                      <td class="text-center"><?= (int)$st['reg'] ?></td>
                      <td class="text-center"><?= (int)$st['prep'] ?></td>
                      <td class="text-center"><?= (int)$st['real'] ?></td>
                      <td class="text-center"><?= (int)$st['del'] ?></td>
                      <td class="text-center fw-bold"><?= (int)$st['total'] ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tabla detalle por técnico+tipo -->
      <div class="card shadow-sm border-0 mt-3">
        <div class="card-header bg-white">
          <strong>Detalle por Técnico y Tipo de Ensayo</strong>
          <div class="small text-muted">Cuenta por etapa para cada Test_Type.</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Técnico</th>
                  <th>Test</th>
                  <th class="text-center">Reg</th>
                  <th class="text-center">Prep</th>
                  <th class="text-center">Real</th>
                  <th class="text-center">Ent</th>
                  <th class="text-center">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if(empty($detail)): ?>
                  <tr><td colspan="7" class="text-center text-muted py-3">Sin datos.</td></tr>
                <?php else: ?>
                  <?php foreach($detail as $d): ?>
                    <tr>
                      <td><?=h($d['Technician'])?></td>
                      <td>
                        <code><?=h($d['Test_Type'])?></code>
                        <span class="text-muted">— <?=h($d['Test_Name'])?></span>
                      </td>
                      <td class="text-center"><?=$d['reg']?></td>
                      <td class="text-center"><?=$d['prep']?></td>
                      <td class="text-center"><?=$d['real']?></td>
                      <td class="text-center"><?=$d['del']?></td>
                      <td class="text-center fw-bold"><?=$d['total']?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    <!-- Charts -->
    <div class="col-lg-5">
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-white">
          <strong>Etapas (Top 10 técnicos)</strong>
          <div class="small text-muted">Barra apilada por etapa.</div>
        </div>
        <div class="card-body">
          <div id="chartStages" style="height:360px;"></div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <strong>Mix de ensayos — <?=h($selectedTech ?: 'N/A')?></strong>
          <div class="small text-muted">Por defecto usa Realización; si no hay, usa total.</div>
        </div>
        <div class="card-body">
          <div id="chartDonut" style="height:320px;"></div>
        </div>
      </div>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
(function(){
  const el = document.getElementById('chartStages');
  if(!el) return;
  const chart = echarts.init(el);

  const techs = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
  const reg   = <?= json_encode($regA) ?>;
  const prep  = <?= json_encode($prepA) ?>;
  const real  = <?= json_encode($realA) ?>;
  const del   = <?= json_encode($delA) ?>;

  chart.setOption({
    tooltip: { trigger:'axis', axisPointer:{ type:'shadow' } },
    legend: { top: 0 },
    grid: { left: 10, right: 10, top: 40, bottom: 10, containLabel:true },
    xAxis: { type:'value' },
    yAxis: { type:'category', data: techs },
    series: [
      { name:'Registro', type:'bar', stack:'t', data: reg },
      { name:'Preparación', type:'bar', stack:'t', data: prep },
      { name:'Realización', type:'bar', stack:'t', data: real },
      { name:'Entrega', type:'bar', stack:'t', data: del }
    ]
  });

  window.addEventListener('resize', ()=>chart.resize());
})();
</script>

<script>
(function(){
  const el = document.getElementById('chartDonut');
  if(!el) return;
  const chart = echarts.init(el);

  const data = <?= json_encode($donutData, JSON_UNESCAPED_UNICODE) ?>;

  if(!data || data.length===0){
    chart.setOption({ title:{ text:'Sin datos', left:'center', top:'middle' } });
    return;
  }

  chart.setOption({
    tooltip: { trigger:'item' },
    legend: { bottom: 0 },
    series: [{
      type:'pie',
      radius:['40%','70%'],
      label: { show:false },
      labelLine: { show:false },
      data: data
    }]
  });

  window.addEventListener('resize', ()=>chart.resize());
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
.kpi-icon{
  width:38px;height:38px;border-radius:999px;
  background:#f1f5f9;
  display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;color:#0f172a;
}
</style>

<?php include_once('../components/footer.php'); ?>
