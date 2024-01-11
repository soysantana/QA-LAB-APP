<?php
   $page_title = 'User - Register';
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
                <a href="../index.php" class="logo d-flex align-items-center w-auto">
                  <img src="/app/assets/img/BARRICK-GOLD-logo.svg" width="200px" alt="logo-barrick">
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <?php echo display_msg($msg); ?>

                  <form class="row g-3 needs-validation" method="post" action="users-register.php" novalidate>
                    <div class="col-6">
                      <label for="yourName" class="form-label">Your Name</label>
                      <input type="text" name="full-name" class="form-control" id="yourName" required>
                      <div class="invalid-feedback">Please, enter your name!</div>
                    </div>

                    <div class="col-6">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please choose a username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Your Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail">
                      <div class="invalid-feedback">Please enter a valid Email adddress!</div>
                    </div>

                    <div class="col-6">
                      <label for="yourPhone" class="form-label">Your Phone</label>
                      <input type="tel" name="phone" class="form-control" id="yourPhone" pattern="[0-9]{10}">
                      <div class="invalid-feedback">Please enter a valid phone number!</div>
                    </div>

                    <div class="col-6">
                      <label for="yourJob" class="form-label">Your Job</label>
                      <input type="text" name="Job" class="form-control" id="yourJob">
                      <div class="invalid-feedback">Please, enter your job!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <label for="accessLevel" class="form-label">Access Level</label>
                      <select id="accessLevel" class="form-select" name="accesslevel" required>
                        <option></option>
                        <?php foreach ($groups as $group ):?>
                            <option value="<?php echo $group['group_level'];?>"><?php echo ucwords($group['group_name']);?></option>
                        <?php endforeach;?>
                      </select>
                      <div class="invalid-feedback">Please select a level!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" name="add_user" type="submit">Create Account</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Already have an account? <a href="../index.php">Log in</a></p>
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
<script src="/app/assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="/app/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/app/assets/vendor/chart.js/chart.umd.js"></script>
<script src="/app/assets/vendor/echarts/echarts.min.js"></script>
<script src="/app/assets/vendor/quill/quill.min.js"></script>
<script src="/app/assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="/app/assets/vendor/tinymce/tinymce.min.js"></script>
<script src="/app/assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="/app/assets/js/main.js"></script>

</body>

</html>