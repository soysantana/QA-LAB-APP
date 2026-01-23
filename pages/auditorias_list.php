<?php
$page_title = "Auditorías del Laboratorio";
require_once "../config/load.php";
page_require_level(2);
include_once('../components/header.php');

$user = current_user();
?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Auditorías del Laboratorio</h1>
    <p class="text-muted">Registro de auditorías internas, externas y cruzadas del Laboratorio de Mecánica de Suelos.</p>
  </div>

  <!-- Filtros -->
  <div class="card mb-3">
    <div class="card-body">
      <form class="row g-2" method="get" action="">

        <div class="col-md-3">
          <label class="form-label">Desde</label>
          <input type="date" name="start" class="form-control"
                 value="<?php echo isset($_GET['start']) ? htmlentities($_GET['start']) : ''; ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label">Hasta</label>
          <input type="date" name="end" class="form-control"
                 value="<?php echo isset($_GET['end']) ? htmlentities($_GET['end']) : ''; ?>">
        </div>

        <div class="col-md-2">
          <label class="form-label">Estado</label>
          <select name="status" class="form-select">
            <option value="">Todos</option>
            <option value="Open"        <?php if(($_GET['status'] ?? '')=='Open') echo 'selected'; ?>>Open</option>
            <option value="In Progress" <?php if(($_GET['status'] ?? '')=='In Progress') echo 'selected'; ?>>In Progress</option>
            <option value="Closed"      <?php if(($_GET['status'] ?? '')=='Closed') echo 'selected'; ?>>Closed</option>
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label">Área</label>
          <input type="text" name="area" class="form-control"
                 placeholder="Granulometría, Proctor..."
                 value="<?php echo isset($_GET['area']) ? htmlentities($_GET['area']) : ''; ?>">
        </div>

        <div class="col-md-2">
          <label class="form-label">Buscar</label>
          <input type="text" name="q" class="form-control"
                 placeholder="Código, auditor..."
                 value="<?php echo isset($_GET['q']) ? htmlentities($_GET['q']) : ''; ?>">
        </div>

        <div class="col-12 mt-2">
          <button class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
          <a href="auditorias_list.php" class="btn btn-secondary">Limpiar</a>
          <a href="auditorias_form.php" class="btn btn-success float-end">
            <i class="bi bi-plus-circle"></i> Nueva Auditoría
          </a>
        </div>

      </form>
    </div>
  </div>

  <!-- Tabla -->
  <div class="card">
    <div class="card-body">

      <h5 class="card-title">Listado de Auditorías</h5>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Código</th>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Área</th>
              <th>Severidad</th>
              <th>Estado</th>
              <th>Auditor</th>
              <th>Relacionado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php
            global $db;

            $where = "1=1";

            if (!empty($_GET['start'])) {
              $start = $db->escape($_GET['start']);
              $where .= " AND Audit_Date >= '{$start}'";
            }
            if (!empty($_GET['end'])) {
              $end = $db->escape($_GET['end']);
              $where .= " AND Audit_Date <= '{$end}'";
            }
            if (!empty($_GET['status'])) {
              $status = $db->escape($_GET['status']);
              $where .= " AND Status = '{$status}'";
            }
            if (!empty($_GET['area'])) {
              $area = $db->escape($_GET['area']);
              $where .= " AND Area LIKE '%{$area}%'";
            }
            if (!empty($_GET['q'])) {
              $q = $db->escape($_GET['q']);
              $where .= " AND (
                  Audit_Code LIKE '%{$q}%'
                  OR Auditor LIKE '%{$q}%'
                  OR Audited LIKE '%{$q}%'
                  OR Findings LIKE '%{$q}%'
              )";
            }

            $sql = "
              SELECT *
              FROM auditorias_lab
              WHERE {$where}
              ORDER BY Audit_Date DESC, id DESC
              LIMIT 200
            ";
            $audits = find_by_sql($sql);

            if (!$audits):
          ?>
            <tr>
              <td colspan="10" class="text-center text-muted">
                No se encontraron auditorías con los filtros seleccionados.
              </td>
            </tr>
          <?php
            else:
              $i = 1;
              foreach ($audits as $a):
                // Colores rápidos para estado
                $badgeStatus = 'secondary';
                if ($a['Status'] == 'Open')        $badgeStatus = 'danger';
                if ($a['Status'] == 'In Progress') $badgeStatus = 'warning';
                if ($a['Status'] == 'Closed')      $badgeStatus = 'success';

                $badgeSeverity = 'secondary';
                if ($a['Severity'] == 'Minor')    $badgeSeverity = 'info';
                if ($a['Severity'] == 'Major')    $badgeSeverity = 'warning';
                if ($a['Severity'] == 'Critical') $badgeSeverity = 'danger';
          ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><strong><?php echo htmlentities($a['Audit_Code']); ?></strong></td>
              <td><?php echo htmlentities($a['Audit_Date']); ?></td>
              <td><?php echo htmlentities($a['Audit_Type']); ?></td>
              <td><?php echo htmlentities($a['Area']); ?></td>
              <td>
                <span class="badge bg-<?php echo $badgeSeverity; ?>">
                  <?php echo htmlentities($a['Severity']); ?>
                </span>
              </td>
              <td>
                <span class="badge bg-<?php echo $badgeStatus; ?>">
                  <?php echo htmlentities($a['Status']); ?>
                </span>
              </td>
              <td><?php echo htmlentities($a['Auditor']); ?></td>
              <td>
                <?php
                  $related = [];
                  if (!empty($a['Related_Sample_ID'])) $related[] = "Sample: ".$a['Related_Sample_ID'];
                  if (!empty($a['Related_Client']))    $related[] = "Client: ".$a['Related_Client'];
                  echo !empty($related) ? implode(" / ", $related) : "<span class='text-muted'>N/A</span>";
                ?>
              </td>
              <td>
          <div class="btn-group btn-group-sm" role="group">
  <a href="auditorias_form.php?id=<?php echo $a['id']; ?>" class="btn btn-primary" title="Editar">
    <i class="bi bi-pencil-square"></i>
  </a>

  <a href="../pdf/auditoria_pdf.php?id=<?php echo (int)$a['id']; ?>"
   class="btn btn-outline-dark"
   title="Generar PDF"
   target="_blank">
  <i class="bi bi-filetype-pdf"></i>
</a>


  <a href="auditorias_delete.php?id=<?php echo $a['id']; ?>" class="btn btn-outline-danger"
     title="Eliminar"
     onclick="return confirm('¿Seguro que deseas eliminar esta auditoría?');">
    <i class="bi bi-trash"></i>
  </a>
</div>


              </td>
            </tr>
          <?php
              endforeach;
            endif;
          ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</main>

<?php include_once('../components/footer.php'); ?>
