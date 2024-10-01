<?php
$page_title = 'Usuarios & Grupos';
$user_group = 'show';
require_once('../config/load.php');
$Search = find_all_user();
page_require_level(1);
include_once('../components/header.php');
?>

<?php 
  // Manejo de los formularios
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        include('../database/user/update-user.php');
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
            <h5 class="card-title">Lista de Usuarios</h5>
            <table class="table datatable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Usuario</th>
                  <th>Rol de usuario</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($Search as $user): ?>
                  <tr>
                    <td><?php echo count_id() ?></td>
                    <td><?php echo remove_junk($user['name'])?></td>
                    <td><?php echo remove_junk($user['username'])?></td>
                    <td><?php echo remove_junk($user['group_name'])?></td>
                    <td>
                    <?php if($user['status'] === '1'): ?>
                     <span class="badge bg-success rounded-pill"><?php echo "Activo"; ?></span>
                    <?php else: ?>
                     <span class="badge bg-danger rounded-pill"><?php echo "Inactivo"; ?></span>
                    <?php endif;?>
                    </td>
                    <td>
                     <div class="btn-group" role="group">
                     <button type="button" class="btn btn-primary" onclick="modaledit('<?php echo $user['id']; ?>')"><i class="bi bi-pencil"></i></button>
                     <button type="button" class="btn btn-danger" onclick="modaldelete('<?php echo $user['id']; ?>')"><i class="bi bi-trash"></i></button>
                     <a type="button" class="btn btn-warning" href="views-group.php"><i class="bi bi-person-gear"></i></a>
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

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm" action="users-group.php" method="post">
          <input type="hidden" id="id" name="id">
          <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="mb-3">
            <label for="role" class="form-label">Rol de Usuario</label>
            <select class="form-select" id="role" name="role" required>
              <option value="1">Supervisor</option>
              <option value="2">Control Document</option>
              <option value="3">Tecnico</option>
              <option value="4">Visitor</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Estado</label>
            <select class="form-select" id="status" name="status" required>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary" name="update_user">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Modal -->

<script>
function modaledit(id) {
    const users = <?php echo json_encode($Search); ?>;
    const user = users.find(u => u.id == id);
    if (user) {
        document.getElementById('id').value = user.id;
        document.getElementById('name').value = user.name;
        document.getElementById('username').value = user.username;
        document.getElementById('role').value = user.user_level;
        document.getElementById('status').value = user.status;

        var myModal = new bootstrap.Modal(document.getElementById('editUserModal'), {
            keyboard: false
        });
        myModal.show();
    }
}
</script>


<?php include_once('../components/footer.php'); ?>
