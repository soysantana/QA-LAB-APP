<?php
  $page_title = 'Message & Notify';
  require_once('../config/load.php');
?>

<?php

if (isset($_POST['update-signed'])) {
  $Requisitions = find_all('lab_test_requisition_form');
  $Reviewed = find_all('test_reviewed');

  foreach ($Requisitions as $req) {
      for ($i = 1; $i <= 19; $i++) {
          // Verificar si el test_type correspondiente existe en el formulario
          $testTypeKey = "Test_Type$i";
          if (isset($_POST[$testTypeKey])) {
              $testTypeValue = $_POST[$testTypeKey];
              // Verificar si se ha enviado algún valor para el checkbox
              $signed = isset($_POST[$testTypeKey]) ? 1 : 0;

              // Verificar si hay una revisión existente para este ensayo
              $existing_review = false;
              foreach ($Reviewed as $review) {
                  if ($review['Sample_Name'] == $req['Sample_ID'] && $review['Sample_Number'] == $req['Sample_Number']) {
                      // Actualizar la revisión existente
                      $existing_review = true;
                      $query = "UPDATE test_reviewed SET Signed = '{$signed}' WHERE Sample_Name = '{$req['Sample_ID']}' AND Sample_Number = '{$req['Sample_Number']}' AND Test_Type = '{$testTypeValue}'";
                      $result = $db->query($query);
                      if ($result && $db->affected_rows() === 1) {
                          $session->msg('s', 'Sample has been updated');
                      } else {
                          $session->msg('w', 'No changes were made');
                      }
                      break;
                  }
              }
          }
      }
  }

  // Redireccionar a la página apropiada
  header('Location: ../pages/message.php');
}


?>

<?php page_require_level(1); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Notification</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Forms</li>
      <li class="breadcrumb-item active">Notification</li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">

  <div class="col-lg-8">
  <?php echo display_msg($msg); ?>
  </div>

  <div class="col-lg-6">

  <?php $week = date('Y-m-d', strtotime('-14 days')); ?>
  <?php $RevNotify = find_by_sql("SELECT * FROM test_reviewed WHERE Start_Date >= '{$week}' ORDER BY Start_Date DESC"); ?>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Reviewed</h5>
      
      <div class="list-group">
      <?php foreach ($RevNotify as $revNotify) : ?>
        <?php
           $urls = array(
            'AL' => '../reviews/atterberg-limit.php',
            'MC_Oven' => '../reviews/moisture-oven.php',
            'MC_Microwave' => '../reviews/moisture-microwave.php',
            'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php',
            'GS' => '../reviews/grain-size.php',
            'GS-Fine' => '../reviews/grain-size-fine-agg.php',
            'GS-Coarse' => '../reviews/grain-size-coarse-agg.php',
            'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php',
            'SG' => '../reviews/specific-gravity.php',
            'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php',
            'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php',
            'SP' => '../reviews/standard-proctor.php',
           );
           
           $id = $revNotify['Sample_Name'];
           $number = $revNotify['Sample_Number'];
           $testType = $revNotify['Test_Type'];
           $tracking = $revNotify['Tracking'];
           
           if (array_key_exists($testType, $urls)) {
            $url = $urls[$testType];
             echo "<a href=\"$url?id=$tracking\" class=\"list-group-item list-group-item-action\">$id-$number-$testType</a>";
           } else {
             echo "<a href=\"#\" class=\"list-group-item list-group-item-action\"><i class=\"bi bi-eye\"></i></a>";
           }
        ?>
      <?php endforeach; ?>
      </div>
      
    </div>
  </div>

  </div>

  <div class="col-lg-6">

  <?php $RepNotify = find_by_sql("SELECT * FROM test_repeat WHERE Start_Date >= '{$week}' ORDER BY Start_Date DESC"); ?>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Repeat</h5>

      <div class="list-group">
      <?php foreach ($RepNotify as $repNotify) : ?>
        <?php
           $urls = array(
            'AL' => '../reviews/atterberg-limit.php',
            'MC_Oven' => '../reviews/moisture-oven.php',
            'MC_Microwave' => '../reviews/moisture-microwave.php',
            'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php',
            'GS' => '../reviews/grain-size.php',
            'GS-Fine' => '../reviews/grain-size-fine-agg.php',
            'GS-Coarse' => '../reviews/grain-size-coarse-agg.php',
            'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php',
            'SG' => '../reviews/specific-gravity.php',
            'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php',
            'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php',
            'SP' => '../reviews/standard-proctor.php',
           );
           
           $id = $repNotify['Sample_Name'];
           $number = $repNotify['Sample_Number'];
           $testType = $repNotify['Test_Type'];
           $tracking = $repNotify['Tracking'];
           
           if (array_key_exists($testType, $urls)) {
            $url = $urls[$testType];
             echo "<a href=\"$url?id=$tracking\" class=\"list-group-item list-group-item-action\">$id-$number-$testType</a>";
           } else {
             echo "<a href=\"#\" class=\"list-group-item list-group-item-action\"><i class=\"bi bi-eye\"></i></a>";
           }
        ?>
      <?php endforeach; ?>
      </div>

    </div>
  </div>

  </div>

  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Signed</h5>
        <?php $Requisitions = find_all('lab_test_requisition_form'); ?>
        <!-- Table with stripped rows -->
        <table class="table datatable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Sample ID</th>
              <th scope="col">Sample Number</th>
              <th scope="col">Material Type</th>
              <th scope="col">Collection</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($Requisitions as $req): ?>
            <tr>
              <td><?php echo count_id(); ?></td>
              <td><?php echo $req['Sample_ID']; ?></td>
              <td><?php echo $req['Sample_Number']; ?></td>
              <td><?php echo $req['Material_Type']; ?></td>
              <td><?php echo $req['Sample_Date']; ?></td>
              <td><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#requisitionview<?php echo $req['id']; ?>"><i class="bi bi-eye"></i></button></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <!-- End Table with stripped rows -->
      </div>
    </div>
  </div>
  
  <?php $Reviewed = find_all('test_reviewed'); ?>
<?php foreach ($Requisitions as $req): ?>
<form method="post" action="">
  <div class="modal" id="requisitionview<?php echo $req['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Essay detail</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <div class="container">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Signed essays</h5>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Signed ✅</th>
                      <th scope="col">Essays</th> <!-- Corregido typo: "Esays" a "Essays" -->
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $testTypeMappings = [
                      'MC' => [
                          'MC_Oven',
                          'MC_Microwave'
                      ],
                      'GS' => 'GS_General'
                    ];

                    for ($i = 1; $i <= 19; $i++) {
                      $testTypeKey = 'Test_Type' . $i;
                      if (isset($req[$testTypeKey]) && $req[$testTypeKey] !== '') {
                        $testTypeValue = $req[$testTypeKey];

                        // Check if there's a mapping for this test type
                        if (isset($testTypeMappings[$testTypeValue])) {
                          if (is_array($testTypeMappings[$testTypeValue])) {
                            $testTypeValue = $testTypeMappings[$testTypeValue][0];
                          } else {
                            $testTypeValue = $testTypeMappings[$testTypeValue];
                          }
                        }

                        $signed = false;
                        foreach ($Reviewed as $review) {
                          if ($review['Sample_Name'] == $req['Sample_ID'] && $review['Sample_Number'] == $req['Sample_Number'] && $review['Test_Type'] == $testTypeValue && $review['Signed'] == 1) {
                            $signed = true;
                            break;
                          }
                        }
                        ?>
                        <tr>
                          <td><input class="form-check-input me-1" type="checkbox" name="Test_Type<?php echo $i; ?>" id="testType<?php echo $i; ?>" value="<?php echo $testTypeValue; ?>" <?php echo $signed ? 'checked' : ''; ?>></td>
                          <td><?php echo $testTypeValue; ?></td>
                        </tr>
                        <?php
                      }
                    }
                    ?>
                  </tbody>
                </table>
              </div> <!-- End Signed essays -->
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success" name="update-signed">Update</button>
        </div>

      </div>
    </div>
  </div><!-- End Modal-->
</form>
<?php endforeach; ?>


  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>