<?php
 $page_title = "All registered trials";
 $review_essay = 'show';
 require_once "../config/load.php";
?>

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
    'moisture_oven' => 'Moisture Oven',
    'moisture_constant_mass' => 'Moisture Constant Mass',
    'moisture_microwave' => 'Moisture Microwave',
    'atterberg_limit' => 'Atterberg Limit',
    'grain_size_general' => 'Grain Size General',
    'grain_size_fine' => 'Grain Size Fine',
    'grain_size_coarse' => 'Grain Size Coarse',
    'grain_size_coarsethan' => 'Grain Size Coarsethan',
    'specific_gravity' => 'Specific Gravity',
    'specific_gravity_coarse' => 'Specific Gravity Coarse',
    'specific_gravity_fine' => 'Specific Gravity Fine',
    'los_angeles_abrasion_coarse_filter' => 'Los Angeles Abrasion Coarse Filter',
    'los_angeles_abrasion_coarse_aggregate' => 'Los Angeles Abrasion Coarse Aggregate',
    // Add more tables as needed
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
                                $link = '';

                                switch ($testType) {
                                    case 'AL':
                                        $link = '../reviews/atterberg-limit.php?id=' . $entry['id'];
                                        break;
                                    case 'MC_Oven':
                                        $link = '../reviews/moisture-oven.php?id=' . $entry['id'];
                                        break;
                                    case 'MC_Microwave':
                                        $link = '../reviews/moisture-microwave.php?id=' . $entry['id'];
                                        break;
                                    case 'MC_Constant_Mass':
                                        $link = '../reviews/moisture-constant-mass.php?id=' . $entry['id'];
                                        break;
                                    case 'GS':
                                        $link = '../reviews/grain-size.php?id=' . $entry['id'];
                                        break;
                                    case 'GS-Fine':
                                        $link = '../reviews/grain-size-fine-agg.php?id=' . $entry['id'];
                                        break;
                                    case 'GS-Coarse':
                                        $link = '../reviews/grain-size-coarse-agg.php?id=' . $entry['id'];
                                        break;
                                    case 'GS-CoarseThan':
                                        $link = '../reviews/grain-size-coarsethan-agg.php?id=' . $entry['id'];
                                        break;
                                    case 'SG':
                                        $link = '../reviews/specific-gravity.php?id=' . $entry['id'];
                                        break;
                                    case 'SG-Coarse':
                                        $link = '../reviews/specific-gravity-coarse-aggregates.php?id=' . $entry['id'];
                                        break;
                                    case 'SG-Fine':
                                        $link = '../reviews/specific-gravity-fine-aggregate.php?id=' . $entry['id'];
                                        break;
                                    case 'LAA_Coarse_Filter':
                                        $link = '../reviews/LAA-Small.php?id=' . $entry['id'];
                                        break;
                                    case 'LAA_Coarse_Aggregate':
                                        $link = '../reviews/LAA-Large.php?id=' . $entry['id'];
                                        break;
                                    default:
                                        $link = '#';
                                        break;
                                }
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