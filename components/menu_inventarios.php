<?php
// components/menu_inventarios.php
$page_title   = 'Inventario del laboratorio';
$menu_active  = 'inventario';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>
<main id="main" class="main">

  <div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
      <h1>Inventario del laboratorio</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/pages/home.php">Inicio</a></li>
          <li class="breadcrumb-item active">Inventario</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row g-3">

      <!-- =========================
           Inventario general
           ========================= -->
      <div class="col-12">
        <h6 class="text-muted text-uppercase small mb-1">Inventario general</h6>
      </div>

      <!-- Inventario de equipos / insumos -->
      <div class="col-12 col-md-6 col-lg-4">
        <!-- Ajusta la ruta de href a tu archivo real, por ejemplo: /pages/inventario_equipos.php -->
        <a href="/components/menu_inventario_equipos.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-box-seam me-2"></i>
                <h5 class="card-title mb-0">Inventario de equipos e insumos</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Control de equipos, consumibles y material de laboratorio, con estado y ubicación.
              </p>
            </div>
          </div>
        </a>
      </div>

      <!-- =========================
           Muestras especiales
           ========================= -->
      <div class="col-12 mt-3">
        <h6 class="text-muted text-uppercase small mb-1">Muestras especiales</h6>
      </div>

      <!-- Inventario de muestras inalteradas -->
      <div class="col-12 col-md-6 col-lg-4">
        <!-- Ajusta a la ruta real que uses para el inventario de muestras inalteradas -->
        <a href="/pages/inventario_inalterada.php" class="text-decoration-none">
          <div class="card h-100 shadow-sm border-0 hover-elevate">
            <div class="card-body">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-geo-alt me-2"></i>
                <h5 class="card-title mb-0">Inventario de muestras inalteradas</h5>
              </div>
              <p class="card-text small text-muted mb-0">
                Control de muestras inalteradas: código, profundidad, longitud, peso, ubicación y estado.
              </p>
            </div>
          </div>
        </a>
      </div>


    </div><!-- End row -->
  </section>

</main><!-- End #main -->

<?php include_once('../components/footer.php'); ?>

<style>
  /* Toque visual para las tarjetas del hub de inventario */
  .hover-elevate {
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .hover-elevate:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08);
  }
  .card-title {
    font-size: 0.98rem;
  }
  .card-text {
    font-size: 0.8rem;
  }
</style>
