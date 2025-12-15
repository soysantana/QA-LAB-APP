<?php
$user = current_user();

function colExists($table, $col){
    // OJO: find_by_sql devuelve array
    $r = find_by_sql("SHOW COLUMNS FROM {$table} LIKE '".addslashes($col)."'");
    return is_array($r) && count($r) > 0;
}

if (isset($_POST["reviewed_mc_scale"])) {

    $Search = $_GET["id"] ?? '';

    if (empty($Search)) {
        $session->msg("w", "ID de búsqueda vacío.");
        redirect("/pages/essay-review.php", false);
        exit;
    }

    $SearchEsc = $db->escape($Search);

    $search_data = find_by_sql("SELECT * FROM moisture_scale WHERE id = '{$SearchEsc}' LIMIT 1");
    if (!$search_data) {
        $session->msg("w", "No se encontró el registro de moisture_scale.");
        redirect("/pages/essay-review.php", false);
        exit;
    }

    $rowMS = $search_data[0];

    $TrackingID    = $rowMS["id"]; // GUID tracking
    $SampleID      = $db->escape($rowMS["Sample_ID"] ?? '');
    $SampleNumber  = $db->escape($rowMS["Sample_Number"] ?? '');
    $TestType      = $db->escape($rowMS["Test_Type"] ?? '');
    $RegisBy       = $db->escape($rowMS["Register_By"] ?? '');

    if ($SampleID === '' || $TestType === '') {
        $session->msg("d", "Faltan datos clave (Sample_ID o Test_Type) en moisture_scale.");
        redirect("/reviews/moisture-scale.php?id=".$Search, false);
        exit;
    }

    // Buscar si ya existe el registro en test_reviewed
    $whereParts = [];
    $whereParts[] = "Sample_ID='{$SampleID}'";
    $whereParts[] = "Sample_Number='{$SampleNumber}'";
    $whereParts[] = "Test_Type='{$TestType}'";

    if (colExists('test_reviewed','Register_By') && $RegisBy !== '') {
        $whereParts[] = "Register_By='{$RegisBy}'";
    }
    if (colExists('test_reviewed','Tracking')) {
        $whereParts[] = "Tracking='".$db->escape($TrackingID)."'";
    }

    $existing_record = find_by_sql("
        SELECT id
        FROM test_reviewed
        WHERE ".implode(" AND ", $whereParts)."
        LIMIT 1
    ");

    $ReviewedBy   = $db->escape($user["name"] ?? 'N/A');
    $ReviewedDate = $db->escape(make_date()); // 'Y-m-d H:i:s'

    // Detectar nombre de columna de fecha de registro
    $dateCol = null;
    if (colExists('test_reviewed','Start_Date'))   $dateCol = 'Start_Date';
    elseif (colExists('test_reviewed','Register_Date')) $dateCol = 'Register_Date';
    elseif (colExists('test_reviewed','Date'))     $dateCol = 'Date';

    // Detectar columnas de "Reviewed"
    $hasReviewedBy   = colExists('test_reviewed','Reviewed_By');
    $hasReviewedDate = colExists('test_reviewed','Reviewed_Date');

    // Fallback típico si tu tabla usa Technician
    $hasTechnician = colExists('test_reviewed','Technician');

    // Status existe?
    $hasStatus = colExists('test_reviewed','Status');

    // Tracking existe?
    $hasTracking = colExists('test_reviewed','Tracking');

    if (!$existing_record) {

        $newId = $db->escape(uuid());

        // Armamos columnas/valores dinámicamente según existan
        $cols = ["id","Sample_ID","Sample_Number","Test_Type"];
        $vals = ["'{$newId}'","'{$SampleID}'","'{$SampleNumber}'","'{$TestType}'"];

        if ($dateCol) {
            $cols[] = $dateCol;
            $vals[] = "'{$ReviewedDate}'";
        }

        if (colExists('test_reviewed','Register_By') && $RegisBy !== '') {
            $cols[] = "Register_By";
            $vals[] = "'{$RegisBy}'";
        }

        if ($hasTracking) {
            $cols[] = "Tracking";
            $vals[] = "'".$db->escape($TrackingID)."'";
        }

        if ($hasReviewedBy) {
            $cols[] = "Reviewed_By";
            $vals[] = "'{$ReviewedBy}'";
        } elseif ($hasTechnician) {
            // fallback si tu tabla NO tiene Reviewed_By
            $cols[] = "Technician";
            $vals[] = "'{$ReviewedBy}'";
        }

        if ($hasReviewedDate) {
            $cols[] = "Reviewed_Date";
            $vals[] = "'{$ReviewedDate}'";
        }

        if ($hasStatus) {
            $cols[] = "Status";
            $vals[] = "'Reviewed'";
        }

        $sql = "INSERT INTO test_reviewed (".implode(",",$cols).") VALUES (".implode(",",$vals).")";

        if ($db->query($sql)) {
            $session->msg("s", "Ensayo marcado como revisado.");
            redirect("/pages/essay-review.php", false);
            exit;
        } else {
            // Muestra SQL para depurar (como haces tú)
            echo "Error en esta consulta :<pre>{$sql}</pre>";
            exit;
        }

    } else {

        $rowId = $db->escape($existing_record[0]['id']);

        $set = [];

        if ($hasReviewedBy) {
            $set[] = "Reviewed_By='{$ReviewedBy}'";
        } elseif ($hasTechnician) {
            $set[] = "Technician='{$ReviewedBy}'";
        }

        if ($hasReviewedDate) {
            $set[] = "Reviewed_Date='{$ReviewedDate}'";
        }

        if ($hasStatus) {
            $set[] = "Status='Reviewed'";
        }

        // nada que actualizar?
        if (empty($set)) {
            $session->msg("w", "La tabla test_reviewed no tiene columnas para actualizar (Reviewed_By/Technician/Reviewed_Date/Status).");
            redirect("/pages/essay-review.php", false);
            exit;
        }

        $sqlUp = "
            UPDATE test_reviewed
            SET ".implode(", ", $set)."
            WHERE id='{$rowId}'
            LIMIT 1
        ";

        if ($db->query($sqlUp)) {
            $session->msg("s", "Revisión actualizada.");
            redirect("/pages/essay-review.php", false);
            exit;
        } else {
            echo "Error en esta consulta :<pre>{$sqlUp}</pre>";
            exit;
        }
    }
}
