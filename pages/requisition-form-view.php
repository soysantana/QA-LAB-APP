<?php
$page_title = 'Muestras Registradas';
$requisition_form = 'show';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete-requisition'])) {
    include('../database/requisition-form.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
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
              $threeMonthsAgo = date('Y-m-d', strtotime('-2 months'));

              $query = "SELECT id, Sample_ID, Sample_Number, Test_Type, Comment, Sample_By, Sample_Date, Registed_Date 
              FROM lab_test_requisition_form WHERE Registed_Date >= '$threeMonthsAgo' ORDER BY Registed_Date DESC";
              $Requisition = find_by_sql($query);


              // Obtener todos los resultados de entregas en una sola consulta
              $sample_ids = array_unique(array_column($Requisition, 'Sample_ID'));
              $sample_numbers = array_unique(array_column($Requisition, 'Sample_Number'));

              if (!empty($sample_ids) && !empty($sample_numbers)) {
                $query = "SELECT Sample_ID, Sample_Number, Test_Type FROM test_delivery WHERE Sample_ID IN ('" . implode("','", $sample_ids) . "') AND Sample_Number IN ('" . implode("','", $sample_numbers) . "')";
                $result = $db->query($query);
              }

              // Crear un arreglo con las entregas
              $entregas = [];
              while ($row = $result->fetch_assoc()) {
                $entregas[$row['Sample_ID']][$row['Sample_Number']][] = $row['Test_Type'];
              }
              ?>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Muestra</th>
                    <th scope="col">Numero de muestra</th>
                    <th scope="col">Solicitados</th>
                    <th scope="col">Entregados</th>
                    <th scope="col">Progreso de Ensayos</th>
                    <th scope="col">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($Requisition as $Requisition): ?>

                    <?php
                    $count_solicitados = 0;
                    $count_entregados = 0;

                    // Alias para simplificar acceso a las entregas
                    $entregados = $entregas[$Requisition['Sample_ID']][$Requisition['Sample_Number']] ?? [];

                    $testType = $Requisition["Test_Type"] ?? '';  // Ejemplo: "MC,HY,SND"
                    $count_solicitados = 0;
                    $count_entregados = 0;

                    if ($testType !== '') {
                      // Convertimos el string en array, eliminando espacios por si acaso
                      $testTypesArray = array_map('trim', explode(',', $testType));

                      $count_solicitados = count($testTypesArray);

                      foreach ($testTypesArray as $type) {
                        if (in_array($type, $entregados)) {
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
                      <td><?php echo $Requisition['Sample_ID']; ?></td>
                      <td><?php echo $Requisition['Sample_Number']; ?></td>
                      <td><span class="badge bg-primary rounded-pill me-2"><?php echo $count_solicitados; ?></span></td>
                      <td><span class="badge bg-success rounded-pill me-2"><?php echo $count_entregados; ?></span></td>
                      <td>
                        <div class="progress" role="progressbar" aria-valuenow="<?php echo $porce_entregados; ?>" aria-valuemin="0" aria-valuemax="100">
                          <div class="progress-bar" style="width: <?php echo $porce_entregados; ?>%"><?php echo $porce_entregados; ?>%</div>
                        </div>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#requisitionview<?php echo $Requisition['id']; ?>"><i class="bi bi-eye"></i></button>
                          <a href="requisition-form-edit.php?id=<?php echo $Requisition['id']; ?>" class="btn btn-warning"><i class="bi bi-pen"></i></a>
                          <button type="button" class="btn btn-danger" onclick="modaldelete('<?php echo $Requisition['id']; ?>')"><i class="bi bi-trash"></i></button>
                        </div>
                      </td>
                    </tr>

                    <div class="modal" id="requisitionview<?php echo $Requisition['id']; ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Detalle del ensayo </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="container">
                              <div class="card">
                                <div class="card-body">
                                  <h5 class="card-title">Muestra</h5>
                                  <h5><?php echo $Requisition['Sample_ID'] . "-" . $Requisition['Sample_Number']; ?></h5>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-body">
                                  <h5 class="card-title">Ensayos solicitados</h5>
                                  <ul class="list-group">
                                    <?php
                                    $testType = $Requisition['Test_Type'] ?? '';

                                    if (!empty($testType)) {
                                      // Convertimos el string a array, eliminando espacios
                                      $testTypesArray = array_map('trim', explode(',', $testType));

                                      foreach ($testTypesArray as $testTypeValue) {
                                        if (!empty($testTypeValue)) {
                                          echo '<li class="list-group-item">' . htmlspecialchars($testTypeValue) . '</li>';
                                        }
                                      }
                                    }
                                    ?>

                                  </ul>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-body">
                                  <h5 class="card-title">Comentario</h5>
                                  <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h5><code><?php echo $Requisition['Comment']; ?></code></h5>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-body">
                                  <h5 class="card-title">Otros datos</h5>
                                  <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h5><code>Fecha de la muestra</code></h5>
                                      <span class="badge bg-primary rounded-pill"><?php echo $Requisition['Sample_Date']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h5><code>Fecha de Registro</code></h5>
                                      <span class="badge bg-primary rounded-pill"><?php echo $Requisition['Registed_Date']; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      <h5><code>Muestra por</code></h5>
                                      <span class="badge bg-primary rounded-pill"><?php echo $Requisition['Sample_By']; ?></span>
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

              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </form>

      <!-- Modal -->
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
  var selectedId; // Variable para almacenar el ID

  function modaldelete(id) {
    // Almacena el ID
    selectedId = id;

    // Utiliza el método modal() de Bootstrap para mostrar el modal
    $('#ModalDelete').modal('show');
  }

  function Delete() {
    // Verifica si se ha guardado un ID
    if (selectedId !== undefined) {
      // Concatena el ID al final de la URL en el atributo 'action' del formulario
      document.getElementById("deleteForm").action = "requisition-form-view.php?id=" + selectedId;

      // Envía el formulario
      document.getElementById("deleteForm").submit();
    } else {
      console.log('No se ha seleccionado ningún ID para eliminar.');
    }
  }
</script>

<?php include_once('../components/footer.php');  ?>