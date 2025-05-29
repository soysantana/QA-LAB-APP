<?php
$page_title = 'Editar Producto';
require_once('../config/load.php');
page_require_level(3);

$product_id = (int)$_GET['id'];
$product = find_by_id('products', $product_id);
$all_categories = find_all('categories');
$all_media = find_all('media');

if (!$product) {
  $session->msg("d", "ProductoArticulo no encontrado.");
  redirect('../pages/product.php');
}

if (isset($_POST['product'])) {
  $req_fields = ['product-title', 'product-categorie', 'product-quantity', 'buying-price', 'product-modelo', 'product-code', 'product-status'];
  validate_fields($req_fields);

  if (empty($errors)) {
    $name = remove_junk($db->escape($_POST['product-title']));
    $marca_modelo = remove_junk($db->escape($_POST['product-modelo']));
    $codigo = remove_junk($db->escape($_POST['product-code']));
    $quantity = remove_junk($db->escape($_POST['product-quantity']));
    $buy_price = remove_junk($db->escape($_POST['buying-price']));
    $categorie = (int)$_POST['product-categorie'];
    $media_id = (int)$_POST['product-photo'];
    $status = remove_junk($db->escape($_POST['product-status']));

    $sql = "UPDATE products SET name='{$name}', Marca_Modelo='{$marca_modelo}', Codigo='{$codigo}', quantity='{$quantity}', buy_price='{$buy_price}', categorie_id='{$categorie}', media_id='{$media_id}', Status='{$status}' WHERE id='{$product_id}'";

    if ($db->query($sql)) {
      $session->msg("s", "Articulo actualizado exitosamente.");
      redirect('../pages/product.php');
    } else {
      $session->msg("d", "Lo siento, actualización falló.");
      redirect('../pages/edit_product.php?id=' . $product_id);
    }
  } else {
    $session->msg("d", $errors);
    redirect('../pages/edit_product.php?id=' . $product_id);
  }
}

?>

<?php include_once('../components/header.php'); ?>
<main id="main" class="main">
<div class="pagetitle">
  <h1>Editar Articulo</h1>
</div>
<section class="section">
  <div class="card">
    <div class="card-body">
      <form method="post" action="edit_product.php?id=<?php echo (int)$product['id']; ?>">
        <div class="row g-3 py-3">
          <div class="col-md-6">
            <label for="product-title" class="form-label">Nombre del artículo</label>
            <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']); ?>">
          </div>
          <div class="col-md-6">
            <label for="product-modelo" class="form-label">Marca / Modelo</label>
            <input type="text" class="form-control" name="product-modelo" value="<?php echo remove_junk($product['Marca_Modelo']); ?>">
          </div>
          <div class="col-md-6">
            <label for="product-code" class="form-label">Código</label>
            <input type="text" class="form-control" name="product-code" value="<?php echo remove_junk($product['Codigo']); ?>">
          </div>
          <div class="col-md-3">
            <label for="product-quantity" class="form-label">Cantidad</label>
            <input type="text" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
          </div>
          <div class="col-md-3">
            <label for="buying-price" class="form-label">Precio Compra</label>
            <input type="text" class="form-control" name="buying-price" value="<?php echo remove_junk($product['buy_price']); ?>">
          </div>
          <div class="col-md-4">
            <label for="product-categorie" class="form-label">Categoría</label>
            <select class="form-control" name="product-categorie">
              <option value="">Selecciona una categoría</option>
              <?php foreach ($all_categories as $cat): ?>
                <option value="<?php echo (int)$cat['id']; ?>" <?php if ($product['categorie_id'] === $cat['id']) echo 'selected'; ?>>
                  <?php echo remove_junk($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="product-photo" class="form-label">Imagen</label>
            <select class="form-control" name="product-photo">
              <option value="0">Sin imagen</option>
              <?php foreach ($all_media as $media): ?>
                <option value="<?php echo (int)$media['id']; ?>" <?php if ($product['media_id'] === $media['id']) echo 'selected'; ?>>
                  <?php echo $media['file_name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label for="product-status" class="form-label">Estado</label>
            <select class="form-control" name="product-status">
              <option value="Disponible" <?php if ($product['Status'] === 'Disponible') echo 'selected'; ?>>Disponible</option>
              <option value="En uso" <?php if ($product['Status'] === 'En uso') echo 'selected'; ?>>En uso</option>
              <option value="En reparación" <?php if ($product['Status'] === 'En reparación') echo 'selected'; ?>>En reparación</option>
              <option value="Descartado" <?php if ($product['Status'] === 'Descartado') echo 'selected'; ?>>Descartado</option>
            </select>
          </div>
        </div>
        <div class="text-end">
          <button type="submit" name="product" class="btn btn-success">Actualizar articulo</button>
        </div>
      </form>
    </div>
  </div>
</section>
</main>
<?php include_once('../components/footer.php'); ?>