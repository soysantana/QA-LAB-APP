<?php
$page_title = 'Inventario de Muestras';
require_once('../config/load.php');
page_require_level(3);

global $db;

// -----------------------------
// Helpers
// -----------------------------
function is_valid_date_ymd(string $d): bool {
  $dt = DateTime::createFromFormat('Y-m-d', $d);
  return $dt && $dt->format('Y-m-d') === $d;
}
function req_get(string $key, ?string $default=null): ?string {
  return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
}

// CSRF
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// -----------------------------
// Filtros
// -----------------------------
$fecha_limite = date('Y-m-d', strtotime('-12 month'));

$tipo  = req_get('tipo', '');
$desde = req_get('desde', $fecha_limite);
$hasta = req_get('hasta', date('Y-m-d'));

if (!is_valid_date_ymd($desde)) $desde = $fecha_limite;
if (!is_valid_date_ymd($hasta)) $hasta = date('Y-m-d');
if ($desde > $hasta) { $tmp=$desde; $desde=$hasta; $hasta=$tmp; }

$tipo = ($tipo !== '') ? $tipo : null;

// Toast
$saved = (isset($_GET['saved']) && $_GET['saved'] === '1');

// -----------------------------
// Query principal (prepared)
// -----------------------------
$sql = "
  SELECT 
    r.id, r.Sample_ID, r.Sample_Number, r.Sample_Type, r.Depth_From, r.Depth_To, r.Sample_Date, r.Test_Type,
    i.sample_length, i.sample_weight, i.store_in, i.comment
  FROM lab_test_requisition_form r
  LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
  WHERE
    (r.Sample_Type IN ('Shelby','Mazier','Lexan','Ring','Rock') OR FIND_IN_SET('Envio', r.Test_Type))
    AND r.Sample_Date BETWEEN ? AND ?
";
$params = [$desde, $hasta];
$types  = "ss";

if ($tipo) {
  $sql .= " AND r.Sample_Type = ? ";
  $params[] = $tipo;
  $types   .= "s";
}
$sql .= " ORDER BY r.Sample_Date DESC ";

$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

// -----------------------------
// Carga filas + KPIs
// -----------------------------
$total = 0; $editadas = 0;
$por_tipo = ['Shelby'=>0,'Mazier'=>0,'Lexan'=>0,'Ring'=>0,'Rock'=>0];
$rows = [];

while ($row = $res->fetch_assoc()) {
  $rows[] = $row;
  $total++;

  $has_length = ($row['sample_length'] !== null && $row['sample_length'] !== '');
  $has_weight = ($row['sample_weight'] !== null && $row['sample_weight'] !== '');
  $has_store  = ($row['store_in']      !== null && $row['store_in']      !== '');
  $has_comm   = ($row['comment']       !== null && $row['comment']       !== '');

  if ($has_length || $has_weight || $has_store || $has_comm) $editadas++;
  if (isset($por_tipo[$row['Sample_Type']])) $por_tipo[$row['Sample_Type']]++;
}
$stmt->close();

// -----------------------------
// Tipos dinámicos para filtros
// -----------------------------
$tipos_db = [];
$tipo_sql = "
  SELECT DISTINCT Sample_Type 
  FROM lab_test_requisition_form 
  WHERE (Sample_Type IN ('Shelby','Mazier','Lexan','Ring','Rock') OR FIND_IN_SET('Envio', Test_Type))
  ORDER BY Sample_Type ASC
";
$tipo_res = $db->query($tipo_sql);
while ($t = $db->fetch_assoc($tipo_res)) {
  $tipos_db[] = $t['Sample_Type'];
}
if (!$tipos_db) $tipos_db = array_keys($por_tipo);

?>

<?php include_once('../components/header.php'); ?>

<style>
  .card { border-radius: 14px; }
  .badge-soft { background: rgba(13,110,253,.08); color: #0d6efd; border: 1px solid rgba(13,110,253,.2); }
  .badge-soft-secondary { background: rgba(108,117,125,.08); color:#6c757d; border:1px solid rgba(108,117,125,.2); }
  .table thead th { position: sticky; top: 0; z-index: 5; }
  .kpi { min-width: 190px; }
  .dark-toggle { cursor:pointer; }
  .table-sm td, .table-sm th { padding:.55rem .75rem; }
  .status-chip { display:inline-flex; align-items:center; gap:.4rem; border-radius: 999px; padding:.2rem .6rem; font-weight:600; font-size:.82rem; }
  .status-chip.ok { background: #e9f7ef; color:#198754; border:1px solid #cfe9d9; }
  .status-chip.pending { background:#fff4e5; color:#b76e00; border:1px solid #ffe3bd; }
</style>

<main id="main" class="main">

  <div class="pagetitle d-flex align-items-center justify-content-between">
    <div>
      <h1 class="mb-1">Inventarios</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../pages/home.php">Home</a></li>
          <li class="breadcrumb-item">Pages</li>
          <li class="breadcrumb-item active"><a href="../components/menu_inventarios.php">Inventarios</a></li>
        </ol>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-outline-secondary dark-toggle" title="Modo oscuro">
        <i class="bi bi-moon-stars"></i>
      </button>

      <a href="../pages/sumary/export_inventario_muestras.php" class="btn btn-success">
        <i class="bi bi-file-earmark-excel"></i> Exportar Inventario
      </a>

      <a href="../pages/sumary/export_ltrf_samples.php?tipo=<?= urlencode((string)$tipo) ?>&desde=<?= urlencode((string)$desde) ?>&hasta=<?= urlencode((string)$hasta) ?>"
         class="btn btn-outline-success">
        <i class="bi bi-filetype-csv"></i> Inventario General
      </a>

      <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros">
        <i class="bi bi-funnel"></i> Filtros
      </button>
    </div>
  </div>

  <div class="pagetitle mb-3">
    <h1><i class="bi bi-box-seam"></i> Inventario de Muestras</h1>
  </div>

  <section class="section">
    <div class="row g-3 mb-3">
      <div class="col-12 col-md-4 col-lg-3">
        <div class="card shadow-sm kpi">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Total muestras (<?= htmlspecialchars($desde) ?> → <?= htmlspecialchars($hasta) ?>)</div>
                <div class="h4 mb-0"><?= number_format($total) ?></div>
              </div>
              <i class="bi bi-archive h3 text-primary"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-3">
        <div class="card shadow-sm kpi">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Muestras con datos añadidos</div>
                <div class="h4 mb-0"><?= number_format($editadas) ?></div>
              </div>
              <i class="bi bi-check2-circle h3 text-success"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <span class="text-muted small">Por tipo:</span>
              <?php foreach ($por_tipo as $t => $cnt): ?>
                <span class="badge badge-soft"><?= htmlspecialchars($t) ?>: <strong><?= (int)$cnt ?></strong></span>
              <?php endforeach; ?>
              <?php if ($tipo): ?>
                <span class="ms-auto badge bg-info">Filtro activo: <?= htmlspecialchars($tipo) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm">
      <div class="card-body pt-4">
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-sm align-middle datatable" id="tablaInventario">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Prof. Desde</th>
                <th>Hasta</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th style="width:140px;">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($rows as $row):
                $has_length = ($row['sample_length'] !== null && $row['sample_length'] !== '');
                $has_weight = ($row['sample_weight'] !== null && $row['sample_weight'] !== '');
                $has_store  = ($row['store_in']      !== null && $row['store_in']      !== '');
                $has_comm   = ($row['comment']       !== null && $row['comment']       !== '');
                $tieneDatos = ($has_length || $has_weight || $has_store || $has_comm);

                $estadoChip = $tieneDatos
                  ? '<span class="status-chip ok"><i class="bi bi-check-circle"></i> Editado</span>'
                  : '<span class="status-chip pending"><i class="bi bi-pencil"></i> Pendiente</span>';
              ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['Sample_ID']) ?></td>
                <td><?= htmlspecialchars($row['Sample_Number']) ?></td>
                <td><span class="badge badge-soft-secondary"><?= htmlspecialchars($row['Sample_Type']) ?></span></td>
                <td><?= htmlspecialchars($row['Depth_From']) ?></td>
                <td><?= htmlspecialchars($row['Depth_To']) ?></td>
                <td><?= htmlspecialchars($row['Sample_Date']) ?></td>
                <td><?= $estadoChip ?></td>
                <td>
                  <button
                    class="btn btn-sm btn-outline-primary jsEditBtn"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?= (int)$row['id'] ?>"
                    data-sample_id="<?= htmlspecialchars($row['Sample_ID'], ENT_QUOTES) ?>"
                    data-sample_number="<?= htmlspecialchars($row['Sample_Number'], ENT_QUOTES) ?>"
                    data-sample_type="<?= htmlspecialchars($row['Sample_Type'], ENT_QUOTES) ?>"
                    data-depth_from="<?= htmlspecialchars($row['Depth_From'], ENT_QUOTES) ?>"
                    data-depth_to="<?= htmlspecialchars($row['Depth_To'], ENT_QUOTES) ?>"
                    data-sample_date="<?= htmlspecialchars($row['Sample_Date'], ENT_QUOTES) ?>"
                    data-length="<?= htmlspecialchars((string)$row['sample_length'], ENT_QUOTES) ?>"
                    data-weight="<?= htmlspecialchars((string)$row['sample_weight'], ENT_QUOTES) ?>"
                    data-store_in="<?= htmlspecialchars((string)$row['store_in'], ENT_QUOTES) ?>"
                    data-comment="<?= htmlspecialchars((string)$row['comment'], ENT_QUOTES) ?>"
                  >
                    <i class="bi bi-pencil-square"></i> Editar
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </section>
</main>

<!-- Modal único -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <form action="../database/inventario/guardar_inalterada.php" method="POST" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="requisition_id" id="m_requisition_id">
        <input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarLabel">Editar Información de Muestra</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="row g-2">
            <div class="col-12">
              <label class="form-label">Nombre de Muestra</label>
              <input type="text" class="form-control" id="m_sample_id" readonly>
            </div>
            <div class="col-6">
              <label class="form-label">Número</label>
              <input type="text" class="form-control" id="m_sample_number" readonly>
            </div>
            <div class="col-6">
              <label class="form-label">Tipo</label>
              <input type="text" class="form-control" id="m_sample_type" readonly>
            </div>
            <div class="col-6">
              <label class="form-label">Prof. Desde (m)</label>
              <input type="text" class="form-control" id="m_depth_from" readonly>
            </div>
            <div class="col-6">
              <label class="form-label">Hasta (m)</label>
              <input type="text" class="form-control" id="m_depth_to" readonly>
            </div>

            <div class="col-6">
              <label class="form-label">Longitud (m)</label>
              <input type="number" step="any" class="form-control" name="Sample_Length" id="m_length">
              <div class="invalid-feedback">Ingrese un número válido.</div>
            </div>
            <div class="col-6">
              <label class="form-label">Peso (kg)</label>
              <input type="number" step="any" class="form-control" name="Sample_Weight" id="m_weight">
              <div class="invalid-feedback">Ingrese un número válido.</div>
            </div>

            <div class="col-12">
              <label class="form-label">Ubicación</label>
              <select class="form-select" name="Store_In" id="m_store_in">
                <option value="">Seleccionar…</option>
                <option value="Stored_PVLab">Almacenado en PVLab</option>
                <option value="Sended_To">Muestra Enviada</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Comentario</label>
              <input type="text" class="form-control" name="Comment" id="m_comment" maxlength="255">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Offcanvas Filtros -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFiltros">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title"><i class="bi bi-funnel"></i> Filtros</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form method="GET">
      <div class="mb-3">
        <label class="form-label">Tipo de muestra</label>
        <select class="form-select" name="tipo">
          <option value="">Todos</option>
          <?php foreach ($tipos_db as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= ($tipo===$t?'selected':'') ?>><?= htmlspecialchars($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-6">
          <label class="form-label">Desde</label>
          <input type="date" class="form-control" name="desde" value="<?= htmlspecialchars($desde) ?>">
        </div>
        <div class="col-6">
          <label class="form-label">Hasta</label>
          <input type="date" class="form-control" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
        </div>
      </div>
      <div class="d-grid">
        <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle"></i> Aplicar</button>
      </div>
    </form>
  </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
  <div id="toastGuardado" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bi bi-check2-circle text-success me-2"></i>
      <strong class="me-auto">Cambios guardados</strong>
      <small>Ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">La información de la muestra se actualizó correctamente.</div>
  </div>
</div>

<?php include_once('../components/footer.php'); ?>

<script>
// Dark mode
(function(){
  const html = document.documentElement;
  const key = 'pv-dark';
  const toggle = document.querySelector('.dark-toggle');
  const set = (on)=>{ on ? html.setAttribute('data-bs-theme','dark') : html.removeAttribute('data-bs-theme'); localStorage.setItem(key,on?'1':'0'); };
  set(localStorage.getItem(key)==='1');
  toggle?.addEventListener('click', ()=> set(!(localStorage.getItem(key)==='1')) );
})();

// Validation
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
      form.classList.add('was-validated');
    }, false);
  });
})();

// Load modal data
document.addEventListener('click', function(e){
  const btn = e.target.closest('.jsEditBtn');
  if (!btn) return;

  document.getElementById('m_requisition_id').value = btn.dataset.id || '';
  document.getElementById('m_sample_id').value      = btn.dataset.sample_id || '';
  document.getElementById('m_sample_number').value  = btn.dataset.sample_number || '';
  document.getElementById('m_sample_type').value    = btn.dataset.sample_type || '';
  document.getElementById('m_depth_from').value     = btn.dataset.depth_from || '';
  document.getElementById('m_depth_to').value       = btn.dataset.depth_to || '';
  document.getElementById('m_length').value         = btn.dataset.length || '';
  document.getElementById('m_weight').value         = btn.dataset.weight || '';
  document.getElementById('m_store_in').value       = btn.dataset.store_in || '';
  document.getElementById('m_comment').value        = btn.dataset.comment || '';
});

// DataTables + toast
document.addEventListener('DOMContentLoaded', function(){
  if (window.jQuery && $.fn.DataTable) {
    $('#tablaInventario').DataTable({
      pageLength: 25,
      order: [[6,'desc']],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
  }
  const params = new URLSearchParams(window.location.search);
  if (params.get('saved') === '1') {
    const toastEl = document.getElementById('toastGuardado');
    if (toastEl && window.bootstrap) new bootstrap.Toast(toastEl).show();
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('modalEditar');
  if (!modalEl) return;

  modalEl.addEventListener('show.bs.modal', function () {
    // Mover al body para evitar recortes por overflow/stacking del layout
    document.body.appendChild(modalEl);
  });
});

</script>
<style>
  /* Evita que el modal sea recortado por contenedores con overflow */
  .main, #main, .content, .content-wrapper {
    overflow: visible !important;
  }

  /* Asegura que el modal quede por encima */
  .modal { z-index: 2000; }
  .modal-backdrop { z-index: 1990; }
</style>
