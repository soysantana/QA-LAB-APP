<?php
  $page_title = 'Message & Notify';
  require_once('../config/load.php');
?>

<?php
if (isset($_POST['update-signed'])) {

    $sample_id = $db->escape($_POST['Sample_ID']);
    $sample_number = $db->escape($_POST['Sample_Number']);

    // Mapeo de test_type
    $testTypeMappings = [
      'AL' => ['AL'],
      'BTS' => ['BTS'],
      'GS' => ['GS_Fine', 'GS_Coarse', 'GS_CoarseThan', 'GS_FF', 'GS_CF', 'GS_LPF', 'GS_UTF'],
      'LAA' => ['LAA_Coarse_Filter', 'LAA_Coarse_Aggregate'],
      'MC' => ['MC_Oven', 'MC_Microwave', 'MC_Constant_Mass', 'MC_Scale'],
      'PLT' => ['PLT'],
      'SG' => ['SG_Coarse', 'SG_Fine'],
      'SP' => ['SP'],
      'UCS' => ['UCS'],
    ];

    $update_count = 0; // Contador para saber cuántas filas se actualizan

    for ($i = 1; $i <= 20; $i++) {
        $testTypeKey = 'Test_Type' . $i;
        $testTypeValueKey = 'Test_Type' . $i . '_value';

        // Obtener el valor de Test_Type del campo oculto
        $testTypeValue = isset($_POST[$testTypeValueKey]) ? $db->escape($_POST[$testTypeValueKey]) : '';

        // Verificar si el checkbox está presente en $_POST
        if (isset($_POST[$testTypeKey])) {
            $signed = 1; // Asignamos 1 porque está marcado
        } else {
            $signed = 0; // Asignamos 0 porque está desmarcado
        }

        // Mapear el valor de Test_Type si existe en el mapeo
        $mappedValues = [];
        foreach ($testTypeMappings as $key => $values) {
            if ($key === $testTypeValue) {
                $mappedValues = $values;
                break;
            }
        }

        // Actualizar la tabla test_reviewed
        foreach ($mappedValues as $mappedValue) {
            $query = "UPDATE test_reviewed SET Signed = '{$signed}' 
                      WHERE Sample_Name = '{$sample_id}' 
                      AND Sample_Number = '{$sample_number}' 
                      AND Test_Type = '{$mappedValue}'";
            $result = $db->query($query);

            // Depuración: Verifica el resultado de la consulta
            if ($result) {
                if ($db->affected_rows() > 0) {
                    $update_count++;
                }
            } else {
                echo "Error executing query: " . $db->error . "<br>";
            }
        }
    }

    // Mensaje de sesión basado en el número de filas actualizadas
    if ($update_count > 0) {
        $session->msg('s', 'Sample has been updated');
    } else {
        $session->msg('w', 'No changes were made');
    }

    // Redireccionar a la página apropiada
    header('Location: ../pages/message.php');
    exit();
}
?>

<?php page_require_level(2); ?>
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
            'BTS' => '../reviews/brazilian.php',
            'GS' => '../reviews/grain-size.php',
            'GS-Fine' => '../reviews/grain-size-fine-agg.php',
            'GS-Coarse' => '../reviews/grain-size-coarse-agg.php',
            'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php',
            'GS_FF' => '../reviews/grain-size-fine-filter.php',
            'GS_CF' => '../reviews/grain-size-coarse-filter.php',
            'GS_LPF' => '../reviews/grain-size-lpf.php',
            'GS_UTF' => '../reviews/grain-size-upstream-transition-fill.php',
            'LAA_Coarse_Aggregate' => '../reviews/LAA-Large.php',
            'LAA_Coarse_Filter' => '../reviews/LAA-Small.php',
            'MC_Oven' => '../reviews/moisture-oven.php',
            'MC_Microwave' => '../reviews/moisture-microwave.php',
            'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php',
            'MC_Scale' => '../reviews/moisture-scale.php',
            'PLT' => '../reviews/point-Load.php',
            'SG' => '../reviews/specific-gravity.php',
            'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php',
            'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php',
            'SP' => '../reviews/standard-proctor.php',
            'UCS' => '../reviews/unixial-compressive.php',
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
            'BTS' => '../reviews/brazilian.php',
            'GS' => '../reviews/grain-size.php',
            'GS-Fine' => '../reviews/grain-size-fine-agg.php',
            'GS-Coarse' => '../reviews/grain-size-coarse-agg.php',
            'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php',
            'GS_FF' => '../reviews/grain-size-fine-filter.php',
            'GS_CF' => '../reviews/grain-size-coarse-filter.php',
            'GS_LPF' => '../reviews/grain-size-lpf.php',
            'GS_UTF' => '../reviews/grain-size-upstream-transition-fill.php',
            'LAA_Coarse_Aggregate' => '../reviews/LAA-Large.php',
            'LAA_Coarse_Filter' => '../reviews/LAA-Small.php',
            'MC_Oven' => '../reviews/moisture-oven.php',
            'MC_Microwave' => '../reviews/moisture-microwave.php',
            'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php',
            'MC_Scale' => '../reviews/moisture-scale.php',
            'PLT' => '../reviews/point-Load.php',
            'SG' => '../reviews/specific-gravity.php',
            'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php',
            'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php',
            'SP' => '../reviews/standard-proctor.php',
            'UCS' => '../reviews/unixial-compressive.php',
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

<?php
$Reviewed = find_all('test_reviewed'); // Obtener todas las revisiones

// Recorremos cada requisición
foreach ($Requisitions as $req): ?>
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
                                            <th scope="col">Essays</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Define your mapping of test types
                                        $testTypeMappings = [
                                            'AL' => ['AL'],
                                            'BTS' => ['BTS'],
                                            'GS' => ['GS_Fine', 'GS_Coarse', 'GS_CoarseThan'],
                                            'LAA' => ['LAA_Coarse_Filter', 'LAA_Coarse_Aggregate'],
                                            'MC' => ['MC_Oven', 'MC_Microwave', 'MC_Constant_Mass'],
                                            'PLT' => ['PLT'],
                                            'SG' => ['SG_Coarse', 'SG_Fine'],
                                            'SP' => ['SP'],
                                            'UCS' => ['UCS'],
                                        ];

                                        for ($i = 1; $i <= 19; $i++) {
                                            $testTypeKey = 'Test_Type' . $i;
                                            if (isset($req[$testTypeKey]) && $req[$testTypeKey] !== '') {
                                                $testTypeValue = $req[$testTypeKey];

                                                // Determine the mapped values
                                                $mappedValues = [];
                                                foreach ($testTypeMappings as $key => $values) {
                                                    if (is_array($values)) {
                                                        if ($testTypeValue === $key) {
                                                            $mappedValues = $values;
                                                            break;
                                                        }
                                                    } else {
                                                        if ($testTypeValue === $values) {
                                                            $mappedValues = [$values];
                                                            break;
                                                        }
                                                    }
                                                }

                                                // Check if the test type value is mapped
                                                $signed = false;
                                                foreach ($Reviewed as $review) {
                                                    if ($review['Sample_Name'] == $req['Sample_ID'] && 
                                                        $review['Sample_Number'] == $req['Sample_Number'] && 
                                                        in_array($review['Test_Type'], $mappedValues) && 
                                                        $review['Signed'] == 1) {
                                                        $signed = true;
                                                        break;
                                                    }
                                                }

                                                ?>
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input me-1" type="checkbox" 
                                                           name="Test_Type<?php echo $i; ?>" 
                                                           id="testType<?php echo $i; ?>" 
                                                           value="<?php echo $testTypeValue; ?>" 
                                                           <?php echo $signed ? 'checked' : ''; ?>>
                                                        <!-- Hidden field for Test_Type -->
                                                        <input type="hidden" name="Test_Type<?php echo $i; ?>_value" value="<?php echo $testTypeValue; ?>">
                                                    </td>
                                                    <td><?php echo $testTypeValue; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <!-- Hidden fields for Sample_ID and Sample_Number -->
                                <input type="hidden" name="Sample_ID" value="<?php echo $req['Sample_ID']; ?>">
                                <input type="hidden" name="Sample_Number" value="<?php echo $req['Sample_Number']; ?>">
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