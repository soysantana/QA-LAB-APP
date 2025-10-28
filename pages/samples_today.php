<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

date_default_timezone_set('America/Santo_Domingo');

// =============== Ajustes rápidos ===============
/**
 * Si tu columna Registed_Date es DATETIME, pon true; si es DATE, false
 */
$REGISTERED_IS_DATETIME = false;

/**
 * Endpoint (ruta) del API que calcula el consecutivo.
 * Cambia esto si tu archivo tiene otro nombre o ruta.
 * Ejemplos:
 *   '/api/consecutivo.php'
 *   '/pages/consecutivo.php'
 */
const CONSECUTIVE_API = '/api/samples_today_and_next.php';

// Prefijos sugeridos (personaliza libremente)
$prefijos= [
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
// ==============================================

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// ------- Cargar “muestras de hoy” -------
if ($REGISTERED_IS_DATETIME) {
  $sqlToday = "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date >= CURDATE()
      AND Registed_Date <  CURDATE() + INTERVAL 1 DAY
    ORDER BY Registed_Date DESC, id DESC
  ";
} else {
  $sqlToday = "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date = CURDATE()
    ORDER BY id DESC
  ";
}
$rows = find_by_sql($sqlToday);
$todayCount = is_array($rows) ? count($rows) : 0;
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Muestras del Día</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/pages/home.php">Home</a></li>
        <li class="breadcrumb-item active">Muestras del Día</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row g-3">

      <!-- Panel: Generar consecutivo -->
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-hash fs-5"></i>
              <strong>Consecutivo de Sample ID / Sample Number</strong>
            </div>
            <span class="text-muted small">API: <?= e(CONSECUTIVE_API) ?></span>
          </div>
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-md-4">
                <label class="form-label">Prefijo (elige uno)</label>
                <select id="prefixSelect" class="form-select">
                  <option value="">-- Selecciona --</option>
                  <?php foreach($prefijos as $p): ?>
                    <option value="<?= e($p) ?>"><?= e($p) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-5">
                <label class="form-label">O escribe tu prefijo</label>
                <input id="prefixInput" type="text" class="form-control" placeholder="Ej: PVDJ-AGG25">
              </div>
              <div class="col-md-3 d-grid">
                <button id="btnCheck" class="btn btn-primary">
                  <i class="bi bi-magic"></i> Obtener Siguiente
                </button>
              </div>
            </div>

            <hr>

            <div id="resultArea" class="row g-3 d-none">
              <div class="col-lg-6">
                <div class="card h-100 border-success">
                  <div class="card-header bg-success text-white">
                    <i class="bi bi-lightning-charge"></i> Sugerencia unificada
                  </div>
                  <div class="card-body">
                    <div class="row g-2">
                      <div class="col-6">
                        <label class="form-label">Sample ID (usar)</label>
                        <input id="useForID" class="form-control" readonly>
                      </div>
                      <div class="col-6">
                        <label class="form-label">Sample Number (usar)</label>
                        <input id="useForNumber" class="form-control" readonly>
                      </div>
                      <div class="col-12">
                        <label class="form-label">Sample Name (ID + Number)</label>
                        <input id="sampleName" class="form-control" readonly>
                      </div>
                      <div class="col-12">
                        <div class="alert alert-success py-2 mb-0">
                          Consecutivo recomendado: <strong id="recNext"></strong>
                          <span class="text-muted"> (padded: <span id="recPad"></span>)</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer small text-muted">
                    Sin colisión en ID, Number ni en la combinación.
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="card h-100">
                  <div class="card-header">
                    <i class="bi bi-journal-check"></i> Estado actual por columna
                  </div>
                  <div class="card-body">
                    <div class="row g-2">
                      <div class="col-6">
                        <div class="border rounded p-2 h-100">
                          <div class="fw-bold mb-1">Sample_ID</div>
                          <div class="small text-muted">Último encontrado:</div>
                          <div class="display-6" id="maxId">–</div>
                          <div class="small text-muted">Siguiente (independiente):</div>
                          <div class="fw-bold" id="nextIdVal">–</div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="border rounded p-2 h-100">
                          <div class="fw-bold mb-1">Sample_Number</div>
                          <div class="small text-muted">Último encontrado:</div>
                          <div class="display-6" id="maxNum">–</div>
                          <div class="small text-muted">Siguiente (independiente):</div>
                          <div class="fw-bold" id="nextNumVal">–</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer small text-muted">
                    Prefijo resuelto/año: <span id="resolvedPrefix">–</span>
                  </div>
                </div>
              </div>
            </div>

            <div id="emptyHint" class="alert alert-info mt-3 d-none">
              Escribe o selecciona un prefijo y pulsa <b>Obtener Siguiente</b>.
            </div>

            <div id="errorBox" class="alert alert-danger mt-3 d-none"></div>
          </div>
        </div>
      </div>

      <!-- Panel: Lista de hoy -->
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-calendar-day"></i>
              <strong>Registradas hoy (<?= (int)$todayCount ?>)</strong>
            </div>
          </div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped align-middle">
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
              <tbody>
                <?php if (empty($rows)): ?>
                  <tr><td colspan="7" class="text-center text-muted">Sin registros hoy.</td></tr>
                <?php else: ?>
                  <?php foreach ($rows as $i => $r): 
                    $id  = $r['Sample_ID'] ?? '';
                    $num = $r['Sample_Number'] ?? '';
                    $sampleName = $id . ' | ' . $num; // mismo formato de “nombre de muestra” que usa el API
                  ?>
                  <tr>
                    <td><?= (int)($i+1) ?></td>
                    <td><?= e($sampleName) ?></td>
                    <td><?= e($id) ?></td>
                    <td><?= e($num) ?></td>
                    <td><?= e($r['Test_Type'] ?? '') ?></td>
                    <td><?= e($r['Material_Type'] ?? '') ?></td>
                    <td><?= e($r['Registed_Date'] ?? '') ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

<script>
(function(){
  const $ = (sel)=>document.querySelector(sel);

  // --- Elementos UI ---
  const prefixSelect = $('#prefixSelect');
  const prefixInput  = $('#prefixInput');
  const btnCheck     = $('#btnCheck');

  const boxResult = $('#resultArea');
  const boxEmpty  = $('#emptyHint');
  const boxError  = $('#errorBox');

  const useForID     = $('#useForID');
  const useForNumber = $('#useForNumber');
  const sampleName   = $('#sampleName');
  const recNext      = $('#recNext');
  const recPad       = $('#recPad');
  const maxId        = $('#maxId');
  const nextIdVal    = $('#nextIdVal');
  const maxNum       = $('#maxNum');
  const nextNumVal   = $('#nextNumVal');
  const resolvedPref = $('#resolvedPrefix');

  // Estado inicial
  boxEmpty.classList.remove('d-none');
  boxResult.classList.add('d-none');
  boxError.classList.add('d-none');

  prefixSelect?.addEventListener('change', () => {
    prefixInput.value = prefixSelect.value || '';
  });

  btnCheck?.addEventListener('click', async () => {
    const input = (prefixInput.value || prefixSelect.value || '').trim();
    if (!input) return showError('Debes seleccionar o escribir un prefijo (id_prefix).');
    await fetchNext(input);
  });

  function toggleLoading(isLoading){
    if (!btnCheck) return;
    btnCheck.disabled = isLoading;
    btnCheck.innerHTML = isLoading
      ? '<span class="spinner-border spinner-border-sm"></span> Consultando...'
      : '<i class="bi bi-magic"></i> Obtener Siguiente';
  }

  function showError(msg, html){
    console.error('[consecutivo] ' + msg, html || '');
    if (html) {
      boxError.innerHTML = `<div class="mb-2">${msg}</div>
        <details class="mt-2"><summary>Ver detalle</summary>
        <div class="mt-2 border p-2" style="max-height:260px; overflow:auto; white-space:pre-wrap;">${html}</div>
        </details>`;
    } else {
      boxError.textContent = msg;
    }
    boxError.classList.remove('d-none');
    boxResult.classList.add('d-none');
  }

  async function fetchNext(idPrefix) {
    try {
      toggleLoading(true);
      boxError.classList.add('d-none');

      // IMPORTANTE: usamos id_prefix (no "prefix"), y basamos el consecutivo SOLO en Sample_Number
      const url = `<?= e(CONSECUTIVE_API) ?>?id_prefix=${encodeURIComponent(idPrefix)}&number_mode=number_only&pad=4`;
      console.log('[consecutivo] GET', url);

      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      });

      const ct = (res.headers.get('content-type') || '').toLowerCase();
      const text = await res.text();
      console.log('[consecutivo] status', res.status, 'ct', ct);
      if (!ct.includes('application/json')) {
        const snippet = text.substring(0, 1500).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        return showError('El endpoint devolvió HTML en vez de JSON (login o warning de PHP). Revisa CONSECUTIVE_API y sesión.', snippet);
      }

      let data;
      try { data = JSON.parse(text); }
      catch (e) {
        const snippet = text.substring(0, 1500).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        return showError('JSON inválido recibido del endpoint.', snippet);
      }

      if (!res.ok || !data.ok) {
        return showError(data?.error || `HTTP ${res.status}`);
      }

      // Esperamos: data.recommended y data.status
      const next   = data.recommended;
      const status = data.status;
      if (!next || !status) {
        return showError('Respuesta inesperada: faltan bloques "recommended" o "status".');
      }

      // Pinta recomendación
      useForID.value         = next.use_for_id     ?? '';
      useForNumber.value     = next.use_for_number ?? '';
      sampleName.value       = next.sample_name    ?? '';
      recNext.textContent    = (next.recommended_next ?? '—').toString();
      recPad.textContent     = next.next_padded    ?? '—';

      // Informativo
      maxId.textContent      = (typeof status.sample_id?.max_found === 'number') ? status.sample_id.max_found : '—';
      nextIdVal.textContent  = status.sample_id?.next_id ?? '—';
      maxNum.textContent     = (typeof status.sample_number?.max_found === 'number') ? status.sample_number.max_found : '—';
      nextNumVal.textContent = status.sample_number?.next_value ?? '—';

      resolvedPref.textContent = data.params?.id_prefix ?? idPrefix;

      boxEmpty.classList.add('d-none');
      boxResult.classList.remove('d-none');
      console.log('[consecutivo] OK', data);

    } catch (err) {
      showError(err.message || 'Error desconocido.');
    } finally {
      toggleLoading(false);
    }
  }
})();
</script>



<?php include_once('../components/footer.php'); ?>
