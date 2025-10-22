<?php
$user = current_user();

if (isset($_POST["reviewed_mc_scale"])) {
    $Search = $_GET["id"] ?? '';

    if (!empty($Search)) {
        $SearchEsc = $db->escape($Search);
        $search_data = find_by_sql("SELECT * FROM moisture_scale WHERE id = '{$SearchEsc}' LIMIT 1");

        if ($search_data) {
            $ID           = $search_data[0]["id"];
            $SampleID     = $db->escape($search_data[0]["Sample_ID"]);
            $SampleNumber = $db->escape($search_data[0]["Sample_Number"]);
            $TestType     = $db->escape($search_data[0]["Test_Type"]);
            $RegisBy      = $db->escape($search_data[0]["Register_By"]);

            $existing_record = find_by_sql(
                "SELECT id, Status FROM test_reviewed
                 WHERE Sample_ID='{$SampleID}' AND Sample_Number='{$SampleNumber}'
                   AND Test_Type='{$TestType}' AND Register_By='{$RegisBy}'
                   AND Tracking='{$ID}'
                 LIMIT 1"
            );

            $ReviewedBy   = $db->escape($user["name"]);
            $ReviewedDate = $db->escape(make_date()); // tu helper devuelve 'Y-m-d H:i:s'
            $RegistedDate = $ReviewedDate;            // si quieres conservar tu Start_Date como fecha de registro

            if (!$existing_record) {
                $id = uuid();

                $sql = sprintf(
                    "INSERT INTO test_reviewed (
                        id, Sample_ID, Sample_Number, Start_Date,
                        Reviewed_By, Reviewed_Date, Register_By,
                        Test_Type, Tracking, Status
                    ) VALUES (
                        '%s','%s','%s','%s',
                        '%s','%s','%s',
                        '%s','%s','%s'
                    )",
                    $db->escape($id),
                    $SampleID,
                    $SampleNumber,
                    $RegistedDate,
                    $ReviewedBy,
                    $ReviewedDate,
                    $RegisBy,
                    $TestType,
                    $db->escape($ID),
                    'Reviewed'
                );

                if ($db->query($sql)) {
                    $session->msg("s", "Ensayo marcado como revisado.");
                    redirect("/pages/essay-review.php", false);
                } else {
                    $session->msg("d", "Error al insertar en test_reviewed.");
                    redirect("/reviews/moisture-scale.php?id=".$Search, false);
                }
            } else {
                // Si ya existe, actualiza “Reviewed_By” y “Reviewed_Date” (y estatus)
                $row = $existing_record[0];
                $rowId = $db->escape($row['id']);

                $sqlUp = sprintf(
                    "UPDATE test_reviewed
                     SET Reviewed_By='%s',
                         Reviewed_Date='%s',
                         Status='Reviewed'
                     WHERE id='%s'
                     LIMIT 1",
                    $ReviewedBy,
                    $ReviewedDate,
                    $rowId
                );

                if ($db->query($sqlUp)) {
                    $session->msg("s", "Revisión actualizada.");
                    redirect("/pages/essay-review.php", false);
                } else {
                    $session->msg("d", "Error al actualizar la revisión.");
                    redirect("/reviews/moisture-scale.php?id=".$Search, false);
                }
            }
        } else {
            $session->msg("w", "No se encontró el registro de moisture_scale.");
            redirect("/pages/essay-review.php", false);
        }
    } else {
        $session->msg("w", "ID de búsqueda vacío.");
        redirect("/pages/essay-review.php", false);
    }
}
