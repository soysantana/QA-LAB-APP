<?php
  $page_title = 'Lista de Pendientes';
  $Pending_List = 'show';
  require_once('../config/load.php');
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Lista de Pendientes</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Paginas</li>
      <li class="breadcrumb-item active">Lista de Pendientes</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<div class="col-md-4">
  <?php echo display_msg($msg); ?>
</div>

<section class="section">
  <div class="row">

  <form class="row">
   <div class="col-lg-9">
      <div class="card">
         <div class="card-body">
            <h5 class="card-title"></h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
               <thead>
                  <tr>
                     <th scope="col">#</th>
                     <th scope="col">Muestra</th>
                     <th scope="col">Numero de muestra</th>
                     <th scope="col">Tipo de prueba</th>
                     <th scope="col">Fecha de la muestra</th>
                  </tr>
               </thead>
               <tbody>
               <?php
                $Requisition = find_all("lab_test_requisition_form");
                $Preparation = find_all("test_preparation");
                $Review = find_all("test_review");
                $testTypes = [];
                
                foreach ($Requisition as $requisition) {
                    for ($i = 1; $i <= 20; $i++) {
                        $testTypeKey = "Test_Type" . $i;
                        
                        if (
                            isset($requisition[$testTypeKey]) &&
                            $requisition[$testTypeKey] !== null &&
                            $requisition[$testTypeKey] !== ""
                            ) {
                                $matchingPreparations = array_filter($Preparation, function (
                                    $preparation
                                    ) use ($requisition, $testTypeKey) {
                                        return $preparation["Sample_Name"] ===
                                        $requisition["Sample_ID"] &&
                                        $preparation["Sample_Number"] ===
                                        $requisition["Sample_Number"] &&
                                        $preparation["Test_Type"] === $requisition[$testTypeKey];
                                    });
                                    
                                $matchingReviews = array_filter($Review, function (
                                    $review
                                    ) use ($requisition, $testTypeKey) {
                                        return $review["Sample_Name"] ===
                                        $requisition["Sample_ID"] &&
                                        $review["Sample_Number"] ===
                                        $requisition["Sample_Number"] &&
                                        $review["Test_Type"] === $requisition[$testTypeKey];
                                    });
                                    
                                    if (empty($matchingPreparations) && empty($matchingReviews)) {
                                        $testTypes[] = [
                                            "Sample_ID" => $requisition["Sample_ID"],
                                            "Sample_Number" => $requisition["Sample_Number"],
                                            "Sample_Date" => $requisition["Sample_Date"],
                                            "Test_Type" => $requisition[$testTypeKey],
                                        ];
                                    }
                                }
                            }
                        }
                        
                        usort($testTypes, function ($a, $b) {
                            return strcmp($a["Test_Type"], $b["Test_Type"]);
                        });
               ?>

               <?php foreach ($testTypes as $index => $sample) : ?>
                <tr>
                <th scope="row"><?php echo count_id(); ?></th>
                <td><?php echo $sample['Sample_ID']; ?></td>
                <td><?php echo $sample['Sample_Number']; ?></td>
                <td><?php echo $sample['Test_Type']; ?></td>
                <td><?php echo $sample['Sample_Date']; ?></td>
                </tr>
               <?php endforeach; ?>
               </tbody>
            </table>
            <!-- End Table with stripped rows -->

         </div>
      </div>
   </div>

   <div class="col-lg-3">
    
   <div class="card">
    <div class="card-body">
        <h5 class="card-title">Conteo</h5>
        <ul class="list-group">
        <?php
         $columnas_tipo_prueba = array();
         for ($i = 1; $i <= 20; $i++) {
          $columnas_tipo_prueba[] = "Test_Type" . $i;
         }
        
         $columnas_por_tipo = array();
         foreach ($Requisition as $requisition) {
           foreach ($columnas_tipo_prueba as $columna) {
             $testTypeValue = $requisition[$columna];
             if ($testTypeValue !== null && $testTypeValue !== "") {
               $columnas_por_tipo[$testTypeValue] = $columna;
             }
           }
         }

         $typeCount = [];
         foreach ($testTypes as $sample) {
            $testType = $sample['Test_Type'];
            if (isset($typeCount[$testType])) {
                $typeCount[$testType]++;
            } else {
                $typeCount[$testType] = 1;
            }
         }
         foreach ($typeCount as $testType => $count) :
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <h5><code><?php echo $testType; ?></code></h5>
            <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
            <?php if (isset($columnas_por_tipo[$testType])) : ?>
            <a href="../pdf/pendings.php?columna=<?php echo urlencode($columnas_por_tipo[$testType]); ?>&type=<?php echo urlencode($testType); ?>" class="btn btn-secondary"><i class="bi bi-printer"></i></a>
            <?php else : ?>
            <span class="badge bg-danger rounded-pill">Opps</span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
        </ul>
     </div>
    </div>

   </div>

</form>

  </div>
</section>

</main><!-- End #main -->

<?php include_once('../components/footer.php');  ?>