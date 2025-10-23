<?php
// pages/backup_resultados_firmados.php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');


if (session_status() === PHP_SESSION_NONE) session_start();

$defaultPeriod = date('Y-m');
$period = isset($_GET['period']) && preg_match('/^\d{4}\-(0[1-9]|1[0-2])$/', $_GET['period'])
  ? $_GET['period'] : $defaultPeriod;

[$yPreview, $mPreview] = explode('-', $period);
$yPreview = sprintf('%04d', (int)$yPreview);
$mPreview = sprintf('%02d', (int)$mPreview);

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$last = $_SESSION['last_backup_signed_results'] ?? null;


?>
<style>
  /* Estilo específico para esta pantalla */
  .backup-ui{
    font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    font-size: 15px;
    line-height: 1.45;
  }
  .backup-ui .pagetitle h1{
    font-size: 1.35rem;
    margin-bottom: .25rem;
  }
  .backup-ui code{ font-size: 90%; }
</style>
<main id="main" class="main backup-ui">


  <div class="pagetitle">
    <h1>Backups de resultados firmados</h1>
    <p class="text-muted">Genera y descarga el ZIP + manifiesto.</p>
  </div>

  <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info', ENT_QUOTES) ?>">
      <?= htmlspecialchars($flash['msg'] ?? '', ENT_QUOTES) ?>
    </div>
  <?php endif; ?>
  <?php
// Formatear la fecha del último backup a YYYY-MM-DD
$createdAtRaw = $last['created_at'] ?? '';
$createdAtFmt = '';
if ($createdAtRaw) {
  $ts = strtotime($createdAtRaw);
  if ($ts) {
    $createdAtFmt = date('Y-m-d', $ts); // <-- 2025-10-23
  }
}
?>


  <?php if ($last): ?>
  <section class="section">
    <div class="card border-success">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Último backup generado</strong>
        <span class="badge bg-success">OK</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-8">
            <ul class="mb-2">
              <li><strong>Periodo:</strong> <?= htmlspecialchars($last['year'].'-'.$last['month2'], ENT_QUOTES) ?></li>
              <li><strong>ZIP:</strong>
                <span><?= htmlspecialchars(basename($last['zip_web']), ENT_QUOTES) ?></span>
                (<?= number_format(($last['zip_size'] ?? 0)/1024/1024, 2) ?> MB)
              </li>
              <?php if (!empty($last['manifest_web'])): ?>
              <li><strong>Manifiesto:</strong> manifest.json</li>
              <?php endif; ?>
              <li><strong>Fecha:</strong> <?= htmlspecialchars($createdAtFmt, ENT_QUOTES) ?></li>

            </ul>
            <div class="small text-muted">
             
            </div>
          </div>

          <div class="col-md-4 d-flex flex-column align-items-end gap-2">
            <!-- Botones de DESCARGA sencilla (fuerza "Guardar como...") -->
            <div class="d-flex flex-wrap gap-2 justify-content-end">
              <a class="btn btn-primary"
                 href="/database/backup_resultados_firmados_download.php?type=zip">
                Descargar ZIP
              </a>
              <?php if (!empty($last['manifest_web'])): ?>
              <a class="btn btn-outline-primary"
                 href="/database/backup_resultados_firmados_download.php?type=manifest">
                Descargar manifest.json
              </a>
              <?php endif; ?>
            </div>

            <!-- Botón Purgar -->
            <form method="post" action="/database/backup_resultados_firmados_purge.php"
                  onsubmit="return confirm('¿Seguro que deseas purgar (eliminar) el ZIP y el manifiesto generados?');">
              <input type="hidden" name="nonce" value="<?= htmlspecialchars($last['nonce'], ENT_QUOTES) ?>">
              <button type="submit" class="btn btn-outline-danger">Borrar backup generado</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- Formulario de generación -->
  <section class="section">
    <div class="card">
      <div class="card-body">
        <form class="mt-3" method="post" action="/pages/backup_resultados_firmados_make.php" id="backupForm">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Periodo</label>
              <input type="month" class="form-control" id="period"
                     value="<?= htmlspecialchars($period, ENT_QUOTES) ?>"
                     min="2000-01" max="2100-12" required>
              <div class="form-text">Ej.: 2025-10</div>
            </div>
            <input type="hidden" name="year" id="year">
            <input type="hidden" name="month" id="month">
            <div class="col-md-4">
              <label class="form-label">Nombre del ZIP (opcional)</label>
              <input type="text" class="form-control" name="zip_name">
            </div>
            <div class="col-md-4">
              <label class="form-label">Manifiesto</label>
              <select class="form-select" name="checksum_algo">
                <option value="sha256" selected>SHA-256</option>
                <option value="sha1">SHA-1</option>
                <option value="md5">MD5</option>
                <option value="none">Sin manifiesto</option>
              </select>
            </div>
          </div>

          <div class="mt-3">
           

          <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Generar ZIP + Manifiesto</button>
            <a class="btn btn-secondary" href="/pages/docs_list.php">Volver</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<script>
(function(){
  const periodInput = document.getElementById('period');
  const yearInput   = document.getElementById('year');
  const monthInput  = document.getElementById('month');
  const y1 = document.getElementById('y1');
  const y2 = document.getElementById('y2');
  const m1 = document.getElementById('m1');
  const m2 = document.getElementById('m2');

  function syncFields() {
    const v = periodInput.value; // "YYYY-MM"
    if (!/^\d{4}\-(0[1-9]|1[0-2])$/.test(v)) return;
    const parts = v.split('-');
    const y = parts[0];
    const m = parts[1].replace(/^0/, '');

    yearInput.value  = y;
    monthInput.value = m;

    if (y1 && y2 && m1 && m2) {
      y1.textContent = y2.textContent = y;
      m1.textContent = m2.textContent = parts[1];
    }
  }

  syncFields();
  periodInput.addEventListener('change', syncFields);
  document.getElementById('backupForm').addEventListener('submit', function(e){
    syncFields();
    if (!yearInput.value || !monthInput.value) {
      e.preventDefault();
      alert('Selecciona un periodo válido (YYYY-MM).');
    }
  });
})();
</script>
<?php include_once('../components/footer.php'); ?>
