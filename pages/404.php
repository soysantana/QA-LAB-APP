<?php include_once('../components/style.php');  ?>

<main>
    <div class="container">

      <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <h1>404</h1>
        <h2>The page you are looking for doesn't exist.</h2>
        <a class="btn" href="../index.php">Back to home</a>
        <img src="../assets/img/not-found.svg" class="img-fluid py-5" alt="Page Not Found">
        <div class="credits">
          <!-- All the links in the footer should remain intact. -->
          <!-- You can delete the links only if you purchased the pro version. -->
          <!-- Licensing information: https://bootstrapmade.com/license/ -->
          <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
          Designed by <a href="#">Arturo Santana</a>
        </div>
      </section>

    </div>
  </main><!-- End #main -->

  <a href="" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->

<script src="https://pvj-app.azurewebsites.net/assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/chart.js/chart.umd.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/echarts/echarts.min.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/quill/quill.min.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/tinymce/tinymce.min.js"></script>
<script src="https://pvj-app.azurewebsites.net/assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="https://pvj-app.azurewebsites.net/assets/js/main.js"></script>

</body>

</html>

<?php if(isset($db)) { $db->db_disconnect(); } ?>