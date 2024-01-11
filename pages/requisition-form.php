<?php
  $page_title = 'Requisition Form';
  $requisition_form = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Requisition Form</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Pages</li>
      <li class="breadcrumb-item active">Requisition Form</li>
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
            <div class="col-md-3">
              <label for="ProjectName" class="form-label">Project Name</label>
              <input type="text" class="form-control" id="ProjectName">
            </div>
            <div class="col-md-3">
              <label for="Client" class="form-label">Client</label>
              <input type="text" class="form-control" id="Client">
            </div>
            <div class="col-md-3">
              <label for="ProjectNumber" class="form-label">Project Number</label>
              <input type="text" class="form-control" id="ProjectNumber">
            </div>
            <div class="col-md-3">
              <label for="PackageID" class="form-label">Package ID</label>
              <input type="text" class="form-control" id="PackageID">
            </div>
            <div class="col-md-3">
              <label for="Structure" class="form-label">Structure</label>
              <input type="text" class="form-control" id="Structure">
            </div>
            <div class="col-md-3">
              <label for="Area" class="form-label">Area</label>
              <input type="text" class="form-control" id="Area">
            </div>
            <div class="col-md-3">
              <label for="Source" class="form-label">Source</label>
              <input type="text" class="form-control" id="Source">
            </div>
            <div class="col-md-3">
              <label for="CollectionDate" class="form-label">Collection Date</label>
              <input type="text" class="form-control" id="CollectionDate">
            </div>
            <div class="col-md-3">
              <label for="SampleName" class="form-label">Sample Name</label>
              <input type="text" class="form-control" id="SampleName">
            </div>
            <div class="col-md-3">
              <label for="SampleNumber" class="form-label">Sample Number</label>
              <input type="text" class="form-control" id="SampleNumber">
            </div>
            <div class="col-md-3">
              <label for="DepthFrom" class="form-label">Depth From</label>
              <input type="text" class="form-control" id="DepthFrom">
            </div>
            <div class="col-md-3">
              <label for="DepthTo" class="form-label">Depth To</label>
              <input type="text" class="form-control" id="DepthTo">
            </div>
            <div class="col-md-6">
              <label for="MType" class="form-label">Material Type</label>
              <select id="MType" class="form-select">
                <option selected>Choose...</option>
                <option>Soil</option>
                <option>Rock</option>
                <option>CF</option>
                <option>FF</option>
                <option>FRF</option>
                <option>IRF</option>
                <option>LPF</option>
                <option>UTF</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="SType" class="form-label">Sample Type</label>
              <select id="SType" class="form-select">
                <option selected>Choose...</option>
                <option>Grab</option>
                <option>Bulk</option>
                <option>Truck</option>
                <option>Shelby</option>
                <option>Lexan</option>
                <option>Ring</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="PMethods" class="form-label">Preparation Methods</label>
              <select id="PMethods" class="form-select">
                <option selected>Choose...</option>
                <option>Oven Dried</option>
                <option>Air Dried</option>
                <option>Microwave Dried</option>
                <option>Wet</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="SMethods" class="form-label">Split Methods</label>
              <select id="SMethods" class="form-select">
                <option selected>Choose...</option>
                <option>Manual</option>
                <option>Mechanical</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="NatMc" class="form-label">Natural Mc %</label>
              <input type="text" class="form-control" id="NatMc" oninput="LLyPL()">
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

  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>