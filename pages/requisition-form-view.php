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

              // Traer todas las requisiciones recientes
              $query = "SELECT id, Sample_ID, Sample_Number, Test_Type, Comment, Sample_By, Sample_Date, Registed_Date 
                        FROM lab_test_requisition_form 
                        WHERE Registed_Date >= '$twoMonthsAgo' 
                        ORDER BY Registed_Date DESC";
              $RequisitionRows = find_by_sql($query);

              // Agrupar por Sample_ID y luego por Sample_Number
              $paquetes = [];
              foreach ($RequisitionRows as $row) {
                $sampleId = $row['Sample_ID'];
                $sampleNumber = $row['Sample_Number'];
                $tests = array_map('trim', explode(',', $row['Test_Type']));

                foreach ($tests as $t) {
                  if (!empty($t)) {
                    $paquetes[$sampleId][$sampleNumber][] = $t;
                  }
                }

                // Guardar info general (puedes tomar la última o la primera)
                $paquetesInfo[$sampleId] = [
                  'Comment'       => $row['Comment'],
                  'Sample_By'     => $row['Sample_By'],
                  'Sample_Date'   => $row['Sample_Date'],
                  'Registed_Date' => $row['Registed_Date']
                ];

                // Guardar comentario por muestra
                $muestras[$sampleId][$sampleNumber]['Comment'] = $row['Comment'];
              }

              // Calcular entregas desde test_delivery
              $sample_ids = array_unique(array_column($RequisitionRows, 'Sample_ID'));
              $sample_numbers = array_unique(array_column($RequisitionRows, 'Sample_Number'));
              $entregas = [];

              if (!empty($sample_ids) && !empty($sample_numbers)) {
                $query = "SELECT Sample_ID, Sample_Number, Test_Type 
                          FROM test_delivery 
                          WHERE Sample_ID IN ('" . implode("','", $sample_ids) . "') 
                            AND Sample_Number IN ('" . implode("','", $sample_numbers) . "')";
                $result = $db->query($query);

                if ($result) {
                  while ($row = $result->fetch_assoc()) {
                    $entregas[$row['Sample_ID']][$row['Sample_Number']][] = $row['Test_Type'];
                  }
                }
              }
              ?>

              <!-- Tabla -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Muestra</th>
                    <th scope="col">Números de muestra</th>
                    <th scope="col">Solicitados</th>
                    <th scope="col">Entregados</th>
                    <th scope="col">Progreso</th>
                    <th scope="col">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($paquetes as $sampleId => $numbers): ?>
                    <?php
                    $count_solicitados = 0;
                    $count_entregados = 0;

                    foreach ($numbers as $num => $tests) {
                      $count_solicitados += count($tests);
                      foreach ($tests as $t) {
                        if (isset($entregas[$sampleId][$num]) && in_array($t, $entregas[$sampleId][$num])) {
                          $count_entregados++;
                        }
                      }
                    }

                    $porce_entregados = $count_solicitados > 0
                      ? round(($count_entregados / $count_solicitados) * 100)
                      : 0;
                    ?>
                    <tr>
                      <th scope="row"><?php echo count_id(); ?></th>
                      <td><?php echo $sampleId; ?></td>
                      <td>
                        <?php foreach (array_keys($numbers) as $num): ?>
                          <span class="badge bg-secondary me-1"><?php echo $num; ?></span>
                        <?php endforeach; ?>
                      </td>
                      <td><span class="badge bg-primary"><?php echo $count_solicitados; ?></span></td>
                      <td><span class="badge bg-success"><?php echo $count_entregados; ?></span></td>
                      <td>
                        <div class="progress">
                          <div class="progress-bar" style="width: <?php echo $porce_entregados; ?>%">
                            <?php echo $porce_entregados; ?>%
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <!-- Ver -->
                          <button type="button" class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#requisitionview<?php echo $sampleId; ?>">
                            <i class="bi bi-eye"></i>
                          </button>
                          <!-- Editar -->
                          <a href="requisition-form-edit.php?sample_id=<?php echo urlencode($sampleId); ?>"
                            class="btn btn-warning">
                            <i class="bi bi-pen"></i>
                          </a>
                          <!-- Eliminar -->
                          <button type="button" class="btn btn-danger"
                            onclick="modaldelete('<?php echo $sampleId; ?>')">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal" id="requisitionview<?php echo $sampleId; ?>" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Detalle del paquete: <?php echo $sampleId; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="container">

                              <!-- Ensayos por número de muestra -->
                              <div class="card mb-3">
                                <div class="card-body">
                                  <h5 class="card-title">Muestras y ensayos</h5>
                                  <?php foreach ($numbers as $num => $tests): ?>
                                    <div class="mb-2">
                                      <strong class="small"><?php echo htmlspecialchars($num); ?>:</strong>
                                      <?php foreach ($tests as $test):
                                        $hecho = isset($entregas[$sampleId][$num]) && in_array($test, $entregas[$sampleId][$num]);
                                      ?>
                                        <code style="display:inline-block; margin-right:5px; padding:2px 4px; background:#f0f0f0; border-radius:3px;">
                                          <?php echo htmlspecialchars($test) . ' ' . ($hecho ? "✅" : "❌"); ?>
                                        </code>
                                      <?php endforeach; ?>
                                    </div>
                                  <?php endforeach; ?>
                                </div>
                              </div>



                              <!-- Comentario -->
                              <div class="card mb-3">
                                <div class="card-body">
                                  <h5 class="card-title">Comentarios por muestra</h5>
                                  <?php foreach ($numbers as $num => $tests): ?>
                                    <?php
                                    $comment = $muestras[$sampleId][$num]['Comment'] ?? '';
                                    if (!empty($comment)):
                                    ?>
                                      <div class="mb-2">
                                        <p><code>Muestra <?php echo htmlspecialchars($num); ?>: <?php echo htmlspecialchars($comment); ?></code></p>
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
                                      <span class="badge bg-primary"><?php echo $paquetesInfo[$sampleId]['Sample_Date']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h6><code>Fecha de Registro</code></h6>
                                      <span class="badge bg-primary"><?php echo $paquetesInfo[$sampleId]['Registed_Date']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h6><code>Muestra por</code></h6>
                                      <span class="badge bg-primary"><?php echo $paquetesInfo[$sampleId]['Sample_By']; ?></span>
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
      document.getElementById("deleteForm").action = "requisition-form-view.php?sample_id=" + selectedId;
      document.getElementById("deleteForm").submit();
    }
  }
</script>

<?php include_once('../components/footer.php'); ?>