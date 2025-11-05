<?php
// pages/sample_checklist.php — Detalle de Muestra (Checklist)
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$sid = trim($_GET['sid'] ?? '');
$num = trim($_GET['num'] ?? '');
$reqPdf  = isset($_GET['require_pdf']) ? (int)$_GET['require_pdf'] : 0; // 0: solo estado, 1: exigir PDF

if ($sid==='' || $num===''){
  echo '<div class="alert alert-warning m-3">Falta Sample_ID y/o Sample_Number.</div>';
  include_once('../components/footer.php'); exit;
}

// Metadatos (si existen)
$sidE = $db->escape($sid); $numE = $db->escape($num);
$meta = find_by_sql("
  SELECT Client, ProjectName, ProjectNumber, Structure, Area, Source, Sample_Date, Registed_Date
  FROM lab_test_requisition_form
  WHERE Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
    AND Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
  ORDER BY Registed_Date DESC LIMIT 1
");
$M = $meta[0] ?? [];

// Items de checklist
// Items de checklist (usando tus tablas reales y MySQL 8 con JSON_TABLE)
// Items de checklist (sin JSON_TABLE; build dinámico desde PHP)
$reqRow = find_by_sql("
  SELECT r.Test_Type
  FROM lab_test_requisition_form r
  WHERE r.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
    AND r.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
  ORDER BY r.Registed_Date DESC
  LIMIT 1
");

$rows = [];
if (!$reqRow) {
  // No hay requisición para este SID/NUM
  $rows = [];
} else {
  // 1) Expandir Test_Type en PHP
  $ttStr = (string)($reqRow[0]['Test_Type'] ?? '');
  $ttList = array_values(array_unique(array_filter(array_map(function($s){
    return strtoupper(trim($s));
  }, explode(',', $ttStr)))));

  if (empty($ttList)) {
    $rows = [];
  } else {
    // 2) Armar tabla virtual de ensayos: SELECT 'SP' AS Test_Type UNION ALL SELECT 'MC'...
    // (más portable que VALUES por compatibilidad)
    $ttSqlParts = [];
    foreach ($ttList as $i => $tt) {
      $ttSqlParts[] = "SELECT '{$db->escape($tt)}' AS Test_Type";
    }
    $ttSql = implode("\nUNION ALL\n", $ttSqlParts);

    // 3) Ejecutar la query única con tu lógica de estado y fechas
    $rows = find_by_sql("
      WITH tt AS (
        {$ttSql}
      )
      SELECT
        t.Test_Type,

        CASE
          WHEN EXISTS (
            SELECT 1 FROM test_repeat x
            WHERE x.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
              AND x.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
              AND UPPER(TRIM(x.Test_Type))=t.Test_Type
          ) THEN 'Repeat'
          WHEN EXISTS (
            SELECT 1
            FROM test_review rv
            LEFT JOIN test_reviewed rved
              ON rved.Sample_ID=rv.Sample_ID
             AND rved.Sample_Number=rv.Sample_Number
             AND UPPER(TRIM(rved.Test_Type))=UPPER(TRIM(rv.Test_Type))
            WHERE rv.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
              AND rv.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
              AND UPPER(TRIM(rv.Test_Type))=t.Test_Type
              AND rved.id IS NULL
          ) THEN 'Review'
          WHEN EXISTS (
            SELECT 1 FROM test_delivery d
            WHERE d.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
              AND d.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
              AND UPPER(TRIM(d.Test_Type))=t.Test_Type
          ) THEN 'Delivery'
          WHEN EXISTS (
            SELECT 1 FROM test_realization z
            WHERE z.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
              AND z.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
              AND UPPER(TRIM(z.Test_Type))=t.Test_Type
          ) THEN 'Realization'
          WHEN EXISTS (
            SELECT 1 FROM test_preparation p
            WHERE p.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
              AND p.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
              AND UPPER(TRIM(p.Test_Type))=t.Test_Type
          ) THEN 'Preparation'
          ELSE 'Pending'
        END AS Status,

        (SELECT MIN(p.Start_Date)
           FROM test_preparation p
           WHERE p.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
             AND p.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
             AND UPPER(TRIM(p.Test_Type))=t.Test_Type) AS Preparation_Date,

        (SELECT MIN(z.Start_Date)
           FROM test_realization z
           WHERE z.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
             AND z.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
             AND UPPER(TRIM(z.Test_Type))=t.Test_Type) AS Realization_Date,

        (SELECT MIN(d.Register_Date)
           FROM test_delivery d
           WHERE d.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
             AND d.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
             AND UPPER(TRIM(d.Test_Type))=t.Test_Type) AS Delivery_Date,

        (SELECT MIN(rv.Start_Date)
           FROM test_review rv
           LEFT JOIN test_reviewed rved
             ON rved.Sample_ID=rv.Sample_ID
            AND rved.Sample_Number=rv.Sample_Number
            AND UPPER(TRIM(rved.Test_Type))=UPPER(TRIM(rv.Test_Type))
           WHERE rv.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
             AND rv.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
             AND UPPER(TRIM(rv.Test_Type))=t.Test_Type
             AND rved.id IS NULL) AS Review_Date,

        (SELECT MIN(rp.Start_Date)
           FROM test_repeat rp
           WHERE rp.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
             AND rp.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci
             AND UPPER(TRIM(rp.Test_Type))=t.Test_Type) AS Repeat_Date,

        -- Estos campos no existen en tus tablas; los dejamos como NULL para que el front no falle.
        NULL AS Has_Report,
        NULL AS Is_Signed,
        NULL AS File_Path,

        -- Por si usas en UI:
        (SELECT MIN(r.Registed_Date)
           FROM lab_test_requisition_form r
           WHERE r.Sample_ID='{$sidE}' COLLATE utf8mb4_0900_ai_ci
             AND r.Sample_Number='{$numE}' COLLATE utf8mb4_0900_ai_ci) AS Requested_At

      FROM tt AS t
      ORDER BY t.Test_Type
    ");
  }
}


// Cálculo de listo/missing
$items = []; $ready=0; $requested=0; $missing=[];
foreach ($rows as $r){
  $requested++;
  $status = $r['Status'] ?? 'Pending';
  $hasPdf = (int)($r['Has_Report'] ?? 0) === 1;
  $isReady = in_array($status, ['Delivery','Review'], true) && (!$reqPdf || $hasPdf);
  if ($isReady) $ready++; else {
    $missing[] = [
      'Test_Type' => $r['Test_Type'],
      'Status'    => $status,
      'Has_PDF'   => (int)$hasPdf,
      'Note'      => ($reqPdf && !$hasPdf) ? 'Falta PDF' : 'Estado no final'
    ];
  }
  $items[] = $r;
}
$pct = $requested>0 ? round(($ready/$requested)*100,1) : 0.0;
$state = ($requested>0 && $ready===$requested) ? 'green' : (count($missing)>0 ? 'yellow':'red');

function state_badge(string $s): string {
  switch ($s) {
    case 'green':  return '<span class="badge text-bg-success"><i class="bi bi-check2-circle"></i> Completada</span>';
    case 'yellow': return '<span class="badge text-bg-warning"><i class="bi bi-gear-wide-connected"></i> En proceso</span>';
    default:       return '<span class="badge text-bg-danger"><i class="bi bi-exclamation-octagon"></i> Pendiente</span>';
  }
}

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script>
(function(){
  const saved = localStorage.getItem('theme')||'light';
  document.documentElement.setAttribute('data-bs-theme', saved);
})();
function toggleTheme(){
  const cur = document.documentElement.getAttribute('data-bs-theme')||'light';
  const next = cur==='light'?'dark':'light';
  document.documentElement.setAttribute('data-bs-theme', next);
  localStorage.setItem('theme', next);
}
function exportCSV(){
  const table = document.getElementById('tblChecklist');
  if (!table) return;
  const rows = [...table.querySelectorAll('tr')].map(tr =>
    [...tr.children].map(td => `"${(td.innerText||'').replace(/"/g,'""')}"`).join(',')
  );
  const blob = new Blob([rows.join('\n')], {type: 'text/csv;charset=utf-8;'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'sample_checklist.csv';
  a.click();
}
function copyTable(){
  const table = document.getElementById('tblChecklist');
  if (!table) return;
  const sel = window.getSelection(); const range = document.createRange();
  range.selectNodeContents(table); sel.removeAllRanges(); sel.addRange(range);
  document.execCommand('copy'); sel.removeAllRanges();
  alert('Tabla copiada.');
}
</script>
<style>
  .card{ border:none; border-radius:1rem; box-shadow:0 6px 20px rgba(0,0,0,.06); }
  .kpi .value{ font-size:1.4rem; font-weight:800; line-height:1; }
  .kpi .label{ opacity:.75; font-size:.85rem; }
  .table-modern thead th{ position:sticky; top:0; background:var(--bs-body-bg); z-index:1; }
  .table-modern tbody tr:hover{ background:rgba(0,0,0,.03); }
  [data-bs-theme="dark"] .table-modern tbody tr:hover{ background:rgba(255,255,255,.06); }
  .progress{ height:10px; border-radius:6px; }
</style>

<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Checklist de Muestra</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/pages/sample_tracker.php">Seguimiento</a></li>
        <li class="breadcrumb-item active"><?= e($sid) ?> / <?= e($num) ?></li>
      </ol></nav>
    </div>
    <div>
      <button class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()" title="Tema claro/oscuro"><i class="bi bi-moon-stars"></i></button>
    </div>
  </div>

  <!-- Encabezado -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2">
        <div class="col-md-4">
          <div><strong>Sample ID:</strong> <code><?= e($sid) ?></code></div>
          <div><strong>Sample Number:</strong> <code><?= e($num) ?></code></div>
          <div class="mt-1"><?= state_badge($state) ?></div>
        </div>
        <div class="col-md-4">
          <div><strong>Cliente:</strong> <?= e($M['Client'] ?? '—') ?></div>
          <div><strong>Proyecto:</strong> <?= e($M['ProjectName'] ?? '—') ?></div>
          <div><strong>Fecha muestra:</strong> <?= e($M['Sample_Date'] ?? '—') ?></div>
        </div>
        <div class="col-md-4">
          <div class="kpi d-flex align-items-center gap-3">
            <div class="value"><?= (int)$ready ?>/<?= (int)$requested ?></div>
            <div class="label">Ensayos listos / solicitados</div>
          </div>
          <div class="d-flex align-items-center gap-2 mt-2">
            <div class="progress flex-fill"><div class="progress-bar" style="width: <?= $pct ?>%"></div></div>
            <div class="small text-muted" style="width:50px;"><?= $pct ?>%</div>
          </div>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" id="swPdf" <?= $reqPdf?'checked':'' ?>
              onchange="location.href='sample_checklist.php?sid=<?= urlencode($sid) ?>&num=<?= urlencode($num) ?>&require_pdf='+(this.checked?1:0)">
            <label class="form-check-label" for="swPdf">Exigir PDF por ensayo (Nivel B)</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Checklist -->
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="card-title mb-0">Ensayos solicitados</h5>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-secondary btn-sm" onclick="exportCSV()"><i class="bi bi-table"></i> CSV</button>
          <button class="btn btn-outline-secondary btn-sm" onclick="copyTable()"><i class="bi bi-clipboard"></i> Copiar</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-modern table-striped align-middle" id="tblChecklist">
          <thead>
            <tr>
              <th>Test Type</th>
              <th>Estado</th>
              <th>Fechas</th>
              <th>Evidencia</th>
              <th>Observación</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!$items): ?>
            <tr><td colspan="5" class="text-center text-muted">No hay ensayos.</td></tr>
          <?php else:
            foreach ($items as $it):
              $st = $it['Status'] ?? 'Pending';
              $prep = $it['Preparation_Date'] ? date('Y-m-d H:i', strtotime($it['Preparation_Date'])) : '';
              $real = $it['Realization_Date'] ? date('Y-m-d H:i', strtotime($it['Realization_Date'])) : '';
              $delv = $it['Delivery_Date']    ? date('Y-m-d H:i', strtotime($it['Delivery_Date'])) : '';
              $rev  = $it['Review_Date']      ? date('Y-m-d H:i', strtotime($it['Review_Date'])) : '';
              $rep  = $it['Repeat_Date']      ? date('Y-m-d H:i', strtotime($it['Repeat_Date'])) : '';
              $pdf  = ((int)($it['Has_Report'] ?? 0)===1);
              $signed = ((int)($it['Is_Signed'] ?? 0)===1);
              $obs = !$reqPdf ? ($st==='Delivery'||$st==='Review' ? '' : 'Estado no final')
                              : (!$pdf ? 'Falta PDF' : (($st==='Delivery'||$st==='Review')?'':'Estado no final'));
          ?>
            <tr>
              <td><code><?= e($it['Test_Type']) ?></code></td>
              <td>
                <?php
                  $map=['Preparation'=>'primary','Realization'=>'warning','Delivery'=>'success','Review'=>'dark','Repeat'=>'secondary','Pending'=>'danger'];
                  $cls = $map[$st] ?? 'light';
                  echo '<span class="badge text-bg-'.$cls.'">'.e($st).'</span>';
                ?>
              </td>
              <td class="small">
                <?php if($prep): ?><div><span class="text-muted">Prep:</span> <?= e($prep) ?></div><?php endif; ?>
                <?php if($real): ?><div><span class="text-muted">Real:</span> <?= e($real) ?></div><?php endif; ?>
                <?php if($delv): ?><div><span class="text-muted">Ent:</span> <?= e($delv) ?></div><?php endif; ?>
                <?php if($rev):  ?><div><span class="text-muted">Rev:</span> <?= e($rev) ?></div><?php endif; ?>
                <?php if($rep):  ?><div><span class="text-muted">Rep:</span> <?= e($rep) ?></div><?php endif; ?>
              </td>
              <td class="small">
                <?= $pdf ? 'PDF: Sí' : 'PDF: —' ?>
                <?= $signed ? ' · Firmado' : '' ?>
              </td>
              <td class="small text-danger"><?= e($obs) ?></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <?php if (count($missing)>0): ?>
      <div class="alert alert-light border mt-3">
        <strong>Faltantes para marcar la muestra como Completada:</strong>
        <ul class="mb-0">
          <?php foreach($missing as $m): ?>
            <li><code><?= e($m['Test_Type']) ?></code> — <?= e($m['Note']) ?> (Estado: <?= e($m['Status']) ?><?= $m['Has_PDF']? ', PDF: Sí':', PDF: —' ?>)</li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include_once('../components/footer.php'); ?>
