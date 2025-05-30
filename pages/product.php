<?php
$page_title = 'Lista de productos';
require_once('../config/load.php');
page_require_level(3);

$products = join_product_table(); 
?>
<?php include_once('../components/header.php'); ?>

<style>
.image-preview-container {
  position: relative;
  display: inline-block;
}

.thumb-img {
  width: 40px;
  height: 40px;
  object-fit: cover;
  cursor: pointer;
}

.img-preview {
  display: none;
  position: absolute;
  top: -10px;
  left: 50px;
  z-index: 1000;
  border: 1px solid #ddd;
  background: #fff;
  padding: 5px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

.img-preview img {
  width: 150px;
  height: auto;
  border-radius: 5px;
}

.image-preview-container:hover .img-preview {
  display: block;
}
</style>

<main id="main" class="main">

<div class="pagetitle">
  <h1>Inventarios</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Pages</li>
      <li class="breadcrumb-item active" ><a href="../components/menu_inventarios.php">Inventarios</a></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
  <div class="pagetitle">
    <h1><i class="bi bi-box-seam"></i> Inventario de Equipos</h1>
  </div>

  <?php echo display_msg($msg); ?>

  <section class="section">
    <div class="card shadow-sm">
      <div class="card-body pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">Listado de Artículos</h5>
          <div class="d-flex">
            <a href="add_product.php" class="btn btn-primary me-2">
              <i class="bi bi-plus-circle"></i> Agregar Artículo
            </a>
            <a href="../pages/sumary/export_inventario_equipos.php" class="btn btn-success">
              <i class="bi bi-file-earmark-excel"></i> Exportar Inventario
            </a>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Imagen</th>
                <th>Artículo</th>
                <th>Marca/Modelo</th>
                <th>Código</th>
                <th>Status</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Compra</th>
                <th>Agregado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $index => $product): ?>
              <tr>
                <td><?= $index + 1; ?></td>
                <td style="position: relative;">
                  <div class="image-preview-container">
                    <?php if ($product['media_id'] === '0' || empty($product['image'])): ?>
                      <img src="../uploads/products/no_image.jpg" class="rounded-circle thumb-img" alt="No Image">
                      <div class="img-preview">
                        <img src="../uploads/products/no_image.jpg" alt="No Image">
                      </div>
                    <?php else: ?>
                      <img src="../uploads/products/<?= htmlspecialchars($product['image']); ?>" class="rounded-circle thumb-img" alt="Imagen">
                      <div class="img-preview">
                        <img src="../uploads/products/<?= htmlspecialchars($product['image']); ?>" alt="Preview">
                      </div>
                    <?php endif; ?>
                  </div>
                </td>
                <td><?= remove_junk($product['name']); ?></td>
                <td><?= remove_junk($product['Marca_Modelo']); ?></td>
                <td><?= remove_junk($product['Codigo']); ?></td>
                <td><span class="badge bg-<?= $product['Status'] === 'Disponible' ? 'success' : 'secondary'; ?>">
                  <?= remove_junk($product['Status']); ?>
                </span></td>
                <td><?= remove_junk($product['categorie']); ?></td>
                <td class="text-center"><?= remove_junk($product['quantity']); ?></td>
                <td class="text-end"><?= number_format($product['buy_price'], 2); ?></td>
                <td class="text-center"><?= read_date($product['date']); ?></td>
                <td class="text-center">
                  <a href="edit_product.php?id=<?= (int)$product['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <a href="delete_product.php?id=<?= (int)$product['id']; ?>" class="btn btn-sm btn-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
