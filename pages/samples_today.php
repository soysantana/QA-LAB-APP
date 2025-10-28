<?php
declare(strict_types=1);
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

date_default_timezone_set('America/Santo_Domingo');

/* ===== Ajustes ===== */
const NEXT_API = '../api/samples_today_and_next.php'; // misma carpeta
$REGISTERED_IS_DATETIME = false;

/* Prefijos sugeridos */
$prefijos_id = ['LLD-258','S15','265','PVDJ-AGG25','PVDJ-AGG-INV25','PVDJ-AGG-DIO25','PVDJ-AGG-CF25','PVDJ-AGG-FF25'];
$prefijos_num = $prefijos_id; // puedes tener otros si tu SN usa prefijos distintos

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* Cargar “hoy” (opcional) */
if ($REGISTERED_IS_DATETIME) {
  $sqlToday = "
    SELECT id, Sample_ID, Sample_Number, Test_Type, Material_Type, Registed_Date
    FROM lab_test_requisition_form
    WHERE Registed_Date >= CURDATE() AND Registed_Date < CURDATE() + INTERVAL 1 DAY
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

      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-123"></i>
              <strong>Consecutivo considerando Sample_ID y Sample_Number</strong>
            </div>
            <span class="text-muted small">Endpoint: <?= e(NEXT_API) ?></span>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">ID Prefix (para Sample_ID)</label>
                <select id="idPrefixSelect" class="form-select">
                  <option value="">-- Selecciona --</option>
                  <?php foreach($prefijos_id as $p): ?>
                    <option value="<?= e($p) ?>"><?= e($p) ?></option>
                  <?php endforeach; ?>
                </select>
                <input id="idPrefixInput" type="text" class="form-control mt-2" placeholder="O escribe: p.ej. LLD-258">
              </div>

              <div class="col-md-4">
                <label class="form-label">Sample_Number mode</label>
                <select id="numberMode" class="form-select">
                  <option value="number_only" selected>number_only (solo número)</option>
                  <option value="with_prefix">with_prefix (lleva prefijo)</option>
                </select>

                <div id="numberPrefixBox" class="mt-2 d-none">
                  <label class="form-label">Number Prefix (para Sample_Number con prefijo)</label>
                  <select id="numberPrefixSelect" class="form-select">
                    <option value="">-- Selecciona --</option>
                    <?php foreach($prefijos_num as $p): ?>
                      <option value="<?= e($p) ?>"><?= e($p) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input id="numberPrefixInput" type="text" class="form-control mt-2" placeholder="O escribe: p.ej. LLD-258">
                </div>
              </div>

              <div class="col-md-2">
                <label class="form-label">Padding</label>
                <input id="padLen" type="number" class="form-control" value="4" min="1" max="10">
              </div>

              <div class="col-md-2 d-grid align-items-end">
                <button id="btnCheck" class="btn btn-primary"><i class="bi bi-magic"></i> Obtener Siguiente</button>
              </div>
            </div>

            <hr>

            <div id="resultArea" class="row g-3 d-none">
              <div class="col-lg-6">
                <div class="card h-100 border-success">
                  <div class="card-header bg-success text-white">
                    <i class="bi bi-lightning-charge"></i> Recomendación sin colisiones
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
                        <label class="form-label">Sample Name</label>
                        <input id="sampleName" class="form-control" readonly>
                      </div>
                      <div class="col-12">
                        <div class="alert alert-success py-2 mb-0">
                          Consecutivo: <strong id="recNext"></strong>
                          <span class="text-muted"> (padded: <span id="recPad"></span>)</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer small text-muted">
                    Calculado con máximos en Sample_ID (<?= e('id_prefix') ?>) y Sample_Number (según modo/prefijo), y validado vs. colisiones.
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="card h-100">
                  <div class="card-header">
                    <i class="bi bi-journal-check"></i> Estado (informativo)
                  </div>
                  <div class="card-body">
                    <div class="row g-2">
                      <div class="col-6">
                        <div class="border rounded p-2 h-100">
                          <div class="fw-bold">Sample_ID</div>
                          <div class="small text-muted">Máximo detectado:</div>
                          <div class="display-6" id="maxId">–</div>
                          <div class="small text-muted">Siguiente id-only:</div>
                          <div class="fw-bold" id="nextIdVal">–</div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="border rounded p-2 h-100">
                          <div class="fw-bold">Sample_Number</div>
                          <div class="small text-muted">Ámbito:</div>
                          <div class="small" id="numScope">–</div>
                          <div class="small text-muted mt-2">Máximo detectado:</div>
                          <div class="display-6" id="maxNum">–</div>
                          <div class="small text-muted">Siguiente num-only:</div>
                          <div class="fw-bold" id="nextNumVal">–</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer small text-muted">
                    ID Prefix: <span id="resolvedIdPrefix">–</span>
                    <span class="ms-3">Number Prefix: <span id="resolvedNumPrefix">–</span></span>
                  </div>
                </div>
              </div>
            </div>

            <div id="emptyHint" class="alert alert-info mt-3">
              Elige/escribe un <b>ID Prefix</b>. Si <b>Sample_Number</b> lleva prefijo, selecciona <b>with_prefix</b> y define <b>Number Prefix</b>. Luego pulsa <b>Obtener Siguiente</b>.
            </div>

            <div id="errorBox" class="alert alert-danger mt-3 d-none"></div>
          </div>
        </div>
      </div>

      <!-- Lista de hoy -->
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
                  <th>#</th><th>Sample Name</th><th>Sample ID</th><th>Sample Number</th><th>Test Type</th><th>Material</th><th>Registered</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($rows)): ?>
                  <tr><td colspan="7" class="text-center text-muted">Sin registros hoy.</td></tr>
                <?php else: foreach ($rows as $i=>$r):
                  $id = $r['Sample_ID'] ?? '';
                  $num= $r['Sample_Number'] ?? '';
                  $name = $id.' | '.$num;
                ?>
                  <tr>
                    <td><?= (int)($i+1) ?></td>
                    <td><?= e($name) ?></td>
                    <td><?= e($id) ?></td>
                    <td><?= e($r['Sample_Number'] ?? '') ?></td>
                    <td><?= e($r['Test_Type'] ?? '') ?></td>
                    <td><?= e($r['Material_Type'] ?? '') ?></td>
                    <td><?= e($r['Registed_Date'] ?? '') ?></td>
                  </tr>
                <?php endforeach; endif; ?>
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
  const $ = s=>document.querySelector(s);

  const idSel = $('#idPrefixSelect');
  const idInp = $('#idPrefixInput');
  const mode  = $('#numberMode');
  const nBox  = $('#numberPrefixBox');
  const nSel  = $('#numberPrefixSelect');
  const nInp  = $('#numberPrefixInput');
  const padEl = $('#padLen');
  const btn   = $('#btnCheck');

  const resWrap = $('#resultArea');
  const hint    = $('#emptyHint');
  const errBox  = $('#errorBox');

  const useForID   = $('#useForID');
  const useForNum  = $('#useForNumber');
  const sampleName = $('#sampleName');
  const recNext    = $('#recNext');
  const recPad     = $('#recPad');
  const maxId      = $('#maxId');
  const nextIdVal  = $('#nextIdVal');
  const maxNum     = $('#maxNum');
  const nextNumVal = $('#nextNumVal');
  const scopeEl    = $('#numScope');
  const idShow     = $('#resolvedIdPrefix');
  const numShow    = $('#resolvedNumPrefix');

  idSel?.addEventListener('change',()=>{ idInp.value = idSel.value || ''; });
  nSel?.addEventListener('change',()=>{ nInp.value = nSel.value || ''; });
  mode?.addEventListener('change',()=>{ nBox.classList.toggle('d-none', mode.value!=='with_prefix'); });

  btn?.addEventListener('click', async ()=>{
    const idPrefix = (idInp.value || idSel.value || '').trim();
    if (!idPrefix) return showError('Debes indicar el ID Prefix (para Sample_ID).');

    const numberMode = mode.value || 'number_only';
    const pad = Math.max(1, parseInt(padEl.value||'4',10));
    let qs = `action=next&id_prefix=${encodeURIComponent(idPrefix)}&number_mode=${encodeURIComponent(numberMode)}&pad=${pad}`;

    if (numberMode==='with_prefix') {
      const numberPrefix = (nInp.value || nSel.value || '').trim() || idPrefix;
      qs += `&number_prefix=${encodeURIComponent(numberPrefix)}`;
    }

    await doFetch(qs, idPrefix);
  });

  function toggleLoading(b){
    btn.disabled = b;
    btn.innerHTML = b?'<span class="spinner-border spinner-border-sm"></span> Consultando...':'<i class="bi bi-magic"></i> Obtener Siguiente';
  }
  function showError(msg, html){
    errBox.classList.remove('d-none');
    errBox.innerHTML = html
      ? `<div class="mb-2">${msg}</div><details><summary>Detalle</summary><div class="mt-2 border p-2" style="max-height:260px;overflow:auto;white-space:pre-wrap;">${html}</div></details>`
      : msg;
    resWrap.classList.add('d-none');
  }

  async function doFetch(qs, idPrefix){
    try{
      toggleLoading(true);
      errBox.classList.add('d-none');

      const url = `<?= e(NEXT_API) ?>?${qs}`;
      const res = await fetch(url, { headers:{'Accept':'application/json'}, credentials:'same-origin' });
      const ct = (res.headers.get('content-type')||'').toLowerCase();
      const text = await res.text();

      if (!ct.includes('application/json')) {
        const snip = text.substring(0,2000).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        return showError('El endpoint devolvió HTML en vez de JSON. Revisa sesión/ruta.', snip);
      }
      let data;
      try { data = JSON.parse(text); } catch(e){
        const snip = text.substring(0,2000).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        return showError('JSON inválido recibido.', snip);
      }
      if (!res.ok || !data.ok) return showError(data?.error || `HTTP ${res.status}`);

      const next = data.recommended, status = data.status;
      if (!next || !status) return showError('Respuesta inesperada: faltan bloques "recommended" o "status".');

      useForID.value   = next.use_for_id     ?? '';
      useForNum.value  = next.use_for_number ?? '';
      sampleName.value = next.sample_name    ?? '';
      recNext.textContent = (next.recommended_next ?? '—').toString();
      recPad.textContent  = next.next_padded ?? '—';

      maxId.textContent     = (typeof status.sample_id?.max_found==='number') ? status.sample_id.max_found : '—';
      nextIdVal.textContent = status.sample_id?.next_id ?? '—';
      maxNum.textContent    = (typeof status.sample_number?.max_found==='number') ? status.sample_number.max_found : '—';
      nextNumVal.textContent= status.sample_number?.next_value ?? '—';
      scopeEl.textContent   = status.sample_number?.scope ?? '—';

      idShow.textContent  = data.params?.id_prefix ?? idPrefix;
      numShow.textContent = data.params?.number_prefix ?? (mode.value==='with_prefix' ? '(id_prefix)' : '(numérico)');

      hint.classList.add('d-none');
      resWrap.classList.remove('d-none');

    } catch (err){
      showError(err.message || 'Error desconocido.');
    } finally {
      toggleLoading(false);
    }
  }
})();
</script>

<?php include_once('../components/footer.php'); ?>
