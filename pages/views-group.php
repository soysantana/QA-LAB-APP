<?php
$page_title = 'Usuarios & Grupos';
$user_group = 'show';
require_once('../config/load.php');
$Search = find_all('user_groups');
page_require_level(1);
include_once('../components/header.php');
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_group'])) {
        include('../database/user/group/update.php');
    } elseif (isset($_POST['delete_group'])) {
        include('../database/user/group/delete.php');
    } elseif (isset($_POST['add_group'])) {
        include('../database/user/group/save.php');
    }
  }
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Usuarios & Grupos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Usuarios & Grupos</li>
      </ol>
    </nav>
  </div>
  <!-- End Page Title -->

  <section class="section">
    <div class="row">

    <div class="col-md-4"><?php echo display_msg($msg); ?></div>
    
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Lista de Grupos</h5>
              <button type="button" class="btn btn-sm btn-success" onclick="modaladd()">
                Agregar Grupo
              </button>
            </div>

            <table class="table datatable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre del grupo</th>
                  <th>Nivel del grupo</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($Search as $group): ?>
                  <tr>
                    <td><?php echo count_id() ?></td>
                    <td><?php echo remove_junk($group['group_name'])?></td>
                    <td><?php echo remove_junk($group['group_level'])?></td>
                    <td>
                    <?php if($group['group_status'] === '1'): ?>
                     <span class="badge bg-success rounded-pill"><?php echo "Activo"; ?></span>
                    <?php else: ?>
                     <span class="badge bg-danger rounded-pill"><?php echo "Inactivo"; ?></span>
                    <?php endif;?>
                    </td>
                    <td>
                     <div class="btn-group" role="group">
                     <button type="button" class="btn btn-primary" onclick="modaledit('<?php echo $group['id']; ?>')"><i class="bi bi-pencil"></i></button>
                     <button type="button" class="btn btn-danger" onclick="modaldelete('<?php echo $group['id']; ?>')"><i class="bi bi-trash"></i></button>
                     </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<!-- End #main -->

<!-- Modal para editar grupo -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Editar Grupo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm" action="views-group.php" method="post">
          <input type="hidden" id="id" name="id">
          <div class="mb-3">
            <label for="GroupName" class="form-label">Nombre del grupo</label>
            <input type="text" class="form-control" id="GroupName" name="GroupName" required>
          </div>
          <div class="mb-3">
            <label for="GroupLevel" class="form-label">Nivel del grupo</label>
            <input type="number" class="form-control" id="GroupLevel" name="GroupLevel" required>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select class="form-select" id="status" name="status" required>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary" name="update_group">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Modal -->

<!-- Modal para agregar nuevo grupo -->
<div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addGroupModalLabel">Agregar Nuevo Grupo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addGroupForm" action="views-group.php" method="post">
          <div class="mb-3">
            <label for="newGroupName" class="form-label">Nombre del grupo</label>
            <input type="text" class="form-control" id="newGroupName" name="newGroupName" required>
          </div>
          <div class="mb-3">
            <label for="newGroupLevel" class="form-label">Nivel del grupo</label>
            <input type="number" class="form-control" id="newGroupLevel" name="newGroupLevel" required>
          </div>
          <div class="mb-3">
            <label for="newStatus" class="form-label">Estado</label>
            <select class="form-select" id="newStatus" name="newStatus" required>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary" name="add_group">Agregar Grupo</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Modal -->

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
          <button type="submit" class="btn btn-outline-danger" name="delete_group" onclick="Delete()">Sí</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal Delete -->

<script>
function modaledit(id) {
    const users = <?php echo json_encode($Search); ?>;
    const user = users.find(u => u.id == id);
    if (user) {
        document.getElementById('id').value = user.id;
        document.getElementById('GroupName').value = user.group_name;
        document.getElementById('GroupLevel').value = user.group_level;
        document.getElementById('status').value = user.group_status;

        var myModal = new bootstrap.Modal(document.getElementById('editUserModal'), {
            keyboard: false
        });
        myModal.show();
    }
}

function modaldelete(id) {
    currentId = id; // Asigna el ID a la variable global

    // Utiliza el método modal() de Bootstrap para mostrar el modal
    $('#ModalDelete').modal('show');
}

function Delete() {
    // Verifica si se ha guardado un ID
    if (currentId !== undefined) {
        // Concatena el ID al final de la URL en el atributo 'action' del formulario
        document.getElementById("deleteForm").action = "views-group.php?id=" + currentId;

        // Envía el formulario
        document.getElementById("deleteForm").submit();
    } else {
        console.log('No se ha seleccionado ningún ID para eliminar.');
    }
}

function modaladd() {
    var myModal = new bootstrap.Modal(document.getElementById('addGroupModal'), {
        keyboard: false
    });
    myModal.show();
}
</script>

<?php include_once('../components/footer.php'); ?>
