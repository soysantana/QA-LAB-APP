<?php
$page_title = 'Essay Review';
$review = 'show';
require_once('../config/load.php');
page_require_level(1);
include_once('../components/header.php');
?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Essay Review</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Essay Review</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">

            <?php echo display_msg($msg); ?>

            <div class="col-lg-6">
                <?php displayAccordion([
                    'atterberg_limit' => 'Atterberg Limit',
                    'moisture_oven' => 'Moisture Oven',
                    'moisture_constant_mass' => 'Moisture Constant Mass',
                    'moisture_microwave' => 'Moisture Microwave',
                    'moisture_scale' => 'Moisture Scale',
                    'grain_size_general' => 'Grain Size General',
                    'grain_size_coarse' => 'Grain Size Coarse Filter',
                    'grain_size_fine' => 'Grain Size Fine Filter',
                    'soundness' => 'Soundness',
                    'specific_gravity' => 'Specific Gravity',
                    'specific_gravity_coarse' => 'Specific Gravity Coarse',
                    'specific_gravity_fine' => 'Specific Gravity Fine',
                    'standard_proctor' => 'Standard Proctor',
                ]); ?>
            </div>

            <div class="col-lg-6">
                <?php displayAccordion([
                    'grain_size_upstream_transition_fill' => 'Grain Size UTF',
                    'grain_size_lpf' => 'Grain Size LPF',
                    'grain_size_full' => 'Grain Size Full',
                    'point_load' => 'PLT',
                    'sand_castle_test' => 'Sand Castle',
                    'unixial_compressive' => 'UCS',
                    'brazilian' => 'BTS',
                    'los_angeles_abrasion_small' => 'LAA Small',
                    'los_angeles_abrasion_large' => 'LAA Large',
                    'pinhole_test' => 'Pinhole',
                    'hydrometer' => 'Hydrometer',
                    'double_hydrometer' => 'Double Hydrometer',
                    'reactivity' => 'Reactivity',
                ]); ?>
            </div>

        </div>
    </section>

</main>

<?php include_once('../components/footer.php');  ?>

<?php
function fetchData($tableName, $applyDateFilter = false)
{
    $week = date('Y-m-d', strtotime('-14 days'));
    $query = $applyDateFilter
        ? "SELECT * 
       FROM {$tableName} p 
       WHERE NOT EXISTS (
           SELECT 1 FROM test_reviewed tr WHERE tr.Tracking = p.id
       )
       AND NOT EXISTS (
           SELECT 1 FROM test_repeat tpr WHERE tpr.Tracking = p.id
       )
       AND p.Registed_Date >= '$week'"
        : "SELECT * FROM {$tableName}";

    return find_by_sql($query);
}


function getTestLink($testType, $id)
{
    return "../pages/review_test.php?type={$testType}&id={$id}";
}


function displayAccordion($tables)
{
    echo '<div class="card"><div class="card-body">';
    echo '<h5 class="card-title">Essay menu under review</h5>';
    echo '<div class="accordion accordion-flush" id="accordionFlushExample">';

    foreach ($tables as $tableName => $displayName) {
        $data = fetchData($tableName, true);
        if (empty($data)) continue;

        echo '<div class="accordion-item">';
        echo '<h2 class="accordion-header" id="flush-heading' . $tableName . '">';
        echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' . $tableName . '" aria-expanded="false" aria-controls="flush-collapse' . $tableName . '">';
        echo $displayName;
        echo '</button></h2>';
        echo '<div id="flush-collapse' . $tableName . '" class="accordion-collapse collapse" aria-labelledby="flush-heading' . $tableName . '" data-bs-parent="#accordionFlushExample">';
        echo '<div class="accordion-body">';

        foreach ($data as $entry) {
            $link = getTestLink($entry['Test_Type'], $entry['id']);
            echo '<a href="' . $link . '" class="text-danger">' . $entry['Sample_ID'] . '-' . $entry['Sample_Number'] . '</a><br>';
        }

        echo '</div></div></div>';
    }

    echo '</div></div></div>';
}
?>