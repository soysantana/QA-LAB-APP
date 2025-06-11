<?php
$page_title = 'Muestra en preparación';
$tracking_show = 'show';
$class_tracking = ' ';
$preparation = 'active';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['test-preparation'])) {
    include('../database/sample-tracking/preparation/save.php');
  } elseif (isset($_POST['delete-preparation'])) {
    include('../database/sample-tracking/preparation/delete.php');
  } elseif (isset($_POST['send-realization'])) {
    include('../database/sample-tracking/realization/save.php');
  } elseif (isset($_POST['update_technician'])) {
    include('../database/sample-tracking/preparation/update.php');
  } elseif (isset($_POST['SendMultipleRealization'])) {
    include('../database/sample-tracking/realization/send-multiple.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Muestra en preparación</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Formularios</li>
        <li class="breadcrumb-item active">Muestra en preparación</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4"><?php echo display_msg($msg); ?></div>

  <section class="section">
    <div class="row">

      <div class="col-lg-5">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">AÑADIR MUESTRA A LA PREPARACIÓN</h5>

            <!-- Multi Columns Form -->
            <form class="row g-3" method="post" action="test-preparation.php">
              <div class="col-md-12">
                <label for="Sname" class="form-label">Nombre de la muestra</label>
                <input type="text" class="form-control" name="Sname" id="Sname" autocomplete="off">
              </div>
              <div class="col-md-12">
                <label for="Snumber" class="form-label">Numero de muestra</label>
                <input type="text" class="form-control" name="Snumber" id="Snumber" autocomplete="off">
              </div>
              <div class="col-md-12">
                <label for="Ttype" class="form-label">Tipo de prueba</label>
                <select id="Ttype" class="form-select" size="20" name="Ttype[]" multiple>
                  <option value="MC">MC</option>
                  <option value="AL">AL</option>
                  <option value="GS">GS</option>
                  <option value="SP">SP</option>
                  <option value="SG">SG</option>
                  <option value="UCS">UCS</option>
                  <option value="BTS">BTS</option>
                  <option value="PLT">PLT</option>
                  <option value="HY">HY</option>
                  <option value="DHY">DHY</option>
                  <option value="AR">AR</option>
                  <option value="SCT">SCT</option>
                  <option value="LAA">LAA</option>
                  <option value="SND">SND</option>
                  <option value="Consolidation">Consolidación</option>
                  <option value="PH">PH</option>
                  <option value="Permeability">Permeabilidad</option>
                  <option value="SHAPE">Formas de Partículas</option>
                  <option value="DENSITY">Densidad</option>
                  <option value="CRUMBS">CRUMBS</option>
                </select>
                <small class="text-muted">Mantén presionada la tecla Ctrl (o Cmd en Mac) para seleccionar múltiples opciones.</small>
              </div>
              <div class="col-md-12">
                <label for="Technician" class="form-label">Técnico/a</label>
                <input type="text" class="form-control" name="Technician" id="Technician" autocomplete="off">
              </div>
              <div>
                <button type="submit" class="btn btn-success" name="test-preparation"><i class="bi bi-save me-1"></i> Enviar a Preparación</button>
              </div>
            </form><!-- End Multi Columns Form -->

          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">LISTA DE MUESTRAS EN PREPARACIÓN</h5>

            <?php $week = date('Y-m-d', strtotime('-10000000000 days')); ?>
            <?php $realization = "(SELECT 1 FROM test_realization WHERE sample_name = p.sample_name AND sample_number = p.sample_number AND test_type = p.test_type)"; ?>
            <?php $Seach = find_by_sql("SELECT id, Sample_Name, Sample_Number, Test_Type, Technician, Start_Date FROM test_preparation p WHERE Start_Date >= '{$week}' AND NOT EXISTS $realization ORDER BY Register_Date DESC"); ?>

            <!-- Preparation -->
            <form id="multiple-send-form" method="post" action="test-preparation.php">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Select</th>
                    <th scope="col">Nombre de la muestra</th>
                    <th scope="col">Numero de muestra</th>
                    <th scope="col">Tipo de prueba</th>
                    <th scope="col">Técnico/a</th>
                    <th scope="col">Fecha de inicio</th>
                    <th scope="col">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($Seach as $Seach): ?>
                    <tr>
                      <td><?php echo count_id(); ?></td>
                      <td>
                        <input type="checkbox" name="selected_samples[]" value="<?php echo $Seach['id']; ?>">
                      </td>
                      <td><?php echo $Seach['Sample_Name']; ?></td>
                      <td><?php echo $Seach['Sample_Number']; ?></td>
                      <td><?php echo $Seach['Test_Type']; ?></td>
                      <td><?php echo $Seach['Technician']; ?></td>
                      <td><?php echo $Seach['Start_Date']; ?></td>
                      <td>
                        <div class="btn-group" role="group" aria-label="Basic example">
                          <a class="btn btn-success open-modal-btn" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true" data-sample-name="<?php echo $Seach['Sample_Name']; ?>" data-sample-number="<?php echo $Seach['Sample_Number']; ?>" data-test-type="<?php echo $Seach['Test_Type']; ?>" data-technician="<?php echo $Seach['Technician']; ?>" data-start-date="<?php echo $Seach['Start_Date']; ?>"><i class="bi bi-send me-1"></i></a>
                          <button type="button" class="btn btn-primary" onclick="modalEdit('<?php echo $Seach['id']; ?>', '<?php echo $Seach['Technician']; ?>')"><i class="bi bi-pencil"></i></button>
                          <button type="button" class="btn btn-danger" onclick="modaldelete('<?php echo $Seach['id']; ?>')"><i class="bi bi-trash"></i></button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <!-- End Table -->

              <button type="submit" class="btn btn-primary mt-2" name="SendMultipleRealization">
                <i class="bi bi-send-check me-1"></i> Enviar seleccionadas a realización
              </button>
            </form>
            <!-- End Preparation -->


          </div>
        </div>
      </div>

      <!-- Modal Update -->
      <div class="modal fade" id="disablebackdrop" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog">
          <div class="modal-content">

            <form method="post" action="test-preparation.php"><!-- Multi Columns Form -->

              <div class="modal-header">
                <h5 class="modal-title">¡Ey! Envia la muestra a realización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-12">
                    <label for="Sname" class="form-label">Nombre de la muestra</label>
                    <input type="text" class="form-control" name="Sname" id="Sname" readonly>
                  </div>
                  <div class="col-md-12">
                    <label for="Snumber" class="form-label">Numero de muestra</label>
                    <input type="text" class="form-control" name="Snumber" id="Snumber" readonly>
                  </div>
                  <div class="col-md-12">
                    <label for="Ttype" class="form-label">Tipo de prueba</label>
                    <select id="Ttype" class="form-select" name="Ttype">
                      <option selected disabled>Elegir...</option>
                      <option value="MC">MC</option>
                      <option value="AL">AL</option>
                      <option value="GS">GS</option>
                      <option value="SP">SP</option>
                      <option value="SG">SG</option>
                      <option value="UCS">UCS</option>
                      <option value="BTS">BTS</option>
                      <option value="PLT">PLT</option>
                      <option value="HY">HY</option>
                      <option value="DHY">DHY</option>
                      <option value="AR">AR</option>
                      <option value="SCT">SCT</option>
                      <option value="LAA">LAA</option>
                      <option value="SND">SND</option>
                      <option value="Consolidation">Consolidacion</option>
                      <option value="PH">PH</option>
                      <option value="Permeability">Permeabilidad</option>
                      <option value="SHAPE">Formas de Particulas</option>
                      <option value="DENSITY">Densidad</option>
                      <option value="CRUMBS">CRUMBS</option>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label for="Technician" class="form-label">Técnico/a</label>
                    <input type="text" class="form-control" name="Technician" id="Technician">
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" name="send-realization"><i class="bi bi-save me-1"></i> Enviar a realización</button>
              </div>

            </form><!-- End Multi Columns Form -->

          </div>

        </div>
      </div>
      <!-- End Modal Update -->

      <!-- Modal -->
      <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Editar Técnico</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" action="test-preparation.php" method="post">
              <div class="modal-body">

                <div class="mb-3">
                  <label for="technicianName" class="form-label">Nombre del Técnico</label>
                  <input type="text" class="form-control" id="technicianName" name="technicianName" required>
                </div>
                <input type="hidden" id="technicianId" name="technicianId">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" name="update_technician">Guardar Cambios</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal Delete -->
      <div class="modal fade" id="ModalDelete" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
              <h5>¿Está seguro?</h5>
            </div>
            <div class="modal-body">
              <form id="deleteForm" method="post">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="submit" class="btn btn-outline-danger" name="delete-preparation" onclick="Delete()">Sí</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal Delete -->

    </div>
  </section>

</main><!-- End #main -->

<script>
  // JavaScript para manejar la apertura del modal y la actualización de los datos
  document.addEventListener('DOMContentLoaded', function() {
    var modalTriggerButtons = document.querySelectorAll('.open-modal-btn');
    var modalForm = document.querySelector('#disablebackdrop form');

    modalTriggerButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        // Obtén los datos de la fila correspondiente
        var sampleName = button.getAttribute('data-sample-name');
        var sampleNumber = button.getAttribute('data-sample-number');
        var testType = button.getAttribute('data-test-type');
        var technician = button.getAttribute('data-technician');
        var startDate = button.getAttribute('data-start-date');

        // Actualiza los valores en el formulario dentro del modal
        if (modalForm) {
          modalForm.querySelector('#Sname').value = sampleName;
          modalForm.querySelector('#Snumber').value = sampleNumber;
          modalForm.querySelector('#Ttype').value = testType;
          modalForm.querySelector('#Technician').value = technician;
          // Puedes seguir actualizando otros campos según sea necesario
        }
      });
    });
  });

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
      document.getElementById("deleteForm").action = "test-preparation.php?id=" + selectedId;

      // Envía el formulario
      document.getElementById("deleteForm").submit();
    } else {
      console.log('No se ha seleccionado ningún ID para eliminar.');
    }
  }

  function modalEdit(id, name) {
    // Rellenar el input con el nombre del técnico y el ID oculto
    document.getElementById('technicianId').value = id;
    document.getElementById('technicianName').value = name;

    // Mostrar el modal
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
  }
</script>

<?php include_once('../components/footer.php');  ?>