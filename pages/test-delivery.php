<?php
$page_title = 'Muestra en entrega';
$tracking_show = 'show';
$class_tracking = ' ';
$delivery = 'active';
require_once('../config/load.php');
?>

<?php
// Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete-delivery'])) {
    include('../database/sample-tracking/delivery/delete.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Muestra en entrega</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Formularios</li>
        <li class="breadcrumb-item active">Muestra en entrega</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="col-md-4"><?php echo display_msg($msg); ?></div>

  <section class="section">
    <div class="row">

      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">LISTA DE MUESTRAS EN ENTREGA</h5>

            <?php $week = date('Y-m-d', strtotime('-14 days')); ?>
            <?php $review = "(SELECT 1 FROM test_review WHERE sample_name = p.sample_name AND sample_number = p.sample_number AND test_type = p.test_type)"; ?>
            <?php $Seach = find_by_sql("SELECT * FROM test_delivery p WHERE Start_Date >= '{$week}' AND NOT EXISTS $review ORDER BY Register_Date DESC"); ?>
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


    </div>
  </section>

</main><!-- End #main -->

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
          <button type="submit" class="btn btn-outline-danger" name="delete-delivery" onclick="Delete()">Sí</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Delete -->

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
      document.getElementById("deleteForm").action = "test-delivery.php?id=" + selectedId;

      // Envía el formulario
      document.getElementById("deleteForm").submit();
    } else {
      console.log('No se ha seleccionado ningún ID para eliminar.');
    }
  }
</script>

<?php include_once('../components/footer.php');  ?>