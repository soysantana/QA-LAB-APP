<?php
$page_title = 'Inventario de Muestras';
require_once('../config/load.php');
page_require_level(3);

// Parámetros de filtro (opcionales)
$fecha_limite = date('Y-m-d', strtotime('-12 month'));
$tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? $db->escape($_GET['tipo']) : null;
$desde = isset($_GET['desde']) && $_GET['desde'] !== '' ? $db->escape($_GET['desde']) : $fecha_limite;
$hasta = isset($_GET['hasta']) && $_GET['hasta'] !== '' ? $db->escape($_GET['hasta']) : date('Y-m-d');

$where = [];
$where[] = "(r.Sample_Type IN ('Shelby','Mazier','Lexan','Ring','Rock') OR FIND_IN_SET('Envio', r.Test_Type))";
$where[] = "r.Sample_Date BETWEEN '{$desde}' AND '{$hasta}'";
if ($tipo) { $where[] = "r.Sample_Type = '{$tipo}'"; }

$query = "
  SELECT r.*, i.sample_length, i.sample_weight, i.store_in, i.comment
  FROM lab_test_requisition_form r
  LEFT JOIN inalteratedsample i ON r.id = i.requisition_id
  WHERE " . implode(" AND ", $where) . "
  ORDER BY r.Sample_Date DESC
";
$result = $db->query($query);

// KPIs rápidos
$total = 0; $editadas = 0; $por_tipo = ['Shelby'=>0,'Mazier'=>0,'Lexan'=>0,'Ring'=>0,'Rock'=>0];
$rows = [];
while ($row = $db->fetch_assoc($result)) {
  $rows[] = $row;
  $total++;
  if (!empty($row['sample_length']) || !empty($row['sample_weight']) || !empty($row['store_in']) || !empty($row['comment'])) {
    $editadas++;
  }
  if (isset($por_tipo[$row['Sample_Type']])) $por_tipo[$row['Sample_Type']]++;
}
// Para DataTables / render
?>

<?php include_once('../components/header.php'); ?>

<style>
  /* Modern touch */
  .card { border-radius: 14px; }
  .badge-soft {
    background: rgba(13,110,253,.08);
    color: #0d6efd; border: 1px solid rgba(13,110,253,.2);
  }
  .badge-soft-success {
    background: rgba(25,135,84,.08);
    color: #198754; border:1px solid rgba(25,135,84,.2);
  }
  .badge-soft-secondary {
    background: rgba(108,117,125,.08);
    color:#6c757d; border:1px solid rgba(108,117,125,.2);
  }
  .table thead th { position: sticky; top: 0; z-index: 5; }
  .kpi { min-width: 190px; }
  .dark-toggle { cursor:pointer; }
  .table-sm td, .table-sm th { padding:.55rem .75rem; }
  .status-chip {
    display:inline-flex; align-items:center; gap:.4rem;
    border-radius: 999px; padding:.2rem .6rem; font-weight:600; font-size:.82rem;
  }
  .status-chip.ok { background: #e9f7ef; color:#198754; border:1px solid #cfe9d9; }
  .status-chip.pending { background:#fff4e5; color:#b76e00; border:1px solid #ffe3bd; }
  .status-chip.na { background:#eef2f7; color:#516273; border:1px solid #dee5ef; }
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
      <!-- NUEVO: Exportar LTRF + store_in -->
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

  <!-- KPIs -->
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
                <span class="badge badge-soft"><?= htmlspecialchars($t) ?>: <strong><?= $cnt ?></strong></span>
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
              <?php
              $i = 1;
              $modals = "";
              foreach ($rows as $row):
                $tieneDatos = (!empty($row['sample_length']) || !empty($row['sample_weight']) || !empty($row['store_in']) || !empty($row['comment']));
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
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>">
                      <i class="bi bi-pencil-square"></i> Editar
                    </button>
                    <?php if ($tieneDatos): ?>
                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>">
                      <i class="bi bi-check2-circle"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php
                ob_start();
              ?>
              <div class="modal fade" id="modalEditar<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $row['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                  <div class="modal-content">
                    <form action="../database/inventario/guardar_inalterada.php" method="POST" class="needs-validation" novalidate>
                      <input type="hidden" name="requisition_id" value="<?= $row['id'] ?>">

                      <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarLabel<?= $row['id'] ?>">Editar Información de Muestra</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                      </div>

                      <div class="modal-body">
                        <div class="row g-2">
                          <div class="col-12">
                            <label class="form-label">Nombre de Muestra</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['Sample_ID']) ?>" readonly>
                          </div>
                          <div class="col-6">
                            <label class="form-label">Número</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['Sample_Number']) ?>" readonly>
                          </div>
                          <div class="col-6">
                            <label class="form-label">Tipo</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['Sample_Type']) ?>" readonly>
                          </div>
                          <div class="col-6">
                            <label class="form-label">Prof. Desde (m)</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['Depth_From']) ?>" readonly>
                          </div>
                          <div class="col-6">
                            <label class="form-label">Hasta (m)</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['Depth_To']) ?>" readonly>
                          </div>

                          <div class="col-6">
                            <label class="form-label">Longitud (m)</label>
                            <input type="number" step="any" class="form-control" name="Sample_Length" value="<?= htmlspecialchars($row['sample_length']) ?>">
                            <div class="invalid-feedback">Ingrese un número válido.</div>
                          </div>
                          <div class="col-6">
                            <label class="form-label">Peso (kg)</label>
                            <input type="number" step="any" class="form-control" name="Sample_Weight" value="<?= htmlspecialchars($row['sample_weight']) ?>">
                            <div class="invalid-feedback">Ingrese un número válido.</div>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Ubicación</label>
                            <select class="form-select" name="Store_In">
                              <option value="" <?= empty($row['store_in']) ? 'selected' : '' ?>>Seleccionar…</option>
                              <option value="Stored_PVLab" <?= $row['store_in'] === 'Stored_PVLab' ? 'selected' : '' ?>>Almacenado en PVLab</option>
                              <option value="Sended_To" <?= $row['store_in'] === 'Sended_To' ? 'selected' : '' ?>>Muestra Enviada</option>
                            </select>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Comentario</label>
                            <input type="text" class="form-control" name="Comment" value="<?= htmlspecialchars($row['comment']) ?>">
                          </div>

                          <input type="hidden" name="Sample_Date" value="<?= htmlspecialchars($row['Sample_Date']) ?>">
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
              <?php
                $modals .= ob_get_clean();
              endforeach; ?>
            </tbody>
          </table>
          <?= $modals ?>
        </div>
      </div>
    </div>
  </section>
</main>

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
          <?php foreach (array_keys($por_tipo) as $t): ?>
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

<!-- Toast de guardado -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
  <div id="toastGuardado" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bi bi-check2-circle text-success me-2"></i>
      <strong class="me-auto">Cambios guardados</strong>
      <small>Ahora</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
      La información de la muestra se actualizó correctamente.
    </div>
  </div>
</div>

<?php include_once('../components/footer.php'); ?>

<script>
// Dark mode simple (persistente)
(function(){
  const html = document.documentElement;
  const key = 'pv-dark';
  const toggle = document.querySelector('.dark-toggle');
  const set = (on)=>{ on ? html.setAttribute('data-bs-theme','dark') : html.removeAttribute('data-bs-theme'); localStorage.setItem(key,on?'1':'0'); };
  set(localStorage.getItem(key)==='1');
  toggle?.addEventListener('click', ()=> set(!(localStorage.getItem(key)==='1')) );
})();

// Validación Bootstrap
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
      form.classList.add('was-validated');
      // Si pasa validación, mostramos toast (el backend redirige igual)
      if (form.checkValidity()) {
        const toastEl = document.getElementById('toastGuardado');
        if (toastEl) new bootstrap.Toast(toastEl).show();
      }
    }, false);
  });
})();

// DataTables (si ya está incluido globalmente)
document.addEventListener('DOMContentLoaded', function(){
  if (window.jQuery && $.fn.DataTable) {
    $('#tablaInventario').DataTable({
      pageLength: 25,
      order: [[6,'desc']],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      dom: 'Bfrtip',
      buttons: [
        { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Exportar visible', className: 'btn btn-outline-success btn-sm' },
        { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i> CSV', className: 'btn btn-outline-secondary btn-sm' },
        { extend: 'print', text: '<i class="bi bi-printer"></i> Imprimir', className: 'btn btn-outline-primary btn-sm' }
      ]
    });
  }
});
</script>
