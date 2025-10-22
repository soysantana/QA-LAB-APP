<?php
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');
?>
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Resultados externos</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../pages/home.php">Home</a></li>
        <li class="breadcrumb-item">Documentos</li>
        <li class="breadcrumb-item active">Subir PDF externo</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-8">

        <?php echo display_msg($msg); ?>

        <div class="card">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Cargar resultado (PDF)</h5>
            <span class="text-muted small">El archivo se guardará en el servidor</span>
          </div>
          <div class="card-body">

            <form id="formUpload" action="../database/doc_upload_external_save.php" method="post" enctype="multipart/form-data" class="row g-3">

              <!-- Metadatos -->
              <div class="col-md-4">
                <label class="form-label">Sample ID</label>
                <input name="sample_id" class="form-control" placeholder="Ej: PVDJ-001">
              </div>
              <div class="col-md-4">
                <label class="form-label">Sample Number</label>
                <input name="sample_number" class="form-control" placeholder="Ej: 01">
              </div>
              <div class="col-md-4">
                <label class="form-label">Test Type</label>
                <input name="test_type" class="form-control" placeholder="MC, AL, GS...">
              </div>

              <!-- Archivo -->
              <div class="col-12">
                <label class="form-label">Archivo PDF</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-file-earmark-pdf"></i></span>
                  <input type="file" name="pdf" accept="application/pdf" required class="form-control">
                </div>
                <div class="form-text">Solo PDF. Tamaño recomendado &lt; 5&nbsp;MB.</div>
              </div>

              <!-- Acciones -->
              <div class="col-12 d-flex gap-2">
                <a href="/pages/docs_list.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Volver</a>
                <button class="btn btn-primary">
                  <i class="bi bi-upload"></i> Subir PDF
                </button>
              </div>

            </form>

          </div>
        </div>

      </div>

  </section>

</main><!-- End #main -->
<?php include_once('../components/footer.php'); ?>
