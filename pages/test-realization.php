<?php
$page_title = 'Muestra en realización';
$tracking_show = 'show';
$class_tracking = ' ';
$realization = 'active';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete-realization'])) {
    include('../database/sample-tracking/realization/delete.php');
  } elseif (isset($_POST['send-delivery'])) {
    include('../database/sample-tracking/delivery/save.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Muestra en realización</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Formularios</li>
        <li class="breadcrumb-item active">Muestra en realización</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4"><?php echo display_msg($msg); ?></div>

  <section class="section">
    <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">LISTA DE MUESTRAS EN REALIZACIÓN</h5>

            <?php $week = date('Y-m-d', strtotime('-14 days')); ?>
            <?php $delivery = "(SELECT 1 FROM test_delivery WHERE sample_name = p.sample_name AND sample_number = p.sample_number AND test_type = p.test_type)"; ?>
            <?php $Seach = find_by_sql("SELECT * FROM test_realization p WHERE Start_Date >= '{$week}' AND NOT EXISTS $delivery ORDER BY Register_Date DESC"); ?>
            <!-- Bordered Table -->
            <table class="table datatable">
              <thead>
                <tr>
                  <th scope="col">#</th>
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
                    <td><?php echo $Seach['Sample_Name']; ?></td>
                    <td><?php echo $Seach['Sample_Number']; ?></td>
                    <td><?php echo $Seach['Test_Type']; ?></td>
                    <td><?php echo $Seach['Technician']; ?></td>
                    <td><?php echo $Seach['Start_Date']; ?></td>
                    <td>
                      <div class="btn-group" role="group" aria-label="Basic example">
                        <a class="btn btn-primary open-modal-btn" data-bs-toggle="modal" data-bs-target="#disablebackdrop" data-first-visit="true" data-sample-name="<?php echo $Seach['Sample_Name']; ?>" data-sample-number="<?php echo $Seach['Sample_Number']; ?>" data-test-type="<?php echo $Seach['Test_Type']; ?>" data-technician="<?php echo $Seach['Technician']; ?>" data-start-date="<?php echo $Seach['Start_Date']; ?>"><i class="bi bi-send me-1"></i></a>
                        <button type="button" class="btn btn-danger" onclick="modaldelete('<?php echo $Seach['id']; ?>')"><i class="bi bi-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <!-- End Bordered Table -->

          </div>
        </div>
      </div>

      <!-- Modal Update -->
      <div class="modal fade" id="disablebackdrop" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog">
          <div class="modal-content">

            <form method="post" action="test-realization.php"><!-- Multi Columns Form -->

              <div class="modal-header">
                <h5 class="modal-title">¡Ey! Envia la muestra a entrega</h5>
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
                    <label for="Technician" class="form-label">Técnico</label>
                    <input type="text" class="form-control" name="Technician" id="Technician">
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" name="send-delivery"><i class="bi bi-save me-1"></i> Enviar a entrega</button>
              </div>

            </form><!-- End Multi Columns Form -->

          </div>

        </div>
      </div>
      <!-- End Modal Update -->

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
                <button type="submit" class="btn btn-outline-danger" name="delete-realization" onclick="Delete()">Sí</button>
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
      document.getElementById("deleteForm").action = "test-realization.php?id=" + selectedId;

      // Envía el formulario
      document.getElementById("deleteForm").submit();
    } else {
      console.log('No se ha seleccionado ningún ID para eliminar.');
    }
  }
</script>

<?php include_once('../components/footer.php');  ?>