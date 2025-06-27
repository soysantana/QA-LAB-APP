<?php
$page_title = 'Reporte Semanal del Laboratorio';
require_once('../config/load.php');
page_require_level(3);
include_once('../components/header.php');
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Reporte Semanal</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Reportes</li>
        <li class="breadcrumb-item active">Reporte Semanal</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section dashboard">
    <div class="card">
      <div class="card-body pt-3">
        <h5 class="card-title">Resumen Semanal del Laboratorio</h5>

        <div class="row mb-3">
          <div class="col-md-3">
            <label for="fecha" class="form-label">Seleccionar semana:</label>
            <input type="date" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="cargarDatos()">Cargar</button>
          </div>
        </div>

        <div class="row" id="tarjetas">
          <div class="col-md-4">
            <div class="card info-card">
              <div class="card-body">
                <h6 class="card-title">Muestras registradas</h6>
                <h3 id="total_muestras">0</h3>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card info-card">
              <div class="card-body">
                <h6 class="card-title">Clientes activos</h6>
                <h3 id="clientes">0</h3>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card info-card">
              <div class="card-body">
                <h6 class="card-title">Tipos de ensayo</h6>
                <h3 id="ensayos">0</h3>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div id="grafico" style="width: 100%; height: 400px;"></div>
        </div>

      </div>
    </div>
  </section>

</main><!-- End #main -->



<?php include_once('../components/footer.php'); ?>
