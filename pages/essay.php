<?php
$page_title = "Ensayos Registrados";
$review_essay = 'show';
require_once "../config/load.php";

page_require_level(2);
include_once('../components/header.php');
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Ensayos Registrados</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active">Ensayos Registrados</li>
            </ol>
        </nav>
    </div>

    <?php echo display_msg($msg); ?>

    <section class="section">
        <div class="row">
            <?php
            $tables = [
                'atterberg_limit' => 'Atterberg Limit',
                'moisture_oven' => 'Moisture Oven',
                'moisture_constant_mass' => 'Moisture Constant Mass',
                'moisture_microwave' => 'Moisture Microwave',
                'moisture_scale' => 'Moisture Scale',
                'grain_size_general' => 'Grain Size General',
                'grain_size_coarse' => 'Grain Size Coarse',
                'grain_size_fine' => 'Grain Size Fine',
                'grain_size_coarsethan' => 'Grain Size Coarsethan',
                'soundness' => 'Soundness',
                'specific_gravity' => 'Specific Gravity',
                'specific_gravity_coarse' => 'Specific Gravity Coarse',
                'specific_gravity_fine' => 'Specific Gravity Fine',
                'standard_proctor' => 'Standard Proctor',
                'grain_size_upstream_transition_fill' => 'Grain Size UTF',
                'grain_size_lpf' => 'Grain Size LPF',
                'grain_size_fine_filter' => 'Grain Size FF',
                'grain_size_coarse_filter' => 'Grain Size CF',
                'point_load' => 'PLT',
                'unixial_compressive' => 'UCS',
                'brazilian' => 'BTS',
                'los_angeles_abrasion_coarse_filter' => 'LAA Small',
                'los_angeles_abrasion_coarse_aggregate' => 'LAA Large',
                'pinhole_test' => 'Pinhole',
            ];

            $allData = [];
            foreach ($tables as $tableName => $displayName) {
                $data = find_all($tableName);
                if ($data) {
                    foreach ($data as $entry) {
                        $entry['table_name'] = $displayName;
                        $allData[] = $entry;
                    }
                }
            }
            ?>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ensayos Registrados</h5>
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Muestra</th>
                                    <th>Num. Muestra</th>
                                    <th>Tipo de Prueba</th>
                                    <th>Fecha de Muestra</th>
                                    <th>Registrado por</th>
                                    <th>Fecha de Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allData as $entry): ?>
                                    <tr>
                                        <td><?php echo $entry['Sample_ID']; ?></td>
                                        <td><?php echo $entry['Sample_Number']; ?></td>
                                        <td><?php echo $entry['Test_Type']; ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($entry['Sample_Date'])); ?></td>
                                        <td><?php echo $entry['Register_By']; ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($entry['Registed_Date'])); ?></td>
                                        <td>
                                            <a class="btn btn-primary" href="<?php echo get_test_link($entry['Test_Type'], $entry['id']); ?>">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<?php include_once('../components/footer.php'); ?>

<?php
function get_test_link($testType, $id) {
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
    return isset($links[$testType]) ? $links[$testType] . $id : '#';
}
?>