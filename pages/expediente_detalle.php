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
            LL: <b>{$row['Liquid_Limit_Porce']}</b><br>
            PL: <b>{$row['Plastic_Limit_Porce']}</b><br>
            PI: <b>{$row['Plasticity_Index_Porce']}</b><br>
            Clasification: <b>{$row['Classification']}</b>
        ";
    }
    
   if ($testType === "GS_FF") {
  return "
<div style='display:flex; justify-content:space-between; gap:10px;'>

    <div style='flex:1;'>
        No. 4: <b>{$row['Pass12']}%</b><br>
        No. 10: <b>{$row['Pass13']}%</b><br>
        No. 200: <b>{$row['Pass18']}%</b><br>
        D10: <b>{$row['D10']}mm</b><br>
    </div>

    <div style='flex:1;'>
        
        Coarse Than: <b>{$row['Coarser_than_Gravel']}%</b><br>
        Gravel: <b>{$row['Gravel']}%</b><br>
        Sand: <b>{$row['Sand']}%</b><br>
        Fines: <b>{$row['Fines']}%</b><br>
        
       
    </div>

    <div style='flex:1;'>
        Particles Reactive: <b>{$row['Average_Particles_Reactive']}</b><br>
        Reaction Strength: <b>{$row['Reaction_Strength_Result']}</b><br>
    </div>

</div>
";

}
if ($testType === "GS_CF") {
  return "
<div style='display:flex; justify-content:space-between; gap:10px;'>

    <div style='flex:1;'>
        No. 4: <b>{$row['Pass12']}%</b><br>
        No. 10: <b>{$row['Pass13']}%</b><br>
        No. 200: <b>{$row['Pass18']}%</b><br>
        D10: <b>{$row['D10']}mm</b><br>
    </div>

    <div style='flex:1;'>
        
        Coarse Than: <b>{$row['Coarser_than_Gravel']}%</b><br>
        Gravel: <b>{$row['Gravel']}%</b><br>
        Sand: <b>{$row['Sand']}%</b><br>
        Fines: <b>{$row['Fines']}%</b><br>
        
       
    </div>

    <div style='flex:1;'>
        Particles Reactive: <b>{$row['Average_Particles_Reactive']}</b><br>
        Reaction Strength: <b>{$row['Reaction_Strength_Result']}</b><br>
    </div>

</div>
";

}


   if ($testType === "GS") {
    return "
        <div style='display:flex; justify-content:space-between;'>
            
            <!-- Columna izquierda -->
            <div>
                No. 4: <b>{$row['Pass14']}%</b><br>
                No. 10: <b>{$row['Pass15']}%</b><br>
                Gravel: <b>{$row['Gravel']}%</b><br>
                Fines: <b>{$row['Fines']}%</b>
            </div>

            <!-- Columna derecha -->
            <div>
                No. 200: <b>{$row['Pass22']}%</b><br>
                Coarse Than: <b>{$row['Coarser_than_Gravel']}%</b><br>
                Sand: <b>{$row['Sand']}%</b><br>
                D10: <b>{$row['D10']}mm</b>
            </div>

        </div>
    ";
}


    // 3) PROCTOR
    if ($testType === "SP" || $testType === "MP") {
        return "
            MDD: <b>{$row['MDD']}</b> Kg/m³  
            OMC: <b>{$row['OMC']}</b>%
        ";
    }

    // 4) UCS
    if ($testType === "UCS") {
        return "
            qu: <b>{$row['UCS_q']}</b> kPa  
            Deform.: <b>{$row['Strain']}</b>%
        ";
    }

    // 5) HUMEDAD
    if ($testType === "MC") {
        return "
        Moisture Content: <b>{$row['Moisture_Content_Porce']}</b>%
        ";
    }

    // 6) Hidrometro
    if ($testType === "HY") {
        return "
            Clasification: <b>{$row['Classification1']}</b>
        ";
    }

    // 6) SPECIFIC GRAVITY
    if ($testType === "SG") {
        return "
            Gsb: <b>{$row['Bulk_SG']}</b>  
            Gsa: <b>{$row['Saturated_Surface_Dry_SG']}</b>  
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
   TABLAS POR ENSAYO (para códigos específicos)
============================================================ */
$testTables = [
    "AL" => ["table" => "atterberg_limit", "url" => "../reviews/atterberg-limit.php?id="],
    "BTS" => ["table" => "brazilian", "url" => "../reviews/brazilian.php?id="],

    // GRANULOMETRÍA (estos se usan cuando el código ya viene específico)
    "GS"        => ["table" => "grain_size_general", "url" => "../reviews/grain-size.php?id="],
    "GS-COARSE" => ["table" => "grain_size_coarse", "url" => "../reviews/grain-size-coarse-agg.php?id="],
    "GS-CF"     => ["table" => "grain_size_coarse_filter", "url" => "../reviews/grain-size-coarse-filter.php?id="],
    "GS_FF"     => ["table" => "grain_size_fine", "url" => "../reviews/grain-size-fine-filter.php?id="],
    "GS-LPF"    => ["table" => "grain_size_lpf", "url" => "../reviews/grain-size-lpf.php?id="],
    "GS-UTF"    => ["table" => "grain_size_upstream_transition_fill", "url" => "../reviews/grain-size-upstream-transition-fill.php?id="],
    "GS-TRF"    => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-FRF"    => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-BF"     => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-RF"     => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],
    "GS-IRF"    => ["table" => "grain_size_full", "url" => "../reviews/grain-size-full.php?id="],

    // OTROS ENSAYOS
    "HY"        => ["table" => "hydrometer", "url" => "../reviews/hydrometer.php?id="],
    "DHY"       => ["table" => "double_hydrometer", "url" => "../reviews/double-hydrometer.php?id="],
    "LAA"       => ["table" => "los_angeles_abrasion_large", "url" => "../reviews/LAA-Large.php?id="],
    "MC"        => ["table" => "moisture_oven", "url" => "../reviews/moisture-oven.php?id="],
    "SG"        => ["table" => "specific_gravity", "url" => "../reviews/specific-gravity.php?id="],
    "SG-COARSE" => ["table" => "specific_gravity_coarse", "url" => "../reviews/specific-gravity-coarse-aggregates.php?id="],
    "SG-FINE"   => ["table" => "specific_gravity_fine", "url" => "../reviews/specific-gravity-fine-aggregate.php?id="],
    "SP"        => ["table" => "standard_proctor", "url" => "../reviews/standard-proctor.php?id="],
    "UCS"       => ["table" => "unixial_compressive", "url" => "../reviews/unixial-compressive.php?id="],
    "PLT"       => ["table" => "point_load", "url" => "../reviews/point-load.php?id="],
    "SND"       => ["table" => "soundness", "url" => "../reviews/soundness.php?id="],
    "PH"        => ["table" => "pinhole_test", "url" => "../reviews/pinhole-test.php?id="],
    "AR"        => ["table" => "reactivity", "url" => "../reviews/reactivity.php?id="],
];

/* ============================================================
   BUSCAR RESULTADOS DE CADA ENSAYO
============================================================ */
$testCards = [];

foreach ($requestedTests as $reqCode) {

    // CASO ESPECIAL: TODOS LOS GS EN LA REQUISICIÓN VIENEN COMO "GS"
    if ($reqCode === "GS") {

        // Lista de posibles tablas GS donde puede estar el resultado
        $gsCandidates = [
            [ "code" => "GS",      "table" => "grain_size_general",               "url" => "../reviews/grain-size.php?id=" ],
            [ "code" => "GS_FF",   "table" => "grain_size_fine",                  "url" => "../reviews/grain-size-fine-filter.php?id=" ],
            [ "code" => "GS-COARSE","table" => "grain_size_coarse",               "url" => "../reviews/grain-size-coarse-agg.php?id=" ],
            [ "code" => "GS-CF",   "table" => "grain_size_coarse_filter",         "url" => "../reviews/grain-size-coarse-filter.php?id=" ],
            [ "code" => "GS-LPF",  "table" => "grain_size_lpf",                   "url" => "../reviews/grain-size-lpf.php?id=" ],
            [ "code" => "GS-UTF",  "table" => "grain_size_upstream_transition_fill","url" => "../reviews/grain-size-upstream-transition-fill.php?id=" ],
            [ "code" => "GS-TRF",  "table" => "grain_size_full",                  "url" => "../reviews/grain-size-full.php?id=" ],
        ];

        $found = null;

        foreach ($gsCandidates as $c) {
            $row = find_by_sql("
                SELECT id, Registed_Date, Register_By
                FROM {$c['table']}
                WHERE Sample_ID = '{$sampleID}'
                  AND Sample_Number = '{$sampleNum}'
                LIMIT 1
            ");

            if ($row) {
                $r = $row[0];
                $found = [
                    "display" => "GS",         // Lo que se ve en la tarjeta
                    "code"    => $c["code"],  // Código interno para resumen_clave
                    "status"  => "completed",
                    "id"      => $r["id"],
                    "date"    => $r["Registed_Date"],
                    "tech"    => $r["Register_By"],
                    "table"   => $c["table"],
                    "url"     => $c["url"] . $r["id"],
                ];
                break;
            }
        }

        if ($found) {
            $testCards[] = $found;
        } else {
            // No se encontró en ninguna tabla GS → pendiente
            $testCards[] = [
                "display" => "GS",
                "code"    => "GS",
                "status"  => "pending",
                "test"    => "GS",
            ];
        }

        continue; // Pasamos al siguiente ensayo solicitado
    }

    // RESTO DE ENSAYOS (NO GS GENÉRICO)
    if (!isset($testTables[$reqCode])) {
        $testCards[] = [
            "display" => $reqCode,
            "code"    => $reqCode,
            "status"  => "unknown",
            "msg"     => "No existe tabla"
        ];
        continue;
    }

    $table = $testTables[$reqCode]["table"];

    // Verificar columnas de la tabla
    $cols  = find_by_sql("SHOW COLUMNS FROM {$table}");
    $names = array_column($cols, "Field");

    if (!in_array("Sample_ID", $names)) {
        $testCards[] = [
            "display" => $reqCode,
            "code"    => $reqCode,
            "status"  => "error",
            "msg"     => "Tabla sin Sample_ID"
        ];
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
            "display" => $reqCode,                     // lo que vino en la requisición
            "code"    => $reqCode,                     // mismo código para resumen_clave
            "status"  => "completed",
            "id"      => $r["id"],
            "date"    => $r["Registed_Date"],
            "tech"    => $r["Register_By"],
            "table"   => $table,
            "url"     => $testTables[$reqCode]["url"] . $r["id"]
        ];
    } else {
        $testCards[] = [
            "display" => $reqCode,
            "code"    => $reqCode,
            "status"  => "pending",
        ];
    }
}

?>

<!-- ============================================================
     HTML
============================================================ -->

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Expediente Técnico - <?= $sampleID ?></h1>
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

    <a href="../pdf/expediente_pdf.php?sample=<?= $sampleID ?>&num=<?= $sampleNum ?>" 
       target="_blank"
       class="btn btn-danger btn-lg mb-3">
       <i class="bi bi-filetype-pdf"></i> Generar Expediente PDF
    </a>

    <h4>Ensayos Solicitados</h4>

    <div class="row">

        <?php foreach ($testCards as $t): ?>
        <div class="col-md-4">

            <div class="card shadow-sm mb-3"
                style="border-left:5px solid
                    <?= $t["status"]=="completed" ? '#28a745' 
                    : ($t["status"]=="pending" ? '#dc3545' : '#6c757d') ?>">

                <div class="card-body">
                    <h5 class="card-title"><?= $t["display"] ?? ($t["test"] ?? '') ?></h5>

                    <?php if ($t["status"] == "completed"): ?>

                        <?php
                            $tableForRow = $t["table"];
                            $data = find_by_sql("
                                SELECT *
                                FROM {$tableForRow}
                                WHERE id = '{$t['id']}'
                                LIMIT 1
                            ");
                            $dataRow = $data ? $data[0] : [];
                            $code    = $t["code"] ?? $t["display"];
                            $resumen = $dataRow ? resumen_clave($code, $dataRow) : "<span class='text-muted'>Sin datos</span>";
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

                        <p class="text-muted"><?= $t["msg"] ?? 'Sin información' ?></p>

                    <?php endif; ?>

                </div>
            </div>

        </div>
        <?php endforeach; ?>

    </div>
</main>

<?php include_once('../components/footer.php'); ?>