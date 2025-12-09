<?php
$page_title = "Expediente Técnico - Detalle de Muestra";
require_once "../config/load.php";
page_require_level(2);
include_once('../components/header.php');

/* ============================================================
   FUNCIÓN: Resumen clave por ensayo
============================================================ */
function resumen_clave($testType, $row) {

    // 1) ATTERBERG LIMIT
    if ($testType === "AL") {
        return "
            LL: <b>{$row['Liquid_Limit_Porce']}</b> • 
            PL: <b>{$row['Plastic_Limit_Porce']}</b> • 
            PI: <b>{$row['Plasticity_Index_Porce']}</b>
        ";
    }

    // 2) GRANULOMETRÍA GENERAL (ejemplo, ajustar según columnas reales)
if ($testType === "GS") {
    return "
        <div>
            No. 4: <b>{$row['Pass14']}%</b><br>
            No. 10: <b>{$row['Pass15']}%</b><br>
            No. 200: <b>{$row['Pass22']}%</b><br>
            % Coarse Than: <b>{$row['Coarser_than_Gravel']}%</b><br>
            % Gravel: <b>{$row['Gravel']}%</b><br>
            % Sand: <b>{$row['Sand']}%</b><br>
            % Fines: <b>{$row['Fines']}%</b>
        </div>
    ";
}

    // 3) PROCTOR
    if ($testType === "SP" || $testType === "MP") {
        return "
            MDD: <b>{$row['MDD']}</b> g/cm³ • 
            OMC: <b>{$row['OMC']}</b>%
        ";
    }

    // 4) UCS
    if ($testType === "UCS") {
        return "
            qu: <b>{$row['UCS_q']}</b> kPa • 
            Deform.: <b>{$row['Strain']}</b>%
        ";
    }

    // 5) HUMEDAD
    if ($testType === "MC") {
        return "
            Humedad: <b>{$row['Moisture_Content']}</b>%
        ";
    }

    // 6) SPECIFIC GRAVITY
    if ($testType === "SG") {
        return "
            Gsb: <b>{$row['Bulk_SG']}</b> • 
            Gsa: <b>{$row['Saturated_Surface_Dry_SG']}</b> • 
            Apparent: <b>{$row['Apparent_SG']}</b>
        ";
    }

    return "<span class='text-muted'>Sin resumen disponible</span>";
}

/* ============================================================
   DATOS GENERALES DE LA MUESTRA
============================================================ */
$sampleID   = $db->escape($_GET["sample"]);
$sampleNum  = $db->escape($_GET["num"]);

$info = find_by_sql("
    SELECT *
    FROM lab_test_requisition_form
    WHERE Sample_ID = '{$sampleID}'
      AND Sample_Number = '{$sampleNum}'
    LIMIT 1
");

if (!$info) {
    echo "<div class='alert alert-danger'>Muestra no encontrada</div>";
    include_once('../components/footer.php');
    exit;
}

$info = $info[0];
$requestedTests = array_map('trim', explode(",", $info["Test_Type"]));

/* ============================================================
   TABLAS POR ENSAYO
============================================================ */
$testTables = [
    "AL" => ["table" => "atterberg_limit", "url" => "../reviews/atterberg-limit.php?id="],
    "BTS" => ["table" => "brazilian", "url" => "../reviews/brazilian.php?id="],

    // GRANULOMETRÍA
    "GS"        => ["table" => "grain_size_general", "url" => "../reviews/grain-size-general.php?id="],
    "GS-FINE"   => ["table" => "grain_size_fine", "url" => "../reviews/grain-size-fine-agg.php?id="],
    "GS-COARSE" => ["table" => "grain_size_coarse", "url" => "../reviews/grain-size-coarse-agg.php?id="],
    "GS-CF"     => ["table" => "grain_size_coarse_filter", "url" => "../reviews/grain-size-coarse-filter.php?id="],
    "GS-FF"     => ["table" => "grain_size_fine_filter", "url" => "../reviews/grain-size-fine-filter.php?id="],
    "GS-LPF"    => ["table" => "grain_size_lpf", "url" => "../reviews/grain-size-lpf.php?id="],
    "GS-UTF"    => ["table" => "grain_size_upstream_transition_fill", "url" => "../reviews/grain-size-upstream-transition-fill.php?id="],
    "GS-TRF"    => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-FRF"    => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-BF"     => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-RF"     => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-IRF"    => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],

    // OTROS ENSAYOS
    "HY" => ["table" => "hydrometer", "url" => "../reviews/hydrometer.php?id="],
    "DHY" => ["table" => "double_hydrometer", "url" => "../reviews/double-hydrometer.php?id="],
    "LAA" => ["table" => "los_angeles_abrasion_large", "url" => "../reviews/LAA-Large.php?id="],
    "MC" => ["table" => "moisture_oven", "url" => "../reviews/moisture-oven.php?id="],
    "SG" => ["table" => "specific_gravity", "url" => "../reviews/specific-gravity.php?id="],
    "SG-COARSE" => ["table" => "specific_gravity_coarse", "url" => "../reviews/specific-gravity-coarse-aggregates.php?id="],
    "SG-FINE" => ["table" => "specific_gravity_fine", "url" => "../reviews/specific-gravity-fine-aggregate.php?id="],
    "SP" => ["table" => "standard_proctor", "url" => "../reviews/standard-proctor.php?id="],
    "UCS" => ["table" => "unixial_compressive", "url" => "../reviews/unixial-compressive.php?id="],
    "PLT" => ["table" => "point_load", "url" => "../reviews/point-load.php?id="],
    "SND" => ["table" => "soundness", "url" => "../reviews/soundness.php?id="],
    "PH" => ["table" => "pinhole_test", "url" => "../reviews/pinhole-test.php?id="],
    "AR" => ["table" => "reactivity", "url" => "../reviews/reactivity.php?id="],
];

/* ============================================================
   BUSCAR RESULTADOS DE CADA ENSAYO
============================================================ */
$testCards = [];

foreach ($requestedTests as $t) {

    if (!isset($testTables[$t])) {
        $testCards[] = ["test"=>$t, "status"=>"unknown", "msg"=>"No existe tabla"];
        continue;
    }

    $table = $testTables[$t]["table"];

    // Verificar columnas de la tabla
    $cols = find_by_sql("SHOW COLUMNS FROM {$table}");
    $names = array_column($cols, "Field");

    if (!in_array("Sample_ID", $names)) {
        $testCards[] = ["test"=>$t, "status"=>"error", "msg"=>"Tabla sin Sample_ID"];
        continue;
    }

    // Buscar resultado
    $row = find_by_sql("
        SELECT id, Registed_Date, Register_By
        FROM {$table}
        WHERE Sample_ID = '{$sampleID}'
          AND Sample_Number = '{$sampleNum}'
        LIMIT 1
    ");

    if ($row) {
        $r = $row[0];

        $testCards[] = [
            "test"   => $t,
            "status" => "completed",
            "id"     => $r["id"],
            "date"   => $r["Registed_Date"],
            "tech"   => $r["Register_By"],
            "url"    => $testTables[$t]["url"] . $r["id"]
        ];
    } else {
        $testCards[] = ["test"=>$t, "status"=>"pending"];
    }
}

?>

<!-- ============================================================
     HTML
============================================================ -->

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Expediente Técnico – <?= $sampleID ?></h1>
    </div>

    <!-- Información de la muestra -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Información de la Muestra</h4>

            <p><b>Sample ID:</b> <?= $info["Sample_ID"] ?></p>
            <p><b>Sample Number:</b> <?= $info["Sample_Number"] ?></p>
            <p><b>Cliente:</b> <?= $info["Client"] ?></p>
            <p><b>Material:</b> <?= $info["Material_Type"] ?></p>
            <p><b>Estructura:</b> <?= $info["Structure"] ?></p>
            <p><b>Fecha Registro:</b> <?= $info["Registed_Date"] ?></p>
        </div>
    </div>

    <h4>Ensayos Solicitados</h4>

    <div class="row">

        <?php foreach ($testCards as $t): ?>
        <div class="col-md-4">

            <div class="card shadow-sm mb-3"
                style="border-left:5px solid
                    <?= $t["status"]=="completed" ? '#28a745' 
                    : ($t["status"]=="pending" ? '#dc3545' : '#6c757d') ?>">

                <div class="card-body">
                    <h5 class="card-title"><?= $t["test"] ?></h5>

                    <?php if ($t["status"] == "completed"): ?>

                        <?php
                            $dataRow = find_by_sql("
                                SELECT *
                                FROM {$testTables[$t['test']]['table']}
                                WHERE id = '{$t['id']}'
                                LIMIT 1
                            ")[0];

                            $resumen = resumen_clave($t["test"], $dataRow);
                        ?>

                        <p><b>Estado:</b> <span class="text-success">Completado ✔</span></p>
                        <p><b>Fecha:</b> <?= $t["date"] ?></p>
                        <p><b>Técnico:</b> <?= $t["tech"] ?></p>

                        <div class="alert alert-primary p-2 mb-2">
                            <?= $resumen ?>
                        </div>

                        <a href="<?= $t["url"] ?>" class="btn btn-primary btn-sm">Ver Ensayo</a>

                    <?php elseif ($t["status"] == "pending"): ?>

                        <p><b>Estado:</b> <span class="text-danger">Pendiente ✖</span></p>

                    <?php else: ?>

                        <p class="text-muted"><?= $t["msg"] ?></p>

                    <?php endif; ?>

                </div>
            </div>

        </div>
        <?php endforeach; ?>

    </div>
</main>

<?php include_once('../components/footer.php'); ?>
