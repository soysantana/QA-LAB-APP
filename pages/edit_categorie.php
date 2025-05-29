<?php
$page_title = 'Editar Categoría';
require_once('../config/load.php');
page_require_level(3);

// Validar ID recibido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  $session->msg("d", "ID de categoría inválido.");
  redirect('../pages/categories.php');
}

$id = (int)$_GET['id'];
$categorie = find_by_id('categories', $id);
if (!$categorie) {
  $session->msg("d", "Categoría no encontrada.");
  redirect('../pages/categories.php');
}

// Procesar formulario de edición
if (isset($_POST['update'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($db->escape($_POST['categorie-name']));

  if (empty($errors)) {
    $sql = "UPDATE categories SET name = '{$cat_name}' WHERE id = '{$id}'";
    if ($db->query($sql)) {
      $session->msg("s", "✅ Categoría actualizada exitosamente.");
    } else {
      $session->msg("d", "❌ Error al actualizar la categoría.");
    }
    redirect('../pages/categories.php');
  } else {
    $session->msg("d", $errors);
    redirect("../pages/edit_categorie.php?id={$id}");
  }
}
?>

<?php include_once('../components/header.php'); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Editar Categoría</h1>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-md-6">
        <?php echo display_msg($msg); ?>

        <div class="card">
          <div class="card-body">
            <form method="post" action="">
              <div class="form-group mb-3">
                <label for="categorie-name">Nombre de la categoría</label>
                <input type="text" class="form-control" name="categorie-name" value="<?php echo remove_junk($categorie['name']); ?>" required>
              </div>
              <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
              <a href="../pages/categories.php" class="btn btn-secondary">Cancelar</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
