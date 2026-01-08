<?php
$page_title = 'Muestras Registradas';
$requisition_form = 'show';
require_once('../config/load.php');

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

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"></h5>

            <?php
            $twoMonthsAgo = date('Y-m-d', strtotime('-6 months'));

            // Traer requisiciones recientes
            $query = "SELECT id, Package_ID, Sample_ID, Sample_Number, Test_Type, Comment, Sample_By, Sample_Date, Registed_Date 
                        FROM lab_test_requisition_form 
                        WHERE Registed_Date >= '$twoMonthsAgo' 
                        ORDER BY Registed_Date DESC";
            $RequisitionRows = find_by_sql($query);

            // Inicializar
            $paquetes = [];
            $paquetesInfo = [];
            $muestras = [];

            // Recorremos filas
            foreach ($RequisitionRows as $row) {
              $packageId = trim($row['Package_ID']);

              // Saltar registros sin Package_ID válido
              if (empty($packageId) || $packageId === '-') {
                continue;
              }

              // tests solicitados (array limpio)
              $testsSolicitados = array_map('trim', array_filter(explode(',', $row['Test_Type'] ?? '')));

              // Escapar valores para la consulta (ajusta si tienes un escape mejor)
              $sampleIdRaw = trim($row['Sample_ID']);
              $sampleNumberRaw = trim($row['Sample_Number']);
              $sampleIdSql = addslashes($sampleIdRaw);
              $sampleNumberSql = addslashes($sampleNumberRaw);

              // Traer lista de tests entregados para esta muestra (lista, no COUNT)
              $deliveryQuery = "SELECT DISTINCT TRIM(Test_Type) AS Test_Type
                      FROM test_delivery
                      WHERE Sample_ID = '{$sampleIdSql}'
                      AND Sample_Number = '{$sampleNumberSql}'";
              $deliveryRows = find_by_sql($deliveryQuery);

              // Normalizar resultado en array simple de strings
              $testsEntregados = [];
              if (!empty($deliveryRows) && is_array($deliveryRows)) {
                foreach ($deliveryRows as $dr) {
                  if (!empty($dr['Test_Type'])) {
                    $testsEntregados[] = trim($dr['Test_Type']);
                  }
                }
              }

              // Contadores
              $ensayosSolicitados = count($testsSolicitados);
              $ensayosEntregados = count(array_unique(array_filter($testsEntregados)));
              $porcentaje = $ensayosSolicitados > 0 ? round(($ensayosEntregados / $ensayosSolicitados) * 100, 2) : 0;

              // Llenar paquete (lista de tests a nivel paquete)
              foreach ($testsSolicitados as $t) {
                if ($t !== '') $paquetes[$packageId][] = $t;
              }

              // Info del paquete (puede sobrescribirse, se queda la última - si quieres conservar la primera, envuélvelo con isset())
              $paquetesInfo[$packageId] = [
                'Comment'       => $row['Comment'],
                'Sample_By'     => $row['Sample_By'],
                'Sample_Date'   => $row['Sample_Date'],
                'Registed_Date' => $row['Registed_Date']
              ];

              // Guardar muestra con todos los datos necesarios
              $muestras[$packageId][] = [
                'Sample_ID'           => $row['Sample_ID'],
                'Sample_Number'       => $row['Sample_Number'],
                'Comment'             => $row['Comment'],
                'ensayos_solicitados' => $ensayosSolicitados,
                'ensayos_entregados'  => $ensayosEntregados,
                'porcentaje'          => $porcentaje,
                'tests_solicitados'   => $testsSolicitados,
                'tests_entregados'    => $testsEntregados
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
                  $sampleNumbers = array_map(fn($m) => $m['Sample_Number'] ?? '', $muestras[$packageId] ?? []);

                  // --- A nivel paquete: unir todos los tests solicitados y entregados ---
                  $allSolicitados = [];
                  $allEntregados  = [];

                  if (!empty($muestras[$packageId]) && is_array($muestras[$packageId])) {
                    foreach ($muestras[$packageId] as $mItem) {
                      // Asegurarse que sean arrays
                      $allSolicitados = array_merge($allSolicitados, (array)($mItem['tests_solicitados'] ?? []));
                      $allEntregados  = array_merge($allEntregados, (array)($mItem['tests_entregados'] ?? []));
                    }
                  }

                  // Limpiar y contar (sin quitar duplicados)
                  $ensayosSolicitados = count(array_filter(array_map('trim', $allSolicitados)));
                  $ensayosEntregados  = count(array_filter(array_map('trim', $allEntregados)));

                  $porce_entregados = $ensayosSolicitados > 0
                    ? round(($ensayosEntregados / $ensayosSolicitados) * 100, 2)
                    : 0;
                  ?>
                  <tr>
                    <th scope="row"><?php echo count_id(); ?></th>
                    <td><?php echo htmlspecialchars($sampleID); ?></td>
                    <td>
                      <?php foreach ($sampleNumbers as $sn): ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($sn); ?></span>
                      <?php endforeach; ?>
                    </td>
                    <td><span class="badge bg-primary"><?php echo $ensayosSolicitados; ?></span></td>
                    <td><span class="badge bg-success"><?php echo $ensayosEntregados; ?></span></td>
                    <td>
                      <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $porce_entregados; ?>%">
                          <?php echo $porce_entregados; ?>%
                        </div>
                      </div>
                    </td>
                    <td>
                      <form action="requisition-form-edit.php" method="post" style="display:inline;">
                        <div class="btn-group" role="group">
                          <!-- Ver -->
                          <button type="button" class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#requisitionview<?php echo $packageId; ?>">
                            <i class="bi bi-eye"></i>
                          </button>
                          <!-- Editar -->

                          <input type="hidden" name="package_id" value="<?php echo $packageId; ?>">
                          <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pen"></i>
                          </button>

                          <!-- Eliminar -->
                          <button type="button"
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#ModalDelete"
                            data-package="<?php echo $packageId; ?>"
                            data-samplename="<?php echo $sampleID; ?>">
                            <i class="bi bi-trash"></i>
                          </button>

                        </div>
                      </form>
                    </td>
                  </tr>

                  <!-- Modal del paquete -->
                  <div class="modal" id="requisitionview<?php echo htmlspecialchars($packageId); ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Detalle del paquete: <?php echo htmlspecialchars($sampleID); ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="container">

                            <!-- Ensayos por muestra -->
                            <div class="card mb-3">
                              <div class="card-body">
                                <h5 class="card-title">Ensayos por muestra</h5>

                                <?php if (!empty($muestras[$packageId]) && is_array($muestras[$packageId])): ?>
                                  <?php foreach ($muestras[$packageId] as $mItem): ?>
                                    <p>
                                      <code class="small">Muestra: <?php echo htmlspecialchars($mItem['Sample_Number']); ?></code>
                                      <?php
                                      $testsReq = $mItem['tests_solicitados'] ?? [];
                                      $testsDel = $mItem['tests_entregados'] ?? [];
                                      $testsReq = array_map('trim', $testsReq);
                                      $testsDel = array_map('trim', $testsDel);
                                      ?>
                                      <?php if (!empty($testsReq)): ?>
                                        <?php foreach ($testsReq as $t):
                                          $hecho = in_array($t, $testsDel, true);
                                          $icono = $hecho ? '✅' : '❌';
                                        ?>
                                          <span class="badge bg-light text-dark me-1" style="font-size:0.85em;">
                                            <?php echo htmlspecialchars($t) . ' ' . $icono; ?>
                                          </span>
                                        <?php endforeach; ?>
                                      <?php else: ?>
                                        <em>No hay ensayos solicitados.</em>
                                      <?php endif; ?>
                                    </p>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <p><em>No hay muestras en este paquete.</em></p>
                                <?php endif; ?>

                              </div>
                            </div>



                            <!-- Comentarios de muestras -->
                            <div class="card mb-3">
                              <div class="card-body">
                                <h5 class="card-title">Comentarios por muestra</h5>
                                <?php foreach ($muestras[$packageId] ?? [] as $mItem): ?>
                                  <?php if (!empty($mItem['Comment'])): ?>
                                    <div class="mb-2">
                                      <p>
                                        <code>Muestra <?php echo htmlspecialchars($mItem['Sample_Number']); ?>:</code>
                                        <?php echo htmlspecialchars($mItem['Comment']); ?>
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
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($paquetesInfo[$packageId]['Sample_Date'] ?? ''); ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <h6><code>Fecha de Registro</code></h6>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($paquetesInfo[$packageId]['Registed_Date'] ?? ''); ?></span>
                                  </li>
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <h6><code>Muestra por</code></h6>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($paquetesInfo[$packageId]['Sample_By'] ?? ''); ?></span>
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

      <!-- Modal delete -->
      <div class="modal fade" id="ModalDelete" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
              <h5>¿Está seguro?</h5>
            </div>
            <div class="modal-body">
              <form id="deleteForm" method="post" action="../database/requisition-form/delete.php">
                <input type="hidden" name="package_id" id="deletePackageId">
                <input type="hidden" name="sample_name" id="deletesampleID">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="submit" class="btn btn-outline-danger" name="delete-requisition">Si</button>
              </form>
            </div>
          </div>
        </div>
      </div>


    </div>
  </section>

</main><!-- End #main -->

<!-- Script para pasar el valor -->
<script>
  var modalDelete = document.getElementById('ModalDelete')
  modalDelete.addEventListener('show.bs.modal', function(event) {
    var button = event.relatedTarget
    var packageId = button.getAttribute('data-package')
    var sampleID = button.getAttribute('data-samplename')
    var input = modalDelete.querySelector('#deletePackageId')
    var inputName = modalDelete.querySelector('#deletesampleID')
    input.value = packageId
    inputName.value = sampleID
  })
</script>

<?php include_once('../components/footer.php'); ?>