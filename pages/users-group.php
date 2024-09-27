<?php
$page_title = 'Usuarios & Grupos';
$user_group = 'show';
require_once('../config/load.php');
$Search = find_all_user();
page_require_level(2);
include_once('../components/header.php');
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
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Lista de Usuarios</h5>
            <table class="table table-bordered">
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
                     <button type="button" class="btn btn-primary" onclick="modaldelete('<?php echo $user['id']; ?>')"><i class="bi bi-pencil"></i></button>
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

<?php include_once('../components/footer.php'); ?>
