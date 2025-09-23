<?php
$page_title = 'Muestras Registradas';
$requisition_form = 'show';
require_once('../config/load.php');

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete-requisition'])) {
    include('../database/requisition-form.php');
  }
}

page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Muestras Registradas</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Páginas</li>
        <li class="breadcrumb-item active">Formulario de requisicion</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4">
    <?php echo display_msg($msg); ?>
  </div>

  <section class="section">
    <div class="row">

      <form class="row" action="requisition-form-view.php" method="post">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"></h5>

              <?php

              $twoMonthsAgo = date('Y-m-d', strtotime('-2 months'));

              // Traer requisiciones recientes
              $query = "SELECT Package_ID, Sample_ID, Sample_Number, Test_Type, Comment, Sample_By, Sample_Date, Registed_Date 
          FROM lab_test_requisition_form 
          WHERE Registed_Date >= '$twoMonthsAgo' 
          ORDER BY Registed_Date DESC";
              $RequisitionRows = find_by_sql($query);

              // Agrupar por Package_ID
              $paquetes = [];
              $paquetesInfo = [];
              $muestras = [];

              foreach ($RequisitionRows as $row) {
                $packageId = trim($row['Package_ID']);

                // Saltar registros sin Package_ID válido
                if (empty($packageId) || $packageId === '-') {
                  continue;
                }

                // ---- Ensayos solicitados ----
                $tests = array_map('trim', explode(',', $row['Test_Type']));
                $ensayosSolicitados = count(array_filter($tests)); // total solicitados

                // ---- Ensayos entregados ----
                $sampleId = trim($row['Sample_ID']);
                $sampleNumber = trim($row['Sample_Number']);

                $deliveryQuery = "SELECT COUNT(DISTINCT Test_Type) AS entregados
                      FROM test_delivery
                      WHERE Sample_ID = '{$sampleId}'
                        AND Sample_Number = '{$sampleNumber}'";
                $deliveryResult = find_by_sql($deliveryQuery);
                $ensayosEntregados = !empty($deliveryResult) ? (int)$deliveryResult[0]['entregados'] : 0;

                // ---- Porcentaje ----
                $porcentaje = $ensayosSolicitados > 0
                  ? round(($ensayosEntregados / $ensayosSolicitados) * 100, 2)
                  : 0;

                // Agrupar todos los ensayos del paquete
                foreach ($tests as $t) {
                  if (!empty($t)) {
                    $paquetes[$packageId][] = $t;
                  }
                }

                // Guardar info del paquete
                $paquetesInfo[$packageId] = [
                  'Comment'       => $row['Comment'],
                  'Sample_By'     => $row['Sample_By'],
                  'Sample_Date'   => $row['Sample_Date'],
                  'Registed_Date' => $row['Registed_Date']
                ];

                // Guardar muestras con info de ensayos
                $muestras[$packageId][] = [
                  'Sample_ID'          => $row['Sample_ID'],
                  'Sample_Number'      => $row['Sample_Number'],
                  'Comment'            => $row['Comment'],
                  'ensayos_solicitados' => $ensayosSolicitados,
                  'ensayos_entregados' => $ensayosEntregados,
                  'porcentaje'         => $porcentaje
                ];
              }


              ?>



              <!-- Tabla -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Numero</th>
                    <th scope="col">Solicitados</th>
                    <th scope="col">Entregados</th>
                    <th scope="col">Progreso</th>
                    <th scope="col">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($paquetes as $packageId => $tests): ?>
                    <?php
                    // Sample_ID y lista de Sample_Number
                    $sampleID = $muestras[$packageId][0]['Sample_ID'] ?? $packageId;
                    $sampleNumbers = array_map(fn($m) => $m['Sample_Number'], $muestras[$packageId] ?? []);
                    ?>
                    <tr>
                      <th scope="row"><?php echo count_id(); ?></th>
                      <td>
                        <?php
                        $sampleID = $muestras[$packageId][0]['Sample_ID'] ?? $packageId;
                        echo htmlspecialchars($sampleID);
                        ?>
                      </td>
                      <td>
                        <?php foreach ($sampleNumbers as $sn): ?>
                          <span class="badge bg-secondary"><?php echo htmlspecialchars($sn); ?></span>
                        <?php endforeach; ?>
                      </td>
                      <td><span class="badge bg-primary"><?php echo $ensayosSolicitados; ?></span></td>
                      <td><span class="badge bg-success"><?php echo $ensayosEntregados; ?></span></td>
                      <td>
                        <div class="progress">
                          <div class="progress-bar" style="width: <?php /*echo $porce_entregados; */ ?>%">
                            <?php /*echo $porce_entregados; */ ?>%
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <!-- Ver -->
                          <button type="button" class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#requisitionview<?php echo $packageId; ?>">
                            <i class="bi bi-eye"></i>
                          </button>
                          <!-- Editar -->
                          <?php if (strpos($packageId, 'row_') === 0): ?>
                            <!-- Caso individual: pasar id real -->
                            <a href="requisition-form-edit.php?id=<?php echo $paquetesInfo[$packageId]['real_id']; ?>"
                              class="btn btn-warning">
                              <i class="bi bi-pen"></i>
                            </a>
                          <?php else: ?>
                            <!-- Caso agrupado: pasar package_id -->
                            <a href="requisition-form-edit.php?package_id=<?php echo urlencode($packageId); ?>"
                              class="btn btn-warning">
                              <i class="bi bi-pen"></i>
                            </a>
                          <?php endif; ?>
                          <!-- Eliminar -->
                          <button type="button" class="btn btn-danger"
                            onclick="modaldelete('<?php echo $packageId; ?>')">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal" id="requisitionview<?php echo $packageId; ?>" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Detalle del paquete: <?php echo htmlspecialchars($sampleID); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="container">

                              <!-- Ensayos -->
                              <div class="card mb-3">
                                <div class="card-body">
                                  <h5 class="card-title">Ensayos por muestra</h5>
                                  <?php foreach ($muestras[$packageId] as $m): ?>
                                    <div class="mb-2">
                                      <p>
                                        <span class="small">
                                          Muestra <?php echo htmlspecialchars($m['Sample_Number']); ?>:
                                          <?php
                                          $vistos_tests = [];
                                          foreach ($paquetes[$packageId] as $test):
                                            if (in_array($test, $vistos_tests)) continue; // Saltar duplicados
                                            $vistos_tests[] = $test;

                                            $hecho = isset($entregas[$packageId]) && in_array($test, $entregas[$packageId]);
                                            echo htmlspecialchars($test) . ' ' . ($hecho ? "✅" : "❌") . ' ';
                                          endforeach;
                                          ?>
                                        </span>
                                      </p>
                                    </div>
                                  <?php endforeach; ?>
                                </div>
                              </div>


                              <!-- Comentarios de muestras -->
                              <div class="card mb-3">
                                <div class="card-body">
                                  <h5 class="card-title">Comentarios por muestra</h5>
                                  <?php foreach ($muestras[$packageId] as $m): ?>
                                    <?php if (!empty($m['Comment'])): // Solo mostrar si hay comentario 
                                    ?>
                                      <div class="mb-2">
                                        <p>
                                          <code>Muestra <?php echo htmlspecialchars($m['Sample_Number']); ?>:
                                            <?php echo htmlspecialchars($m['Comment']); ?>
                                          </code>
                                        </p>
                                      </div>
                                    <?php endif; ?>
                                  <?php endforeach; ?>
                                </div>
                              </div>


                              <!-- Otros datos -->
                              <div class="card">
                                <div class="card-body">
                                  <h5 class="card-title">Otros datos</h5>
                                  <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h6><code>Fecha de la muestra</code></h6>
                                      <span class="badge bg-primary"><?php echo $paquetesInfo[$packageId]['Sample_Date']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h6><code>Fecha de Registro</code></h6>
                                      <span class="badge bg-primary"><?php echo $paquetesInfo[$packageId]['Registed_Date']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h6><code>Muestra por</code></h6>
                                      <span class="badge bg-primary"><?php echo $paquetesInfo[$packageId]['Sample_By']; ?></span>
                                    </li>
                                  </ul>
                                </div>
                              </div>

                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>

                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- End Table -->
            </div>
          </div>
        </div>
      </form>

      <!-- Modal delete -->
      <div class="modal fade" id="ModalDelete" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
              <h5>¿Está seguro?</h5>
            </div>
            <div class="modal-body">
              <form id="deleteForm" method="post" action="requisition-form-view.php">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="submit" class="btn btn-outline-danger" name="delete-requisition" onclick="Delete()">Si</button>
              </form>
            </div>
          </div>
        </div>
      </div><!-- End Modal -->

    </div>
  </section>

</main><!-- End #main -->

<script>
  var selectedId;

  function modaldelete(id) {
    selectedId = id;
    $('#ModalDelete').modal('show');
  }

  function Delete() {
    if (selectedId !== undefined) {
      document.getElementById("deleteForm").action = "requisition-form-view.php?package_id=" + selectedId;
      document.getElementById("deleteForm").submit();
    }
  }
</script>

<?php include_once('../components/footer.php'); ?>