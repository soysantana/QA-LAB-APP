<?php
 $page_title = "All registered trials";
 $review_essay = 'show';
 require_once "../config/load.php";
?>

<?php page_require_level(2); ?>
<?php include_once('../components/header.php'); ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Ensayos Registrados</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item active">Ensayos Registrados</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<?php echo display_msg($msg); ?>

<section class="section dashboard" class="">
  <div class="row">

  <!-- Left side columns -->
  <div class="col-lg-8">
    <div class="row">

    <?php
// Array of tables and their display names
$tables = [
    'atterberg_limit' => 'Atterberg Limit',
    'brazilian' => 'BTS',
    'grain_size_general' => 'Grain Size General',
    'grain_size_coarse' => 'Grain Size Coarse',
    'grain_size_fine' => 'Grain Size Fine',
    'grain_size_coarsethan' => 'Grain Size Coarsethan',
    'los_angeles_abrasion_coarse_filter' => 'LAA Small',
    'los_angeles_abrasion_coarse_aggregate' => 'LAA Large',
    'moisture_oven' => 'Moisture Oven',
    'moisture_constant_mass' => 'Moisture Constant Mass',
    'moisture_microwave' => 'Moisture Microwave',
    'pinhole_test' => 'PH',
    'point_load' => 'PLT',
    'soundness' => 'Soundness',
    'specific_gravity' => 'Specific Gravity',
    'specific_gravity_coarse' => 'Specific Gravity Coarse',
    'specific_gravity_fine' => 'Specific Gravity Fine',
    'standard_proctor' => 'Standard Proctor',
    'unixial_compressive' => 'UCS',
];

// Array to hold all data
$allData = [];

// Loop through the tables
foreach ($tables as $tableName => $displayName) {
    // Fetch data for the current table
    $data = find_all($tableName);

    // Check if data is empty
    if (!empty($data)) {
        foreach ($data as $entry) {
            $entry['table_name'] = $displayName; // Add the table name to each entry
            $allData[] = $entry;
        }
    }
}
?>

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Datatables</h5>
            <!-- Table with stripped rows -->
            <table class="table datatable">
                <thead>
                    <tr>
                        <th scope="col">Sample Name</th>
                        <th scope="col">Sample Number</th>
                        <th scope="col">Test Type</th>
                        <th scope="col">Collection</th>
                        <th scope="col">Register By</th>
                        <th scope="col">Registered Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allData as $entry): ?>
                        <tr>
                            <td><?php echo $entry['Sample_ID']; ?></td>
                            <td><?php echo $entry['Sample_Number']; ?></td>
                            <td><?php echo $entry['Test_Type']; ?></td>
                            <td><?php echo $entry['Sample_Date']; ?></td>
                            <td><?php echo $entry['Register_By']; ?></td>
                            <td><?php echo $entry['Registed_Date']; ?></td>
                            <td>
                        <?php
                            $testType = $entry['Test_Type'];
                            $id = $entry['id'];

                            // Mapa de enlaces según el tipo de prueba
                            $links = [
                            'AL' => '../reviews/atterberg-limit.php?id=',
                            'BTS' => '../reviews/brazilian.php?id=',
                            'GS' => '../reviews/grain-size.php?id=',
                            'GS-Fine' => '../reviews/grain-size-fine-agg.php?id=',
                            'GS-Coarse' => '../reviews/grain-size-coarse-agg.php?id=',
                            'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php?id=',
                            'LAA_Coarse_Aggregate' => '../reviews/LAA-Large.php?id=',
                            'LAA_Coarse_Filter' => '../reviews/LAA-Small.php?id=',
                            'MC_Oven' => '../reviews/moisture-oven.php?id=',
                            'MC_Microwave' => '../reviews/moisture-microwave.php?id=',
                            'MC_Constant_Mass' => '../reviews/moisture-constant-mass.php?id=',
                            'MC_Scale' => '../reviews/moisture-scale.php?id=',
                            'PLT' => '../reviews/point-Load.php?id=',
                            'SND' => '../reviews/soundness.php?id=',
                            'SG' => '../reviews/specific-gravity.php?id=',
                            'SG-Coarse' => '../reviews/specific-gravity-coarse-aggregates.php?id=',
                            'SG-Fine' => '../reviews/specific-gravity-fine-aggregate.php?id=',
                            'SP' => '../reviews/standard-proctor.php?id=',
                            'UCS' => '../reviews/unixial-compressive.php?id=',
                            'PH' => '../reviews/pinhole-test.php?id=',
                            'GS_CF' => '../reviews/grain-size-coarse-filter.php?id=',
                            'GS_FF' => '../reviews/grain-size-fine-filter.php?id=',
                            'GS_LPF' => '../reviews/grain-size-lpf.php?id=',
                            'GS_UTF' => '../reviews/grain-size-upstream-transition-fill.php?id=',
                        ];

                        // Asignar el enlace basado en el tipo de prueba, o un enlace por defecto
                        $link = isset($links[$testType]) ? $links[$testType] . $id : '#';
                       ?>

<a class="btn btn-primary" href="<?php echo $link; ?>"><i class="bi bi-eye"></i></a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- End Table with stripped rows -->
        </div>
    </div>
</div>



    </div>
  </div><!-- End Left side columns -->

  <!-- Right side columns -->
  <div class="col-lg-4">



  </div><!-- End Right side columns -->

  </div>
</section>

</main><!-- End #main -->
<?php include_once('../components/footer.php'); ?>