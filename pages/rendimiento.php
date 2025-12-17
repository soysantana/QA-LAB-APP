<?php
// /pages/rendimiento.php  (LISTA + DETALLE en el mismo archivo)
$page_title = 'Desempeño de Técnicos';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

/* ======================================================
   0) PARAMS
====================================================== */
$view = $_GET['view'] ?? 'list';     // list | detail
$techSelected = trim((string)($_GET['tech'] ?? ''));

// fechas
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$fromEsc = $db->escape($from . " 00:00:00");
$toEsc   = $db->escape($to   . " 23:59:59");

/* ======================================================
   1) TEST NAMES (ajusta a tu gusto)
====================================================== */
$testNames = [
  "GS" => "Grain Size",
  "AL" => "Atterberg Limit",
  "MC" => "Moisture Content",
  "SG" => "Specific Gravity",
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

/* ======================================================
   2) ALIAS MAP (OFICIAL)  **AQUÍ ES DONDE SE RESUELVE TODO**
   - Solo se cuentan técnicos que caigan en un alias de esta lista
====================================================== */
$aliasMap = [
  'WD'  => 'Wendin De Jesús',
  'JV'  => 'Jonathan Vargas',
  'RRH' => 'Rafael Reyes',
  'VM'  => 'Victor Mercedes',
  'DV'  => 'Diana Vázquez',
  'LS'  => 'Laura Sánchez',
  'YM'  => 'Yamilexi Mejía',
  'AS'  => 'Arturo Santana',
  'FE'  => 'Frandy Espinal',
  'WM'  => 'Wilson Martínez',
  'RL'  => 'Rafy Leocadio',
  'RV'  => 'Rony Vargas',
  'DF'  => 'Darielvy Félix',
  'JA'  => 'Jordany Almonte',
  'MC'  => 'Melvin Castillo',
  'J.MA' => 'Jordany Amparo',
  'LM'  => 'Luis Monegro',
];

/* Variantes -> Alias (agrega todas las que tú sabes que aparecen) */
$variantsToAlias = [
  'WENDIN' => 'WD',
  'WENDIN DE JESUS' => 'WD',
  'WENDIN DE JESÚS' => 'WD',
  'WENDIN DE JESUS MENDOZA' => 'WD',
  'WENDIN DE JESÚS MENDOZA' => 'WD',
  'W D' => 'WD',
  'W.D' => 'WD',
  'WD.' => 'WD',

  'JONATHAN' => 'JV',
  'JONATHAN VARGAS' => 'JV',
  'J VARGAS' => 'JV',
  'J. VARGAS' => 'JV',

  'RAFAEL REYES' => 'RRH',
  'R REYES' => 'RRH',
];

/* ======================================================
   3) TECH NORMALIZATION (tu forma, robusta)
====================================================== */
function tech_clean($s){
  $s = strtoupper(trim((string)$s));
  $s = str_replace(["\t","\n","\r"], ' ', $s);
  $s = preg_replace('/\s+/', ' ', $s);
  $s = str_replace(['.', ';', ':'], '', $s);
  return trim($s);
}

function tech_unify_separators($s){
  $s = tech_clean($s);
  // cualquier guion unicode => "/"
  $s = preg_replace('/[\p{Pd}]+/u', '/', $s);
  // otros separadores => "/"
  $s = str_replace([',','&','+','|','\\'], '/', $s);
  $s = preg_replace('/\s*\/\s*/', '/', $s);
  return trim($s, '/');
}

function tech_split($s){
  $s = tech_unify_separators($s);
  if ($s === '') return [];
  $parts = explode('/', $s);
  $out = [];
  foreach($parts as $p){
    $p = tech_clean($p);
    if ($p==='') continue;
    $out[] = $p;
  }
  return array_values(array_unique($out));
}

function tech_guess_alias_from_name($t){
  $t = tech_clean($t);
  if ($t === '') return '';
  $words = preg_split('/\s+/', $t);
  if (count($words) >= 2){
    return substr($words[0],0,1) . substr($words[1],0,1);
  }
  return substr($t,0,2);
}

/**
 * MODO ESTRICTO:
 * - si no cae en aliasMap => retorna '' (NO SE CUENTA)
 */
function tech_to_alias_strict($raw, $variantsToAlias, $aliasMap){
  $t = tech_clean($raw);
  if ($t === '') return '';

  if (strpos($t,'/') !== false){
    $t = explode('/', $t)[0];
    $t = tech_clean($t);
  }

  if (isset($aliasMap[$t])) return $t;
  if (isset($variantsToAlias[$t])) {
    $a = $variantsToAlias[$t];
    return isset($aliasMap[$a]) ? $a : '';
  }

  $guess = tech_guess_alias_from_name($t);
  if ($guess !== '' && isset($aliasMap[$guess])) return $guess;

  return '';
}

function tech_fullname($alias, $aliasMap){
  return $aliasMap[$alias] ?? $alias;
}

/* ======================================================
   4) SQL UNIFICADO (sin "real" como alias, evita errores)
====================================================== */
$sqlUnified = "
SELECT
  x.Technician,
  x.Test_Type,
  SUM(CASE WHEN x.Stage='REG'  THEN 1 ELSE 0 END) AS reg_cnt,
  SUM(CASE WHEN x.Stage='PREP' THEN 1 ELSE 0 END) AS prep_cnt,
  SUM(CASE WHEN x.Stage='REAL' THEN 1 ELSE 0 END) AS real_cnt,
  SUM(CASE WHEN x.Stage='DEL'  THEN 1 ELSE 0 END) AS del_cnt,
  COUNT(*) AS total_cnt
FROM (
  /* REGISTRO: Register_By + split Test_Type por coma */
  SELECT
    TRIM(IFNULL(r.Register_By,'')) AS Technician,
    TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(IFNULL(r.Test_Type,''), ',', n.n), ',', -1)) AS Test_Type,
    'REG' AS Stage,
    r.Registed_Date AS Dt
  FROM lab_test_requisition_form r
  JOIN (
    SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
    UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
    UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
    UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
  ) n
    ON n.n <= 1 + LENGTH(IFNULL(r.Test_Type,'')) - LENGTH(REPLACE(IFNULL(r.Test_Type,''), ',', ''))
  WHERE r.Registed_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* PREPARACIÓN */
  SELECT
    TRIM(IFNULL(p.Technician,'')) AS Technician,
    TRIM(IFNULL(p.Test_Type,''))  AS Test_Type,
    'PREP' AS Stage,
    p.Register_Date AS Dt
  FROM test_preparation p
  WHERE p.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* REALIZACIÓN */
  SELECT
    TRIM(IFNULL(a.Technician,'')) AS Technician,
    TRIM(IFNULL(a.Test_Type,''))  AS Test_Type,
    'REAL' AS Stage,
    a.Register_Date AS Dt
  FROM test_realization a
  WHERE a.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* ENTREGA */
  SELECT
    TRIM(IFNULL(d.Technician,'')) AS Technician,
    TRIM(IFNULL(d.Test_Type,''))  AS Test_Type,
    'DEL' AS Stage,
    d.Register_Date AS Dt
  FROM test_delivery d
  WHERE d.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'
) x
WHERE TRIM(IFNULL(x.Technician,'')) <> ''
  AND TRIM(IFNULL(x.Test_Type,'')) <> ''
GROUP BY x.Technician, x.Test_Type
ORDER BY total_cnt DESC
";

$rows = find_by_sql($sqlUnified);
if (!is_array($rows)) $rows = [];

/* ======================================================
   5) BUILD DATASETS (Lista y Detalle)
   - Split DF-RV => DF y RV
   - Strict alias => si no se mapea, NO entra
====================================================== */
$byTech = [];          // resumen por técnico (alias)
$byTechTest = [];      // detalle por técnico + tipo

foreach ($rows as $r){
  $techRaw = (string)($r['Technician'] ?? '');
  $tt      = trim((string)($r['Test_Type'] ?? ''));
  if ($tt === '') continue;

  $reg  = (int)($r['reg_cnt']  ?? 0);
  $prep = (int)($r['prep_cnt'] ?? 0);
  $real = (int)($r['real_cnt'] ?? 0);
  $del  = (int)($r['del_cnt']  ?? 0);
  $tot  = (int)($r['total_cnt']?? 0);

  $parts = tech_split($techRaw);
  if (empty($parts)) continue;

  foreach($parts as $p){
    $alias = tech_to_alias_strict($p, $variantsToAlias, $aliasMap);
    if ($alias === '') continue; // <- evita lista infinita

    if (!isset($byTech[$alias])){
      $byTech[$alias] = [
        'alias'=>$alias,
        'name'=>tech_fullname($alias, $aliasMap),
        'reg'=>0,'prep'=>0,'real'=>0,'del'=>0,'total'=>0
      ];
    }

    $byTech[$alias]['reg']   += $reg;
    $byTech[$alias]['prep']  += $prep;
    $byTech[$alias]['real']  += $real;
    $byTech[$alias]['del']   += $del;
    $byTech[$alias]['total'] += $tot;

    if (!isset($byTechTest[$alias])) $byTechTest[$alias] = [];
    if (!isset($byTechTest[$alias][$tt])){
      $byTechTest[$alias][$tt] = ['reg'=>0,'prep'=>0,'real'=>0,'del'=>0,'total'=>0];
    }
    $byTechTest[$alias][$tt]['reg']   += $reg;
    $byTechTest[$alias][$tt]['prep']  += $prep;
    $byTechTest[$alias][$tt]['real']  += $real;
    $byTechTest[$alias][$tt]['del']   += $del;
    $byTechTest[$alias][$tt]['total'] += $tot;
  }
}

/* ordenar técnicos por total desc */
uasort($byTech, function($a,$b){ return ($b['total'] <=> $a['total']); });

/* KPI global */
$kpi = ['reg'=>0,'prep'=>0,'real'=>0,'del'=>0,'total'=>0];
foreach($byTech as $st){
  $kpi['reg']   += (int)$st['reg'];
  $kpi['prep']  += (int)$st['prep'];
  $kpi['real']  += (int)$st['real'];
  $kpi['del']   += (int)$st['del'];
  $kpi['total'] += (int)$st['total'];

/* ======================================================
   5.1) RANKINGS BY PROCESS (REG / PREP / REAL / DEL)
====================================================== */

}
function sort_by_key_desc($arr, $key){
  uasort($arr, function($a,$b) use ($key){
    return ((int)$b[$key] <=> (int)$a[$key]);
  });
  return $arr;
}

$rankReg  = sort_by_key_desc($byTech, 'reg');
$rankPrep = sort_by_key_desc($byTech, 'prep');
$rankReal = sort_by_key_desc($byTech, 'real');
$rankDel  = sort_by_key_desc($byTech, 'del');

// opcional: top N
$TOPN = 15;
$rankRegTop  = array_slice($rankReg,  0, $TOPN, true);
$rankPrepTop = array_slice($rankPrep, 0, $TOPN, true);
$rankRealTop = array_slice($rankReal, 0, $TOPN, true);
$rankDelTop  = array_slice($rankDel,  0, $TOPN, true);




/* ======================================================
   6) RENDER
====================================================== */
?>
<main id="main" class="main" style="padding:18px;">

  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="mb-0">Desempeño de Técnicos</h1>
      <small class="text-muted">
        <?= $view==='detail' ? 'Detalle por técnico' : 'Listado de técnicos (clic para ver detalle)' ?>
      </small>
    </div>
    <span class="badge bg-light text-dark border">
      Rango: <strong><?=h($from)?></strong> → <strong><?=h($to)?></strong>
    </span>
  </div>

  <!-- filtros -->
  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="GET">
        <input type="hidden" name="view" value="<?=h($view)?>">
        <?php if($view==='detail' && $techSelected!==''): ?>
          <input type="hidden" name="tech" value="<?=h($techSelected)?>">
        <?php endif; ?>

        <div class="col-6 col-md-2">
          <label class="form-label form-label-sm">Desde</label>
          <input type="date" name="from" class="form-control form-control-sm" value="<?=h($from)?>">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label form-label-sm">Hasta</label>
          <input type="date" name="to" class="form-control form-control-sm" value="<?=h($to)?>">
        </div>

        <div class="col-12 col-md-3 d-grid">
          <button class="btn btn-primary btn-sm">
            <i class="bi bi-funnel me-1"></i>Aplicar
          </button>
        </div>

        <?php if($view==='detail'): ?>
          <div class="col-12 col-md-3 d-grid">
            <a class="btn btn-outline-secondary btn-sm"
               href="?view=list&from=<?=h($from)?>&to=<?=h($to)?>">
              <i class="bi bi-arrow-left me-1"></i>Volver a la lista
            </a>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- KPIs -->
  <div class="row g-3 mb-3">
    <?php
      $cards = [
        ['label'=>'Total',       'value'=>number_format($kpi['total']), 'icon'=>'bi-collection'],
        ['label'=>'Registro',    'value'=>number_format($kpi['reg']),   'icon'=>'bi-clipboard-plus'],
        ['label'=>'Preparación', 'value'=>number_format($kpi['prep']),  'icon'=>'bi-hammer'],
        ['label'=>'Realización', 'value'=>number_format($kpi['real']),  'icon'=>'bi-activity'],
        ['label'=>'Entrega',     'value'=>number_format($kpi['del']),   'icon'=>'bi-box-arrow-up-right'],
      ];
    ?>
    <?php foreach($cards as $c): ?>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="kpi-label"><?=h($c['label'])?></div>
              <div class="kpi-value"><?=h($c['value'])?></div>
            </div>
            <div class="kpi-icon"><i class="bi <?=h($c['icon'])?>"></i></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if($view === 'detail'): ?>

    <?php
      $alias = $techSelected;
      if ($alias === '' || !isset($byTech[$alias])) {
        echo '<div class="alert alert-warning">Técnico no encontrado en el rango o alias inválido.</div>';
      } else {
        $st = $byTech[$alias];
        $tests = $byTechTest[$alias] ?? [];
        uasort($tests, fn($a,$b)=>($b['total']<=>$a['total']));
      }
    ?>

    <?php if($alias !== '' && isset($byTech[$alias])): ?>
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
              <h3 class="mb-1"><?=h($st['alias'])?> — <?=h($st['name'])?></h3>
              <div class="text-muted small">Resumen del técnico en el rango seleccionado.</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <span class="badge bg-light text-dark border">REG: <strong><?= (int)$st['reg'] ?></strong></span>
              <span class="badge bg-light text-dark border">PREP: <strong><?= (int)$st['prep'] ?></strong></span>
              <span class="badge bg-light text-dark border">REAL: <strong><?= (int)$st['real'] ?></strong></span>
              <span class="badge bg-light text-dark border">DEL: <strong><?= (int)$st['del'] ?></strong></span>
              <span class="badge bg-primary">TOTAL: <strong><?= (int)$st['total'] ?></strong></span>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <strong>Detalle por Tipo de Ensayo</strong>
          <div class="small text-muted">Conteos por etapa (REG / PREP / REAL / DEL).</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Test</th>
                  <th class="text-center">REG</th>
                  <th class="text-center">PREP</th>
                  <th class="text-center">REAL</th>
                  <th class="text-center">DEL</th>
                  <th class="text-center">TOTAL</th>
                </tr>
              </thead>
              <tbody>
              <?php if(empty($tests)): ?>
                <tr><td colspan="6" class="text-center text-muted py-3">Sin datos.</td></tr>
              <?php else: ?>
                <?php foreach($tests as $code=>$cnt): ?>
                  <tr>
                    <td>
                      <code><?=h($code)?></code>
                      <span class="text-muted">— <?=h($testNames[$code] ?? $code)?></span>
                    </td>
                    <td class="text-center"><?= (int)$cnt['reg'] ?></td>
                    <td class="text-center"><?= (int)$cnt['prep'] ?></td>
                    <td class="text-center"><?= (int)$cnt['real'] ?></td>
                    <td class="text-center"><?= (int)$cnt['del'] ?></td>
                    <td class="text-center fw-bold"><?= (int)$cnt['total'] ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>

  <?php else: /* view=list */ ?>

    <!-- RANKINGS POR PROCESO -->
<div class="card shadow-sm border-0 mb-3">
  <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <strong>Ranking de Técnicos por Proceso</strong>
      <div class="small text-muted">Top <?= (int)$TOPN ?> por etapa (REG / PREP / REAL / DEL).</div>
    </div>
    <span class="badge bg-light text-dark border">
      Participación por etapa (sin mezclar)
    </span>
  </div>

  <div class="card-body">
    <ul class="nav nav-pills mb-3" id="rankTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-reg" data-bs-toggle="pill" data-bs-target="#pane-reg" type="button" role="tab">Registro</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-prep" data-bs-toggle="pill" data-bs-target="#pane-prep" type="button" role="tab">Preparación</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-real" data-bs-toggle="pill" data-bs-target="#pane-real" type="button" role="tab">Realización</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-del" data-bs-toggle="pill" data-bs-target="#pane-del" type="button" role="tab">Entrega</button>
      </li>
    </ul>

    <div class="tab-content">
      <?php
        function render_rank_table($list, $field, $from, $to){
          $max = 0;
          foreach($list as $st){ $max = max($max, (int)$st[$field]); }
          if ($max <= 0) $max = 1;
      ?>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:60px;" class="text-center">#</th>
                <th>Técnico</th>
                <th style="width:110px;" class="text-center">Alias</th>
                <th style="width:120px;" class="text-center">Conteo</th>
                <th>Barra</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach($list as $alias=>$st): ?>
                <?php $val = (int)$st[$field]; $pct = ($val/$max)*100; ?>
                <tr>
                  <td class="text-center fw-bold"><?= $i ?></td>
                  <td>
                    <div class="fw-semibold"><?= h($st['name']) ?></div>
                    <div class="small text-muted">
                      <a href="?view=detail&tech=<?=urlencode($alias)?>&from=<?=h($from)?>&to=<?=h($to)?>" class="text-decoration-none">
                        Ver detalle
                      </a>
                    </div>
                  </td>
                  <td class="text-center"><code><?= h($st['alias']) ?></code></td>
                  <td class="text-center fw-bold"><?= $val ?></td>
                  <td>
                    <div class="rankbar">
                      <div class="rankbar-fill" style="width: <?= (float)$pct ?>%;"></div>
                    </div>
                  </td>
                </tr>
              <?php $i++; endforeach; ?>
              <?php if($i===1): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">Sin datos.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php } ?>

      <div class="tab-pane fade show active" id="pane-reg" role="tabpanel">
        <?php render_rank_table($rankRegTop, 'reg', $from, $to); ?>
      </div>

      <div class="tab-pane fade" id="pane-prep" role="tabpanel">
        <?php render_rank_table($rankPrepTop, 'prep', $from, $to); ?>
      </div>

      <div class="tab-pane fade" id="pane-real" role="tabpanel">
        <?php render_rank_table($rankRealTop, 'real', $from, $to); ?>
      </div>

      <div class="tab-pane fade" id="pane-del" role="tabpanel">
        <?php render_rank_table($rankDelTop, 'del', $from, $to); ?>
      </div>
    </div>
  </div>
</div>


    <?php if(empty($byTech)): ?>
      <div class="alert alert-info">No hay datos en el rango seleccionado.</div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach($byTech as $alias=>$st): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <a class="text-decoration-none"
               href="?view=detail&tech=<?=urlencode($alias)?>&from=<?=h($from)?>&to=<?=h($to)?>">
              <div class="card shadow-sm border-0 tech-card h-100">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="tech-alias"><?=h($st['alias'])?></div>
                      <div class="tech-name"><?=h($st['name'])?></div>
                    </div>
                    <span class="badge bg-primary"><?= (int)$st['total'] ?></span>
                  </div>

                  <div class="mt-3 d-flex gap-2 flex-wrap small">
                    <span class="badge bg-light text-dark border">REG <?= (int)$st['reg'] ?></span>
                    <span class="badge bg-light text-dark border">PREP <?= (int)$st['prep'] ?></span>
                    <span class="badge bg-light text-dark border">REAL <?= (int)$st['real'] ?></span>
                    <span class="badge bg-light text-dark border">DEL <?= (int)$st['del'] ?></span>
                  </div>

                  <div class="mt-3 text-muted small">
                    Clic para ver detalle del técnico.
                  </div>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</main>

<style>
.kpi-card{
  border-radius:14px;
  border:1px solid #e5e7eb;
  background:#fff;
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
.tech-card{ border-radius:16px; transition:transform .12s ease, box-shadow .12s ease; }
.tech-card:hover{ transform:translateY(-2px); box-shadow:0 10px 24px rgba(15,23,42,0.10); }
.tech-alias{ font-weight:900; font-size:1.15rem; color:#0f172a; }
.tech-name{ color:#64748b; font-size:.9rem; margin-top:2px; }

.rankbar{
  width:100%;
  height:10px;
  border-radius:999px;
  background:#eef2ff;
  overflow:hidden;
  border:1px solid #e5e7eb;
}
.rankbar-fill{
  height:100%;
  border-radius:999px;
  background:#2563eb;
}

</style>

<?php include_once('../components/footer.php'); ?>
