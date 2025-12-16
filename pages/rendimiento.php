<?php
// /pages/desempeno_tecnicos_cards.php
$page_title = 'Desempeño de Técnicos (Cards)';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

if (!function_exists('h')) {
  function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

/* ======================================================
   1) Filtro de fechas
====================================================== */
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to   = $_GET['to']   ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

$fromEsc = $db->escape($from . " 00:00:00");
$toEsc   = $db->escape($to   . " 23:59:59");

$selectedTech = trim((string)($_GET['tech'] ?? ''));

/* ======================================================
   2) Nombres de ensayos (ajusta)
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
   3) Normalización de técnicos (TU MISMA IDEA, robusta)
   Regla A: "DF-RV" => DF +1, RV +1
====================================================== */

// Alias -> Nombre completo (AJUSTA TU LISTA REAL)
$aliasMap = [
  'WD'  => 'Wendin De Jesús Mendoza',
  'DF'  => 'Darielvy Félix',
  'RV'  => 'Rony Vargas',
  'RRH' => 'Rafael Reyes',
  'VM'  => 'Victor Mercedes',
  'DV'  => 'Diana Carolina Vázquez',
  'LS'  => 'Laura Sánchez',
  'YM'  => 'Yamilexi Mejía',
  'AS'  => 'Arturo Santana',
  'FE'  => 'Frandy Espinal',
  'WM'  => 'Wilson Martínez',
  'RL'  => 'Rafy Leocadio',
  'JA'  => 'Jordany Almonte',
  'MC'  => 'Melvin Castillo',
  'LM'  => 'Luis Monegro',
  'JV'  => 'Jonathan Vargas',
];

// Variantes -> Alias (agrega TODO lo que te llegue “mal escrito”)
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
  'JV' => 'JV',

  'RONY' => 'RV',
  'RONY VARGAS' => 'RV',

  'DARIELVY' => 'DF',
  'DARIELVY FELIX' => 'DF',
  'DARIELVY FÉLIX' => 'DF',
];

function tech_clean($s){
  $s = strtoupper(trim((string)$s));
  $s = str_replace(["\t","\n","\r"], ' ', $s);
  $s = preg_replace('/\s+/', ' ', $s);
  $s = str_replace(['.', ';', ':'], '', $s);
  return trim($s);
}

/**
 * Convierte separadores raros a "/"
 * - soporta: , & + | \ /
 * - soporta guiones unicode: - ‐ ‒ – — ― (y cualquier \p{Pd})
 */
function tech_unify_separators($s){
  $s = tech_clean($s);

  // Cualquier tipo de dash unicode => "/"
  $s = preg_replace('/[\p{Pd}]+/u', '/', $s);

  // Otros separadores comunes => "/"
  $s = str_replace([',','&','+','|','\\'], '/', $s);

  // Normaliza " / " => "/"
  $s = preg_replace('/\s*\/\s*/', '/', $s);

  return trim($s, '/');
}

/** Devuelve tokens: "DF-RV" => ["DF","RV"] incluso con guion raro */
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

/** "VICTOR MERCEDES" => VM */
function tech_guess_alias_from_name($t){
  $t = tech_clean($t);
  if ($t === '') return '';
  $words = preg_split('/\s+/', $t);

  if (count($words) >= 2){
    $a = substr($words[0], 0, 1);
    $b = substr($words[1], 0, 1);
    return $a.$b;
  }
  return substr($t, 0, 2);
}

/**
 * Convierte a alias oficial.
 * Para evitar lista infinita:
 * - si no se reconoce => devuelve "OTROS"
 */
function tech_to_alias($raw, $variantsToAlias, $aliasMap){
  $t = tech_clean($raw);
  if ($t === '') return '';

  // ya alias
  if (isset($aliasMap[$t])) return $t;

  // variante conocida
  if (isset($variantsToAlias[$t])) return $variantsToAlias[$t];

  // intenta deducir iniciales
  $guess = tech_guess_alias_from_name($t);
  if ($guess !== '' && isset($aliasMap[$guess])) return $guess;

  // no reconocido => OTROS (para no explotar listado)
  return 'OTROS';
}

function tech_fullname($alias, $aliasMap){
  return $aliasMap[$alias] ?? $alias;
}

/* ======================================================
   4) SQL UNIFICADO (REG/PREP/REAL/DEL)
   Nota: REG usa Register_By
====================================================== */
$sql = "
SELECT
  x.Technician,
  x.Test_Type,
  SUM(CASE WHEN x.Stage='REG'  THEN 1 ELSE 0 END) AS reg,
  SUM(CASE WHEN x.Stage='PREP' THEN 1 ELSE 0 END) AS prep,
  SUM(CASE WHEN x.Stage='REAL' THEN 1 ELSE 0 END) AS rea,
  SUM(CASE WHEN x.Stage='DEL'  THEN 1 ELSE 0 END) AS del,
  COUNT(*) AS total
FROM (
  /* REGISTRO: explota Test_Type CSV */
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

  /* PREP */
  SELECT
    TRIM(IFNULL(p.Technician,'')) AS Technician,
    TRIM(IFNULL(p.Test_Type,''))  AS Test_Type,
    'PREP' AS Stage,
    p.Register_Date AS Dt
  FROM test_preparation p
  WHERE p.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* REAL */
  SELECT
    TRIM(IFNULL(a.Technician,'')) AS Technician,
    TRIM(IFNULL(a.Test_Type,''))  AS Test_Type,
    'REAL' AS Stage,
    a.Register_Date AS Dt
  FROM test_realization a
  WHERE a.Register_Date BETWEEN '{$fromEsc}' AND '{$toEsc}'

  UNION ALL

  /* DEL */
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
ORDER BY total DESC
";

$rows = find_by_sql($sql);
if (!is_array($rows)) $rows = [];

/* ======================================================
   5) Construcción de datasets (cards + detalle)
   Regla A: split => cada alias suma completo
====================================================== */
$byTech = [];            // resumen por técnico (alias)
$byTechType = [];        // detalle por técnico + tipo
$techList = [];          // alias => nombre

foreach ($rows as $r){
  $rawTech = (string)($r['Technician'] ?? '');
  $tt      = trim((string)($r['Test_Type'] ?? ''));
  if ($tt==='') continue;

  $reg  = (int)($r['reg'] ?? 0);
  $prep = (int)($r['prep'] ?? 0);
  $real = (int)($r['rea'] ?? 0);
  $del  = (int)($r['del'] ?? 0);
  $tot  = (int)($r['total'] ?? 0);

  $parts = tech_split($rawTech);
  if (!$parts) continue;

  foreach($parts as $p){
    $alias = tech_to_alias($p, $variantsToAlias, $aliasMap);
    if ($alias==='') continue;

    $full = ($alias==='OTROS') ? 'No reconocido (revisar variantes)' : tech_fullname($alias, $aliasMap);

    if (!isset($byTech[$alias])){
      $byTech[$alias] = [
        'alias'=>$alias, 'name'=>$full,
        'reg'=>0,'prep'=>0,'rea'=>0,'del'=>0,'total'=>0
      ];
    }

    $byTech[$alias]['reg']   += $reg;
    $byTech[$alias]['prep']  += $prep;
    $byTech[$alias]['rea']  += $real;
    $byTech[$alias]['del']   += $del;
    $byTech[$alias]['total'] += $tot;

    if (!isset($byTechType[$alias])) $byTechType[$alias] = [];
    if (!isset($byTechType[$alias][$tt])){
      $byTechType[$alias][$tt] = ['reg'=>0,'prep'=>0,'rea'=>0,'del'=>0,'total'=>0];
    }
    $byTechType[$alias][$tt]['reg']   += $reg;
    $byTechType[$alias][$tt]['prep']  += $prep;
    $byTechType[$alias][$tt]['rea']  += $real;
    $byTechType[$alias][$tt]['del']   += $del;
    $byTechType[$alias][$tt]['total'] += $tot;

    $techList[$alias] = $full;
  }
}

// Orden por total DESC
uasort($byTech, fn($a,$b)=>($b['total']<=>$a['total']));

// KPI global
$kpi = ['reg'=>0,'prep'=>0,'real'=>0,'del'=>0,'total'=>0];
foreach($byTech as $st){
  $kpi['reg']   += (int)$st['reg'];
  $kpi['prep']  += (int)$st['prep'];
  $kpi['rea']  += (int)$st['rea'];
  $kpi['del']   += (int)$st['del'];
  $kpi['total'] += (int)$st['total'];
}

// Si no seleccionó tech, el top
if ($selectedTech==='' && !empty($byTech)) $selectedTech = array_key_first($byTech);

// detalle seleccionado
$selSummary = $byTech[$selectedTech] ?? null;
$selTypes   = $byTechType[$selectedTech] ?? [];
if ($selTypes) {
  uasort($selTypes, fn($a,$b)=>($b['total']<=>$a['total']));
}
?>

<main id="main" class="main" style="padding:18px;">

  <div class="pagetitle d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div class="me-3">
      <h1 class="mb-0">Desempeño de Técnicos</h1>
      <div class="text-muted small">Tarjetas por técnico + click para ver detalle. (DF-RV = cuenta para DF y RV)</div>
    </div>
    <span class="badge bg-light text-dark border">
      Rango: <strong><?=h($from)?></strong> → <strong><?=h($to)?></strong>
    </span>
  </div>

  <!-- filtros -->
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
        <div class="col-12 col-md-6">
          <label class="form-label form-label-sm">Buscar técnico</label>
          <input id="qTech" type="text" class="form-control form-control-sm" placeholder="Ej: DF, RV, WD, Victor..." autocomplete="off">
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
        ['label'=>'Total',       'value'=>number_format($kpi['total']), 'icon'=>'bi-collection'],
        ['label'=>'Registro',    'value'=>number_format($kpi['reg']),   'icon'=>'bi-clipboard-plus'],
        ['label'=>'Preparación', 'value'=>number_format($kpi['prep']),  'icon'=>'bi-hammer'],
        ['label'=>'Realización', 'value'=>number_format($kpi['rea']),  'icon'=>'bi-activity'],
        ['label'=>'Entrega',     'value'=>number_format($kpi['del']),   'icon'=>'bi-box-arrow-up-right'],
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
    <!-- LEFT: CARDS GRID -->
    <div class="col-lg-7">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <div>
            <strong>Técnicos</strong>
            <div class="small text-muted">Click en una tarjeta para ver detalle.</div>
          </div>
          <span class="badge bg-light text-dark border"><?=count($byTech)?> técnicos</span>
        </div>
        <div class="card-body">
          <?php if(empty($byTech)): ?>
            <div class="text-muted text-center py-4">Sin datos en el rango.</div>
          <?php else: ?>
            <div id="gridTech" class="grid-tech">
              <?php foreach($byTech as $alias=>$st): ?>
                <?php
                  $isSel = ($alias === $selectedTech);
                  $url = "?from=".urlencode($from)."&to=".urlencode($to)."&tech=".urlencode($alias);
                ?>
                <a class="tech-card <?= $isSel?'selected':''; ?>" href="<?=h($url)?>" data-alias="<?=h($alias)?>" data-name="<?=h($st['name'])?>">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="tech-alias"><?=h($alias)?></div>
                      <div class="tech-name"><?=h($st['name'])?></div>
                    </div>
                    <div class="tech-total"><?= (int)$st['total'] ?></div>
                  </div>

                  <div class="tech-kpis">
                    <div><span>REG</span><b><?= (int)$st['reg'] ?></b></div>
                    <div><span>PREP</span><b><?= (int)$st['prep'] ?></b></div>
                    <div><span>REAL</span><b><?= (int)$st['rea'] ?></b></div>
                    <div><span>DEL</span><b><?= (int)$st['del'] ?></b></div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- RIGHT: DETAIL -->
    <div class="col-lg-5">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
          <strong>Detalle del Técnico</strong>
          <div class="small text-muted">Por tipo de ensayo.</div>
        </div>
        <div class="card-body">
          <?php if(!$selSummary): ?>
            <div class="text-muted text-center py-4">Selecciona un técnico.</div>
          <?php else: ?>
            <div class="mb-2">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <div style="font-size:1.2rem;font-weight:900;"><?=h($selSummary['alias'])?></div>
                  <div class="text-muted"><?=h($selSummary['name'])?></div>
                </div>
                <span class="badge bg-dark">Total: <?= (int)$selSummary['total'] ?></span>
              </div>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6"><div class="mini"><span>REG</span><b><?= (int)$selSummary['reg'] ?></b></div></div>
              <div class="col-6"><div class="mini"><span>PREP</span><b><?= (int)$selSummary['prep'] ?></b></div></div>
              <div class="col-6"><div class="mini"><span>REAL</span><b><?= (int)$selSummary['rea'] ?></b></div></div>
              <div class="col-6"><div class="mini"><span>DEL</span><b><?= (int)$selSummary['del'] ?></b></div></div>
            </div>

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
                  <?php if(empty($selTypes)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Sin detalle.</td></tr>
                  <?php else: ?>
                    <?php foreach($selTypes as $tt=>$c): ?>
                      <tr>
                        <td>
                          <code><?=h($tt)?></code>
                          <div class="small text-muted"><?=h($testNames[$tt] ?? $tt)?></div>
                        </td>
                        <td class="text-center"><?= (int)$c['reg'] ?></td>
                        <td class="text-center"><?= (int)$c['prep'] ?></td>
                        <td class="text-center"><?= (int)$c['rea'] ?></td>
                        <td class="text-center"><?= (int)$c['del'] ?></td>
                        <td class="text-center fw-bold"><?= (int)$c['total'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <?php if($selectedTech==='OTROS'): ?>
              <div class="alert alert-warning mt-2 mb-0">
                <b>OTROS</b> agrupa nombres no reconocidos para evitar listas infinitas.
                Si quieres que “OTROS” se reparta bien, agrega variantes a <code>$variantsToAlias</code>.
              </div>
            <?php endif; ?>

          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</main>

<style>
.kpi-card{
  border-radius:14px;
  border:1px solid #e5e7eb;
  background:#ffffff;
  box-shadow:0 4px 12px rgba(15,23,42,0.04);
  padding:0.75rem 0.9rem;
  height:100%;
}
.kpi-label{ font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; color:#64748b; }
.kpi-value{ font-size:1.35rem; font-weight:800; color:#0f172a; line-height:1.1; }
.kpi-icon{
  width:38px;height:38px;border-radius:999px;background:#f1f5f9;
  display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#0f172a;
}

.grid-tech{
  display:grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap:12px;
}
@media (min-width: 1200px){
  .grid-tech{ grid-template-columns: repeat(3, minmax(0, 1fr)); }
}

.tech-card{
  display:block;
  text-decoration:none;
  border:1px solid #e5e7eb;
  border-radius:14px;
  padding:12px;
  background:#fff;
  box-shadow:0 4px 12px rgba(15,23,42,0.04);
  transition:transform .08s ease, box-shadow .08s ease;
  color:#0f172a;
}
.tech-card:hover{ transform:translateY(-1px); box-shadow:0 10px 24px rgba(15,23,42,0.08); }
.tech-card.selected{ outline:2px solid rgba(59,130,246,.55); border-color:rgba(59,130,246,.35); }

.tech-alias{ font-size:1.05rem; font-weight:900; letter-spacing:.02em; }
.tech-name{ font-size:.82rem; color:#64748b; margin-top:2px; }
.tech-total{
  font-size:1.05rem;
  font-weight:900;
  background:#0f172a;
  color:#fff;
  padding:4px 10px;
  border-radius:999px;
  line-height:1;
}
.tech-kpis{
  margin-top:10px;
  display:grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap:8px;
}
.tech-kpis > div{
  border:1px solid #eef2f7;
  border-radius:10px;
  padding:8px 8px;
  text-align:center;
}
.tech-kpis span{ display:block; font-size:.67rem; color:#64748b; letter-spacing:.05em; }
.tech-kpis b{ display:block; font-size:.92rem; font-weight:900; color:#0f172a; }

.mini{
  border:1px solid #eef2f7; border-radius:12px; padding:10px;
  display:flex; justify-content:space-between; align-items:center;
}
.mini span{ font-size:.75rem; color:#64748b; letter-spacing:.06em; }
.mini b{ font-size:1rem; font-weight:900; }
</style>

<script>
(function(){
  const q = document.getElementById('qTech');
  const grid = document.getElementById('gridTech');
  if(!q || !grid) return;

  q.addEventListener('input', ()=>{
    const needle = (q.value || '').toLowerCase().trim();
    const cards = grid.querySelectorAll('.tech-card');
    cards.forEach(card=>{
      const alias = (card.getAttribute('data-alias') || '').toLowerCase();
      const name  = (card.getAttribute('data-name')  || '').toLowerCase();
      const ok = !needle || alias.includes(needle) || name.includes(needle);
      card.style.display = ok ? '' : 'none';
    });
  });
})();
</script>

<?php include_once('../components/footer.php'); ?>
