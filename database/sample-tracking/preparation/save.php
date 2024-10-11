<?php
 $user = current_user();

 if (isset($_POST['test-preparation'])) {
    $req_fields = array(
        'Sname',
        'Ttype',
        'Technician'
    );

    validate_fields($req_fields);

    if (empty($errors)) {
        $id = uuid();
        $Sname = $db->escape($_POST['Sname']);
        $Snumber = $db->escape($_POST['Snumber']);
        $Ttype = $db->escape($_POST['Ttype']);
        $Technician = $db->escape($_POST['Technician']);
        $RegistedDate = make_date();
        $Register_By = $user['name'];
        $Register_Date = make_date();
        $Status = "Preparation";
        $existingP = check_p($Sname, $Snumber, $Ttype);

        if (!$existingP) {
            $sql = "INSERT INTO test_preparation (
                id,
                Sample_Name,
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
                $session->msg("s", "Muestra enviada para preparación.");
                redirect('/pages/test-preparation.php', false);
            } else {
                $session->msg("d", "Lo sentimos, no se pudo agregar la muestra enviada para preparación.");
                redirect('/pages/test-preparation.php', false);
            }
        } else {
            $session->msg("w", "Lo sentimos, la muestra existe.");
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
        if ($SeachP['Sample_Name'] == $Sname && $SeachP['Sample_Number'] == $Snumber && $SeachP['Test_Type'] == $Ttype) {
            return true;
        }
    }
    
    return false;
 }
?>