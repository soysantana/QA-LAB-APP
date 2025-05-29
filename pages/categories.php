<?php
$page_title = 'Menú de Inventario de Equipos';
require_once('../config/load.php');
page_require_level(3);

$all_categories = find_all('categories');

if (isset($_POST['add_cat'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($db->escape($_POST['categorie-name']));
  if (empty($errors)) {
    $sql  = "INSERT INTO categories (`name`) VALUES ('$cat_name')";
    if ($db->query($sql)) {
      $session->msg("s", "Categoría agregada exitosamente.");
    } else {
      $session->msg("d", "Lo siento, el registro falló.");
    }
  } else {
    $session->msg("d", $errors);
  }
  redirect($_SERVER['PHP_SELF'], false);
}
?>

<?php include_once('../components/header.php'); ?>
<main id="main" class="main">
<div class="row">
  <div class="col-md-12"><?php echo display_msg($msg); ?></div>
</div>

<div class="row">
  <div class="col-md-5">
    <div class="card mb-4">
      <div class="card-header">
        <strong><i class="bi bi-folder-plus"></i> Agregar categoría</strong>
      </div>
      <div class="card-body">
        <form method="post" action="">
          <div class="form-group mb-2">
            <input type="text" class="form-control" name="categorie-name" placeholder="Nombre de la categoría" required>
          </div>
          <button type="submit" name="add_cat" class="btn btn-primary">Agregar categoría</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card">
      <div class="card-header">
        <strong><i class="bi bi-list-ul"></i> Lista de categorías</strong>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Categoría</th>
              <th class="text-center" style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_categories as $cat): ?>
              <tr>
                <td class="text-center"><?php echo count_id(); ?></td>
                <td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_categorie.php?id=<?php echo (int)$cat['id']; ?>" class="btn btn-warning btn-sm" title="Editar">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="delete_categorie.php?id=<?php echo (int)$cat['id']; ?>" class="btn btn-danger btn-sm" title="Eliminar">
                      <i class="bi bi-trash"></i>
                    </a>
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

<?php include_once('../components/footer.php');  ?>