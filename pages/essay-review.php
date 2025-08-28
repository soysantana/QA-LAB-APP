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
                    'grain_size_coarsethan' => 'Grain Size Coarsethan',
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
                    'unixial_compressive' => 'UCS',
                    'brazilian' => 'BTS',
                    'los_angeles_abrasion_small' => 'LAA Small',
                    'los_angeles_abrasion_large' => 'LAA Large',
                    'pinhole_test' => 'Pinhole',
                    'hydrometer' => 'Hydrometer',
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
    $links = [
        'AL' => '../reviews/atterberg-limit.php?id=',
        'BTS' => '../reviews/brazilian.php?id=',
        'GS' => '../reviews/grain-size.php?id=',
        'GS-CoarseThan' => '../reviews/grain-size-coarsethan-agg.php?id=',
        'LAA_Large' => '../reviews/LAA-Large.php?id=',
        'LAA_Small' => '../reviews/LAA-Small.php?id=',
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
        'GS-TRF' => '../reviews/grain-size-full.php?id=',
        'GS-UFF' => '../reviews/grain-size-full.php?id=',
        'GS-FRF' => '../reviews/grain-size-full.php?id=',
        'GS-IRF' => '../reviews/grain-size-full.php?id=',
        'GS-RF' => '../reviews/grain-size-full.php?id=',
        'GS-BF' => '../reviews/grain-size-full.php?id=',
        'HY' => '../reviews/hydrometer.php?id=',
        'AR-CF' => '../reviews/reactivity-coarse.php?id=',
        'AR-FF' => '../reviews/reactivity-fine.php?id=',
    ];

    return isset($links[$testType]) ? $links[$testType] . $id : '#';
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