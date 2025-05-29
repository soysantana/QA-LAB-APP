<?php
$page_title = 'Agregar Producto';
require_once('../config/load.php');
page_require_level(3);

$all_categories = find_all('categories');
$all_media = find_all('media');

if (isset($_POST['add_product'])) {
  $req_fields = ['product-title', 'Marca_Modelo', 'Codigo', 'buying-price', 'categorie-id'];
  validate_fields($req_fields);

  $name = remove_junk($db->escape($_POST['product-title']));
  $marca = remove_junk($db->escape($_POST['Marca_Modelo']));
  $codigo = remove_junk($db->escape($_POST['Codigo']));
  $quantity = $db->escape($_POST['product-quantity']);
  $buy_price = $db->escape($_POST['buying-price']);
  $category = (int)$db->escape($_POST['categorie-id']);
  $media_id = (int)$db->escape($_POST['product-photo']);
  $status = remove_junk($db->escape($_POST['Status']));
  $date = make_date();

  if (empty($errors)) {
    $sql = "INSERT INTO products (name, Marca_Modelo, Codigo, quantity, buy_price, categorie_id, media_id, date, Status) ";
    $sql .= "VALUES ('{$name}', '{$marca}', '{$codigo}', '{$quantity}', '{$buy_price}', '{$category}', '{$media_id}', '{$date}', '{$status}')";

    if ($db->query($sql)) {
      $session->msg('s', "Producto agregado exitosamente.");
      redirect('../pages/product.php', false);
    } else {
      $session->msg('d', 'Lo siento, el registro falló.');
      redirect('../pages/product.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('../pages/product.php', false);
  }
}
?>

<?php include_once('../components/header.php'); ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-box"></i> Agregar Producto</h1>
  </div>
  <?php echo display_msg($msg); ?>

  <section class="section">
    <div class="card">
      <div class="card-body pt-4">
        <form method="post" action="add_product.php">
          <div class="mb-3">
            <label for="product-title" class="form-label">Nombre del producto</label>
            <input type="text" class="form-control" name="product-title" required>
          </div>
          <div class="mb-3">
            <label for="Marca_Modelo" class="form-label">Marca o Modelo</label>
            <input type="text" class="form-control" name="Marca_Modelo" required>
          </div>
          <div class="mb-3">
            <label for="Codigo" class="form-label">Código</label>
            <input type="text" class="form-control" name="Codigo" required>
          </div>
          <div class="mb-3">
            <label for="product-quantity" class="form-label">Cantidad</label>
            <input type="number" class="form-control" name="product-quantity" min="0">
          </div>
          <div class="mb-3">
            <label for="buying-price" class="form-label">Precio de compra</label>
            <input type="number" step="0.01" class="form-control" name="buying-price" required>
          </div>
          <div class="mb-3">
            <label for="categorie-id" class="form-label">Categoría</label>
            <select class="form-select" name="categorie-id" required>
              <option value="">Seleccione una categoría</option>
              <?php foreach ($all_categories as $cat): ?>
              <option value="<?php echo (int)$cat['id']; ?>">
                <?php echo remove_junk($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="product-photo" class="form-label">Imagen</label>
            <select class="form-select" name="product-photo">
              <option value="0">Sin imagen</option>
              <?php foreach ($all_media as $media): ?>
              <option value="<?php echo (int)$media['id']; ?>">
                <?php echo $media['file_name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="Status" class="form-label">Estado</label>
            <select class="form-select" name="Status" required>
              <option value="Disponible">Disponible</option>
              <option value="En reparación">En reparación</option>
              <option value="En uso">En uso</option>
            </select>
          </div>
          <div class="d-grid">
            <button type="submit" name="add_product" class="btn btn-success">Agregar producto</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
