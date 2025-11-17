<?php
$page_title = 'Lista de Pendientes';
$Pending_List = 'show';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Lista de Pendientes</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Paginas</li>
        <li class="breadcrumb-item active">Lista de Pendientes</li>
      </ol>
    </nav>
  </div>

  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <section class="section">
    <div class="row">
      <form class="row">

        <!-- =========================
             LISTADO PRINCIPAL
             Solo muestras EN PREPARACIÓN
             ========================= -->
        <div class="col-lg-9">
          <div class="card">
            <div class="card-body">

              <table class="table datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Muestra</th>
                    <th>Numero</th>
                    <th>Tipo de prueba</th>
                    <th>Fecha de muestra</th>
                    <th>Proceso</th> <!-- aquí será siempre Preparación -->
                    <th>Etapa</th>   <!-- etapa dentro de preparación (si existe en workflow) -->
                  </tr>
                </thead>
                <tbody>
                  <?php
                  /***********************************
                   * Helpers
                   ***********************************/
                  function normalize($v) {
                    return strtoupper(trim((string)$v));
                  }

                  /***********************************
                   * 1) Requisiciones (universo de ensayos)
                   ***********************************/
                  $requisitions = find_all("lab_test_requisition_form");

                  /***********************************
                   * 2) Índices por estado de proceso
                   ***********************************/
                  // a) En preparación
                  $prep_rows = find_all('test_preparation');
                  $has_prep = [];
                  foreach ($prep_rows as $row) {
                    $key = normalize($row['Sample_ID']) . "|" .
                           normalize($row['Sample_Number']) . "|" .
                           normalize($row['Test_Type']);
                    $has_prep[$key] = true;
                  }

                  // b) En realización
                  $real_rows = find_all('test_realization');
                  $has_real = [];
                  foreach ($real_rows as $row) {
                    $key = normalize($row['Sample_ID']) . "|" .
                           normalize($row['Sample_Number']) . "|" .
                           normalize($row['Test_Type']);
                    $has_real[$key] = true;
                  }

                  // c) Entregados
                  $del_rows = find_all('test_delivery');
                  $has_del = [];
                  foreach ($del_rows as $row) {
                    $key = normalize($row['Sample_ID']) . "|" .
                           normalize($row['Sample_Number']) . "|" .
                           normalize($row['Test_Type']);
                    $has_del[$key] = true;
                  }

                  // d) Revisados / cerrados
                  $rev_rows = find_all('test_reviewed');
                  $has_rev = [];
                  foreach ($rev_rows as $row) {
                    $key = normalize($row['Sample_ID']) . "|" .
                           normalize($row['Sample_Number']) . "|" .
                           normalize($row['Test_Type']);
                    $has_rev[$key] = true;
                  }

                  /***********************************
                   * 3) Workflow (para etapa dentro del proceso)
                   *    Opcional: si no lo tienes, no pasa nada
                   ***********************************/
                  $workflow_index = [];
                  try {
                    $workflow_rows = find_by_sql("
                      SELECT
                        Sample_ID,
                        Sample_Number,
                        Test_Type,
                        Status,
                        IFNULL(sub_stage, '') AS sub_stage,
                        Updated_At
                      FROM test_workflow
                      ORDER BY Updated_At DESC
                    ");

                    foreach ($workflow_rows as $w) {
                      $key = normalize($w['Sample_ID']) . "|" .
                             normalize($w['Sample_Number']) . "|" .
                             normalize($w['Test_Type']);
                      if (!isset($workflow_index[$key])) {
                        $workflow_index[$key] = $w; // más reciente
                      }
                    }
                  } catch (Exception $e) {
                    // Si no existe la tabla o columna, simplemente no habrá etapas
                    $workflow_index = [];
                  }

                  /***********************************
                   * 4) Construir LISTADO de muestras EN PREPARACIÓN
                   *    Regla: aparecen en test_preparation
                   *           y aún NO están en test_realization / test_delivery / test_reviewed
                   ***********************************/
                  $rowsLista = [];

                  foreach ($requisitions as $requisition) {
                    if (empty($requisition['Test_Type'])) continue;

                    $sample_id_norm  = normalize($requisition['Sample_ID']);
                    $sample_num_norm = normalize($requisition['Sample_Number']);
                    $date            = $requisition['Sample_Date'];

                    $testTypesArray = array_map('trim', explode(',', $requisition['Test_Type']));

                    foreach ($testTypesArray as $test_type_raw) {
                      $test_type_norm = normalize($test_type_raw);
                      $key = $sample_id_norm . "|" . $sample_num_norm . "|" . $test_type_norm;

                      // Solo queremos los que ESTÁN en preparación...
                      if (!isset($has_prep[$key])) {
                        continue;
                      }

                      // ...pero todavía NO han llegado a realización / entrega / revisado
                      if (isset($has_real[$key]) || isset($has_del[$key]) || isset($has_rev[$key])) {
                        continue;
                      }

                      // Proceso: Preparación (fijo)
                      $proceso = 'Preparación';
                      // Etapa: si existe en workflow, la tomamos
                      $etapa   = '';
                      if (isset($workflow_index[$key])) {
                        $wf = $workflow_index[$key];
                        // Solo si el Status en workflow también es Preparación, usamos Stage,
                        // si no, igual lo mostramos como referencia.
                        $etapa = !empty($wf['Stage']) ? $wf['Stage'] : '';
                      }

                      $rowsLista[] = [
                        'Sample_ID'     => $requisition['Sample_ID'],
                        'Sample_Number' => $requisition['Sample_Number'],
                        'Test_Type'     => $test_type_norm,
                        'Sample_Date'   => $date,
                        'Proceso'       => $proceso,
                        'Etapa'         => $etapa,
                      ];
                    }
                  }

                  // Ordenar por tipo de prueba (puedes cambiar a fecha o Sample_ID si quieres)
                  usort($rowsLista, fn($a, $b) => strcmp($a['Test_Type'], $b['Test_Type']));

                  /***********************************
                   * 5) Pintar filas
                   ***********************************/
                  foreach ($rowsLista as $index => $sample):
                    $proc = $sample['Proceso'];
                    $badgeClass = 'bg-warning'; // siempre preparación en esta vista
                  ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($sample['Sample_ID']); ?></td>
                      <td><?php echo htmlspecialchars($sample['Sample_Number']); ?></td>
                      <td><?php echo htmlspecialchars($sample['Test_Type']); ?></td>
                      <td><?php echo htmlspecialchars($sample['Sample_Date']); ?></td>
                      <td>
                        <span class="badge <?php echo $badgeClass; ?>">
                          <?php echo htmlspecialchars($proc); ?>
                        </span>
                      </td>
                      <td><?php echo htmlspecialchars($sample['Etapa']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <!-- =========================
             PANEL LATERAL: CONTEO
             Lógica nueva de "pendiente":
             pendiente = NO está en realization, delivery ni reviewed
             (aunque esté en preparación sigue siendo pendiente)
             ========================= -->
        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Conteo</h5>
              <ul class="list-group">
                <?php
                $typeCount   = [];
                $columnaTipo = [];

                // Detectar todos los tipos que existen en las requisiciones
                foreach ($requisitions as $req) {
                  if (!empty($req['Test_Type'])) {
                    $testTypesArray = array_map('trim', explode(',', $req['Test_Type']));
                    foreach ($testTypesArray as $testType) {
                      $columnaTipo[normalize($testType)] = 'Test_Type';
                    }
                  }
                }

                // Nuevo concepto de PENDIENTE:
                // Para cada ensayo solicitado, si NO está en realization, delivery ni reviewed => pendiente.
                $pendientes = [];

                foreach ($requisitions as $req) {
                  if (empty($req['Test_Type'])) continue;

                  $sample_id_norm  = normalize($req['Sample_ID']);
                  $sample_num_norm = normalize($req['Sample_Number']);

                  $testTypesArray = array_map('trim', explode(',', $req['Test_Type']));

                  foreach ($testTypesArray as $test_type_raw) {
                    $test_type_norm = normalize($test_type_raw);
                    $key = $sample_id_norm . "|" . $sample_num_norm . "|" . $test_type_norm;

                    // Si YA está en realización, entrega o revisado -> NO pendiente
                    if (isset($has_real[$key]) || isset($has_del[$key]) || isset($has_rev[$key])) {
                      continue;
                    }

                    // Sigue siendo pendiente (aunque esté en preparación o no)
                    $pendientes[] = [
                      'Sample_ID'     => $req['Sample_ID'],
                      'Sample_Number' => $req['Sample_Number'],
                      'Test_Type'     => $test_type_norm,
                      'Sample_Date'   => $req['Sample_Date'],
                    ];
                  }
                }

                // Contar pendientes por tipo de ensayo
                foreach ($pendientes as $p) {
                  $t = $p['Test_Type'];
                  $typeCount[$t] = ($typeCount[$t] ?? 0) + 1;
                }

                if (empty($typeCount)): ?>
                  <li class="list-group-item text-center text-muted">
                    No hay ensayos pendientes.
                  </li>
                <?php else: ?>
                  <?php foreach ($typeCount as $t => $count): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <code><?php echo htmlspecialchars($t); ?></code>
                        <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
                      </div>
                      <?php if (isset($columnaTipo[$t])): ?>
                        <a href="../pdf/pendings.php?type=<?php echo urlencode($t); ?>" target="_blank"
                          class="btn btn-secondary btn-sm ms-2" title="Generar PDF">
                          <i class="bi bi-printer"></i>
                        </a>
                      <?php else: ?>
                        <span class="badge bg-danger">Err</span>
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>

      </form>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
