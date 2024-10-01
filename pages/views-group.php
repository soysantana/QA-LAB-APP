<?php
$page_title = 'Usuarios & Grupos';
$user_group = 'show';
require_once('../config/load.php');
$Search = find_all('user_groups');
page_require_level(1);
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
            <h5 class="card-title">Lista de Grupos</h5>
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
                     <button type="button" class="btn btn-primary" onclick="modaldelete('<?php echo $group['id']; ?>')"><i class="bi bi-pencil"></i></button>
                     <button type="button" class="btn btn-danger" onclick="modaldelete('<?php echo $group['id']; ?>')"><i class="bi bi-trash"></i></button>
                     <button type="button" class="btn btn-warning"><i class="bi bi-person-gear"></i></button>
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
