<?php
$user = current_user();

if (isset($_POST['test-preparation'])) {
    $req_fields = array(
        'Sname',
        'Snumber',
        'Technician'
    );

    validate_fields($req_fields);

    if (empty($errors)) {
        $Sname = $db->escape($_POST['Sname']);
        $Snumber = $db->escape($_POST['Snumber']);
        $Technician = $db->escape($_POST['Technician']);
        $RegistedDate = make_date();
        $Register_By = $user['name'];
        $Register_Date = make_date();
        $Status = "Preparation";

        if (isset($_POST['Ttype']) && is_array($_POST['Ttype'])) {
            $insertedCount = 0;

            foreach ($_POST['Ttype'] as $rawTtype) {
                $Ttype = $db->escape($rawTtype);
                $existingP = check_p($Sname, $Snumber, $Ttype);

                if (!$existingP) {
                    $id = uuid();

                    $sql = "INSERT INTO test_preparation (
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
                        '$id',
                        '$Sname',
                        '$Snumber',
                        '$Ttype',
                        '$Technician',
                        '$RegistedDate',
                        '$Register_By',
                        '$Register_Date',
                        '$Status'
                    )";

                    if ($db->query($sql)) {
                        $insertedCount++;
                    }
                }
            }

            if ($insertedCount > 0) {
                $session->msg("s", "$insertedCount pruebas enviadas para preparación.");
            } else {
                $session->msg("w", "Las muestras ya existen.");
            }

            redirect('/pages/test-preparation.php', false);
        }
    } else {
        $session->msg("w", $errors);
        redirect('/pages/test-preparation.php', false);
    }
}


function check_p($Sname, $Snumber, $Ttype)
{
    $SeachP = find_all("test_preparation");

    foreach ($SeachP as $SeachP) {
        if ($SeachP['Sample_ID'] == $Sname && $SeachP['Sample_Number'] == $Snumber && $SeachP['Test_Type'] == $Ttype) {
            return true;
        }
    }

    return false;
}
