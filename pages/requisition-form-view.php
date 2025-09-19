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
              $query = "SELECT id, Package_ID, Sample_ID, Sample_Number, Test_Type, Comment, Sample_By, Sample_Date, Registed_Date 
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

                // Si no hay Package_ID, usar el id único como identificador
                if (empty($packageId)) {
                  $uniqueKey = 'row_' . $row['id'];  // clave única para manejar este registro
                  $paquetes[$uniqueKey][] = $row['Test_Type'];

                  $paquetesInfo[$uniqueKey] = [
                    'Comment'       => $row['Comment'],
                    'Sample_By'     => $row['Sample_By'],
                    'Sample_Date'   => $row['Sample_Date'],
                    'Registed_Date' => $row['Registed_Date']
                  ];

                  $muestras[$uniqueKey][] = [
                    'Sample_ID'     => $row['Sample_ID'],
                    'Sample_Number' => $row['Sample_Number'],
                    'Comment'       => $row['Comment']
                  ];

                  // Guardar referencia al id real
                  $paquetesInfo[$uniqueKey]['real_id'] = $row['id'];
                } else {
                  // Caso normal (agrupado por Package_ID)
                  $tests = array_map('trim', explode(',', $row['Test_Type']));
                  foreach ($tests as $t) {
                    if (!empty($t)) {
                      $paquetes[$packageId][] = $t;
                    }
                  }

                  $paquetesInfo[$packageId] = [
                    'Comment'       => $row['Comment'],
                    'Sample_By'     => $row['Sample_By'],
                    'Sample_Date'   => $row['Sample_Date'],
                    'Registed_Date' => $row['Registed_Date']
                  ];

                  $muestras[$packageId][] = [
                    'Sample_ID'     => $row['Sample_ID'],
                    'Sample_Number' => $row['Sample_Number'],
                    'Comment'       => $row['Comment']
                  ];
                }
              }


              // Calcular entregas desde test_delivery
              $package_ids = array_unique(array_column($RequisitionRows, 'Package_ID'));
              $entregas = [];

              if (!empty($package_ids)) {
                // buscar entregas de todas las muestras vinculadas a esos paquetes
                $samplePairs = [];
                foreach ($RequisitionRows as $row) {
                  $samplePairs[] = "('" . $db->escape($row['Sample_ID']) . "', '" . $db->escape($row['Sample_Number']) . "')";
                }
                $pairsSql = implode(',', $samplePairs);

                $query = "SELECT Sample_ID, Sample_Number, Test_Type 
                FROM test_delivery 
                WHERE (Sample_ID, Sample_Number) IN ($pairsSql)";

                $result = $db->query($query);

                if ($result) {
                  while ($row = $result->fetch_assoc()) {
                    // relacionar entrega con su Package_ID original
                    foreach ($RequisitionRows as $r) {
                      if ($r['Sample_ID'] == $row['Sample_ID'] && $r['Sample_Number'] == $row['Sample_Number']) {
                        $entregas[$r['Package_ID']][] = $row['Test_Type'];
                      }
                    }
                  }
                }
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
                    $count_solicitados = count($tests);
                    $count_entregados = 0;

                    foreach ($tests as $t) {
                      if (isset($entregas[$packageId]) && in_array($t, $entregas[$packageId])) {
                        $count_entregados++;
                      }
                    }

                    $porce_entregados = $count_solicitados > 0
                      ? round(($count_entregados / $count_solicitados) * 100)
                      : 0;

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
                                        <code>
                                          Muestra <?php echo htmlspecialchars($m['Sample_Number']); ?>:
                                          <?php
                                          // Los ensayos de este paquete están en $paquetes[$packageId]
                                          foreach ($paquetes[$packageId] as $test) {
                                            $hecho = isset($entregas[$packageId]) && in_array($test, $entregas[$packageId]);
                                            echo htmlspecialchars($test) . ' ' . ($hecho ? "✅" : "❌") . ' ';
                                          }
                                          ?>
                                        </code>
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
                                    <div class="mb-2">
                                      <p>
                                        <code>Muestra <?php echo htmlspecialchars($m['Sample_Number']); ?>:
                                          <?php echo htmlspecialchars($m['Comment']); ?>
                                        </code>
                                      </p>
                                    </div>
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