<?php
   $page_title = 'Registro de usuarios';
   require_once('../config/load.php');
   $groups = find_all('user_groups');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/style.php');  ?>
<main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="/index.php" class="logo d-flex align-items-center w-auto">
                  <img src="/assets/img/BARRICK-GOLD-logo.svg" width="200px" alt="logo-barrick">
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Crea una cuenta</h5>
                    <p class="text-center small">Ingrese sus datos personales para crear una cuenta</p>
                  </div>

                  <?php echo display_msg($msg); ?>

                  <form class="row g-3 needs-validation" method="post" action="../user/new-account2.php" novalidate>
                    <div class="col-6">
                      <label for="yourName" class="form-label">Su nombre</label>
                      <input type="text" name="full-name" class="form-control" id="yourName" required>
                      <div class="invalid-feedback">¡Por favor, escriba su nombre!</div>
                    </div>

                    <div class="col-6">
                      <label for="username" class="form-label">Nombre de usuario</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control" id="username" required>
                        <div class="invalid-feedback">Por favor, elija un nombre de usuario.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="email" class="form-label">Tu correo electrónico</label>
                      <input type="email" name="email" class="form-control" id="email">
                      <div class="invalid-feedback">Ingrese una dirección de correo electrónico válida.</div>
                    </div>

                    <div class="col-6">
                      <label for="phone" class="form-label">Su teléfono</label>
                      <input type="tel" name="phone" class="form-control" id="phone" pattern="[0-9]{10}">
                      <div class="invalid-feedback">Por favor ingrese un número de teléfono válido.</div>
                    </div>

                    <div class="col-6">
                      <label for="job" class="form-label">Tu posición</label>
                      <select id="job" class="form-select" name="job" required>
                        <option></option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Document Control">Control de documentos</option>
                        <option value="Technical">Tecnico</option>
                        <option value="Visitor">Visitante</option>
                      </select>
                      <div class="invalid-feedback">Por favor seleccione su puesto.</div>
                    </div>

                    <div class="col-12">
                      <label for="password" class="form-label">Contraseña</label>
                      <input type="password" name="password" class="form-control" id="password" required>
                      <div class="invalid-feedback">Por favor, introduzca su contraseña.</div>
                    </div>

                    <div class="col-12">
                      <label for="accessLevel" class="form-label">Nivel de acceso</label>
                      <select id="accessLevel" class="form-select" name="accesslevel" required>
                        <option></option>
                        <?php foreach ($groups as $group ):?>
                            <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                        <?php endforeach;?>
                      </select>
                      <div class="invalid-feedback">Por favor seleccione un nivel.</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">Estoy de acuerdo y acepto los <a href="#">términos y condiciones.</a></label>
                        <div class="invalid-feedback">Debes aceptar antes de enviar.</div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" name="add_user" type="submit">Crear una cuenta</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">¿Ya tienes una cuenta? <a href="../index.php">Iniciar sesión</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                Designed by <a href="#">Santana</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="/assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/vendor/chart.js/chart.umd.js"></script>
<script src="/assets/vendor/echarts/echarts.min.js"></script>
<script src="/assets/vendor/quill/quill.min.js"></script>
<script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="/assets/vendor/tinymce/tinymce.min.js"></script>
<script src="/assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="/assets/js/main.js"></script>

</body>

</html>