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
                'atterberg_limit',
                'brazilian',
                'grain_size_coarse',
                'grain_size_fine',
                'grain_size_full',
                'grain_size_general',
                'grain_size_lpf',
                'grain_size_upstream_transition_fill',
                'hydrometer',
                'los_angeles_abrasion_large',
                'los_angeles_abrasion_small',
                'moisture_constant_mass',
                'moisture_microwave',
                'moisture_oven',
                'moisture_scale',
                'pinhole_test',
                'point_load',
                'reactivity',
                'soundness',
                'specific_gravity',
                'specific_gravity_coarse',
                'specific_gravity_fine',
                'standard_proctor',
                'unixial_compressive'
            ];

            $allData = array_reduce($tables, function ($carry, $tableName) {
                $data = find_all($tableName);
                foreach ($data as $entry) {
                    $entry['table_name'] = $tableName;
                    $carry[] = $entry;
                }
                return $carry;
            }, []);
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
                                        <td><?= htmlspecialchars($entry['Sample_ID']); ?></td>
                                        <td><?= htmlspecialchars($entry['Sample_Number']); ?></td>
                                        <td><?= htmlspecialchars($entry['Test_Type']); ?></td>
                                        <td><?= date('Y-m-d', strtotime($entry['Sample_Date'])); ?></td>
                                        <td><?= htmlspecialchars($entry['Register_By']); ?></td>
                                        <td><?= date('Y-m-d', strtotime($entry['Registed_Date'])); ?></td>
                                        <td>
                                            <a class="btn btn-primary" href="<?= get_test_link($entry['Test_Type'], $entry['id']); ?>">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" onclick="modaldelete('<?= $entry['id']; ?>', '<?= $entry['table_name']; ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

<div class="modal fade" id="ModalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
                <h5>¿Está seguro?</h5>
            </div>
            <div class="modal-body">
                <form id="deleteForm" method="post">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-outline-danger" name="delete-essay" onclick="Delete()">Sí</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalTriggerButtons = document.querySelectorAll('.open-modal-btn');
        const modalForm = document.querySelector('#disablebackdrop form');

        modalTriggerButtons.forEach(button => button.addEventListener('click', function() {
            const fields = ['sample-name', 'sample-number', 'test-type', 'technician', 'start-date'];
            fields.forEach(field => {
                const element = modalForm.querySelector(`#${field}`);
                if (element) element.value = button.getAttribute(`data-${field}`);
            });
        }));
    });

    let selectedId, selectedTableName;

    function modaldelete(id, tableName) {
        selectedId = id;
        selectedTableName = tableName;
        $('#ModalDelete').modal('show');
    }

    function Delete() {
        if (selectedId && selectedTableName) {
            document.getElementById("deleteForm").action = `essay.php?id=${selectedId}&table=${selectedTableName}`;
            document.getElementById("deleteForm").submit();
        } else {
            console.error('ID o nombre de tabla no seleccionados.');
        }
    }
</script>

<?php
if (isset($_POST['delete-essay'], $_GET['id'], $_GET['table'])) {
    $ID = delete_by_id($_GET['table'], $_GET['id']);
    $msgType = $ID ? "s" : "d";
    $session->msg($msgType, $ID ? "Borrado exitosamente" : "No encontrado");
    redirect('essay.php');
}
include_once('../components/footer.php');

function get_test_link($testType, $id)
{
    $links = [
        'AL' => '../reviews/atterberg-limit.php?id=',
        'BTS' => '../reviews/brazilian.php?id=',
        'GS' => '../reviews/grain-size.php?id=',
        'GS-Fine' => '../reviews/grain-size-fine-agg.php?id=',
        'GS-Coarse' => '../reviews/grain-size-coarse-agg.php?id=',
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
        'AR-FF' => '../reviews/reactivity-fine.php?id='
    ];
    return isset($links[$testType]) ? $links[$testType] . $id : '#';
}
?>