<?php
require_once('../config/load.php');
require_once('../libs/Media.php'); // Ajusta si está en otra ruta
page_require_level(3);

$media_files = find_all('media');

if (isset($_POST['submit'])) {
  $photo = new Media();
  $photo->upload($_FILES['file_upload']);
  if ($photo->process_media()) {
    $session->msg('s', 'Imagen subida al servidor.');
  } else {
    $session->msg('d', join('<br>', $photo->errors));
  }
  redirect('media.php');
}
?>

<?php include_once('../components/header.php'); ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-images"></i> Galería de Imágenes</h1>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-md-12">
        <?php echo display_msg($msg); ?>
      </div>

      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-upload"></i> Subir nueva imagen</strong>
            <form class="d-flex" action="media.php" method="POST" enctype="multipart/form-data">
              <input type="file" name="file_upload" class="form-control me-2" required>
              <button type="submit" name="submit" class="btn btn-primary">Subir</button>
            </form>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Vista</th>
                    <th>Nombre de archivo</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($media_files as $media_file): ?>
                    <tr>
                      <td class="text-center"><?php echo count_id(); ?></td>
                      <td class="text-center">
                        <img src="../uploads/products/<?php echo $media_file['file_name']; ?>" class="img-thumbnail" width="80">
                      </td>
                      <td class="text-center"><?php echo htmlspecialchars($media_file['file_name']); ?></td>
                      <td class="text-center"><?php echo htmlspecialchars($media_file['file_type']); ?></td>
                      <td class="text-center">
                        <a href="../pages/delete_media.php?id=<?php echo (int)$media_file['id']; ?>" class="btn btn-danger btn-sm" title="Eliminar">
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
      </div>
    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>