<?php
$user = current_user();

if (isset($_POST['SendMultipleRealization'])) {
    if (isset($_POST['selected_samples']) && is_array($_POST['selected_samples'])) {
        $selected_ids = $_POST['selected_samples'];
        $technicians = $_POST['Technician'];
        $RegistedDate = make_date();
        $Register_By = $user['name'];
        $Register_Date = make_date();
        $Status = "Realization";
        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($selected_ids as $sample_id) {
            $escaped_id = $db->escape($sample_id);
            $tech_name = $db->escape($technicians[$sample_id]);

            // Obtén los datos de la muestra desde test_preparation
            $query = "SELECT Sample_ID, Sample_Number, Test_Type FROM test_preparation WHERE id = '$escaped_id' LIMIT 1";
            $result = $db->fetch_assoc($db->query($query));

            if ($result) {
                $Sname = $db->escape($result['Sample_ID']);
                $Snumber = $db->escape($result['Sample_Number']);
                $Ttype = $db->escape($result['Test_Type']);

                $ExistingRealization = check_R($Sname, $Snumber, $Ttype);

                if (!$ExistingRealization) {
                    $new_id = uuid();
                    $insert_sql = "INSERT INTO test_realization (
                        id,
                        Sample_ID,
                        Sample_Number,
                        Test_Type,
                        Technician,
                        Start_Date,
                        Register_By,
                        Register_Date,
                        Status
                    ) VALUES (
                        '$new_id',
                        '$Sname',
                        '$Snumber',
                        '$Ttype',
                        '$tech_name',
                        '$RegistedDate',
                        '$Register_By',
                        '$Register_Date',
                        '$Status'
                    )";

                    if ($db->query($insert_sql)) {
                        $insertedCount++;
                    }
                } else {
                    $skippedCount++;
                }
            }
        }

        if ($insertedCount > 0) {
            $session->msg("s", "$insertedCount muestra(s) enviadas para su realización.");
        }

        if ($skippedCount > 0) {
            $session->msg("w", "$skippedCount muestra(s) ya existían y no fueron reenviadas.");
        }

        redirect('/pages/test-preparation.php', false);
    } else {
        $session->msg("w", "No seleccionaste ninguna muestra para enviar.");
        redirect('/pages/test-preparation.php', false);
    }
}

function check_R($Sname, $Snumber, $Ttype)
{
    $SeachR = find_all("test_realization");

    foreach ($SeachR as $SeachR) {
        if ($SeachR['Sample_ID'] == $Sname && $SeachR['Sample_Number'] == $Snumber && $SeachR['Test_Type'] == $Ttype) {
            return true;
        }
    }

    return false;
}
