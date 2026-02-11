<?php
$page_title = 'Conductividad Hidraulica - Digitación (Hoja de trabajo)';
require_once('../config/load.php');

page_require_level(2);
include_once('../components/header.php');
?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Conductividad Hidraulica - Digitar valores de hoja de trabajo</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Forms</li>
        <li class="breadcrumb-item active">Datos de Entrada</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">

      <form class="row" method="post" action="constant_head.php">

        <?php echo display_msg($msg); ?>

        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Valores de la hoja de trabajo</h5>

              <div class="row g-3">

                <div class="col-md-3">
                  <label class="form-label">Diámetro (cm)</label>
                  <input class="form-control" name="entry[D_cm]" value="11.34">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Longitud (cm)</label>
                  <input class="form-control" name="entry[L_cm]" value="15">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Tiempo (min)</label>
                  <input class="form-control" name="entry[t_min]" value="1.00">
                </div>

                <div class="col-md-3">
                  <label class="form-label">Peso del equipo (kg)</label>
                  <input class="form-control" name="entry[w_equipo]" value="3.4953">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Peso material+equipo+piezas (kg)</label>
                  <input class="form-control" name="entry[w_total]" value="6.3207">
                </div>

                <hr class="my-2">

                <!-- Lectura 1 -->
                <div class="col-md-3">
                  <label class="form-label">Altura inicial 1 (cm)</label>
                  <input class="form-control" name="entry[h1_1_cm]" value="113">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Altura final 1 (cm)</label>
                  <input class="form-control" name="entry[h2_1_cm]" value="32">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Volúmenes lectura 1 (mL) — 5 corridas</label>
                  <div class="row g-2">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <div class="col"><input class="form-control" name="entry[v1][]" placeholder="<?= $i ?>"></div>
                    <?php endfor; ?>
                  </div>
                  <div class="text-muted small mt-1">Ej: 320, 310, 315, 310, 328</div>

                   <label class="form-label">Temperaturas Lectura 1 (°C)  — 5 corridas</label>
                  <div class="row g-2">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <div class="col"><input class="form-control" name="entry[t1][]" placeholder="<?= $i ?>"></div>
                    <?php endfor; ?>
                  </div>
                  <div class="text-muted small mt-1">Ej: 23.5, 24.0, 30.0, 18.20, 15.58</div>
                </div>

                <hr class="my-2">

                <!-- Lectura 2 -->
                <div class="col-md-3">
                  <label class="form-label">Altura inicial 2 (cm)</label>
                  <input class="form-control" name="entry[h1_2_cm]" value="101">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Altura final 2 (cm)</label>
                  <input class="form-control" name="entry[h2_2_cm]" value="32">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Volúmenes lectura 2 (cm³ o mL) — 5 corridas</label>
                  <div class="row g-2">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <div class="col"><input class="form-control" name="entry[v2][]" placeholder="<?= $i ?>"></div>
                    <?php endfor; ?>
                  </div>
                  <div class="text-muted small mt-1">Ej: 280, 280, 290, 290, 290</div>
                  <label class="form-label">Temperaturas Lectura 2 (°C)  — 5 corridas</label>
                  <div class="row g-2">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <div class="col"><input class="form-control" name="entry[t2][]" placeholder="<?= $i ?>"></div>
                    <?php endfor; ?>
                  </div>
                  <div class="text-muted small mt-1">Ej: 23.5, 24.0, 30.0, 18.20, 15.58</div>
                </div>
                

                <hr class="my-2">

                <!-- Lectura 3 -->
                <div class="col-md-3">
                  <label class="form-label">Altura inicial 3 (cm)</label>
                  <input class="form-control" name="entry[h1_3_cm]" value="84">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Altura final 3 (cm)</label>
                  <input class="form-control" name="entry[h2_3_cm]" value="32">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Volúmenes lectura 3 (cm³ o mL) — 5 corridas</label>
                  <div class="row g-2">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <div class="col"><input class="form-control" name="entry[v3][]" placeholder="<?= $i ?>"></div>
                    <?php endfor; ?>
                  </div>
                  <div class="text-muted small mt-1">Ej: 221, 220, 220, 220, 220</div>
                  <label class="form-label">Temperaturas Lectura 3 (°C)  — 5 corridas</label>
                  <div class="row g-2">
                    <?php for($i=1;$i<=5;$i++): ?>
                      <div class="col"><input class="form-control" name="entry[t3][]" placeholder="<?= $i ?>"></div>
                    <?php endfor; ?>
                  </div>
                  <div class="text-muted small mt-1">Ej: 23.5, 24.0, 30.0, 18.20, 15.58</div>
                </div>

                <div class="col-12">
                  <div class="alert alert-info mb-0">
                    <b>Conversión automática al enviar:</b> cm→m, cm³→m³, min→s.  
                    Se crearán automáticamente las corridas 1–15 en el formulario Conductividad Hidraulica.
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Acciones</h5>
              <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-primary" name="send-to-d2434">
                  Enviar al Formulario de Conductividad Hidraulica
                </button>
              </div>
            </div>
          </div>
        </div>

      </form>

    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
