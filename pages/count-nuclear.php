<?php
  $page_title = 'Count For Nuclear Gauche';
  $class_form = ' ';
  $form_show = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Count For Nuclear Gauche</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Concrete</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <div id="product_info"></div>

    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Trial Information</h5>

          <!-- Multi Columns Form -->
          <form class="row g-3">
            <div class="col-md-6">
              <label for="Standard" class="form-label">Standard</label>
              <select id="Standard" class="form-select">
                <option selected>Choose...</option>
                <option>ASTM</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="TestMethod" class="form-label">Test Method</label>
              <input type="text" class="form-control" id="TestMethod">
              </select>
            </div>
            <div class="col-md-6">
              <label for="Technician" class="form-label">Technician</label>
              <input type="text" class="form-control" id="Technician">
            </div>
            <div class="col-md-6">
              <label for="DateTesting" class="form-label">Date of Testing</label>
              <input type="date" class="form-control" id="DateTesting">
            </div>
            <div class="col-12">
              <label for="Comments" class="form-label">Comments</label>
              <textarea class="form-control" id="Comments" style="height: 100px;"></textarea>
            </div>
          </form><!-- End Multi Columns Form -->

        </div>
      </div>

    </div>

    <div class="col-lg-9">

    <div class="card">
            <div class="card-body">
              <h5 class="card-title">Testing Information</h5>
              <!-- Bordered Table -->
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">#</th>
                    <th scope="row">Date of Count</th>
                    <th scope="row">Value of Count</th>
                    <th scope="row">Rank to Date</th>
                    <th scope="row">Operator</th>
                  </tr>

                  <?php
                  $numFilas = 5; // Número de filas que deseas generar
                  
                  for ($i = 1; $i <= $numFilas; $i++) {
                    echo '<tr>';
                    echo '<th scope="row">' . $i . '</th>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="DateCount' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="ValCount' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="RankDate' . $i . '"></td>';
                    echo '<td><input type="text" style="border: none;" class="form-control" id="Operator' . $i . '"></td>';
                    echo '</tr>';
                  }
                  ?>

                </tbody>
              </table>
              <!-- End Bordered Table -->

            </div>
          </div>

    </div>
    
    
    <div class="col-lg-3">
      
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Actions</h5>
        <!-- Actions Buttons -->
        <div class="d-grid gap-2 mt-3">
          <button type="button" class="btn btn-success">Save Essay</button>
        </div>
  
      </div>
    </div>
          
  </div>


  </div>

  </div>
</section>

</main><!-- End #main -->


<?php include_once('../components/footer.php');  ?>