<?php
 $user = current_user();

 if (isset($_POST['send-realization'])) {
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
        $Status = "Realization";
        $ExistingRealization = check_R($Sname, $Snumber, $Ttype);

        if (!$ExistingRealization) {
            $sql = "INSERT INTO test_realization (
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
                $session->msg('s', "Muestra enviada para su realización.");
                redirect('/pages/test-realization.php', false);
            } else {
                $session->msg('d', 'Lo sentimos, no se ha podido añadir la Muestra enviada para su realización.');
                redirect('/pages/test-preparation.php', false);
            }
        } else {
            $session->msg('w', 'Lo sentimos, la muestra existe.');
            redirect('/pages/test-preparation.php', false);
        }
    } else {
        $session->msg("w", $errors);
        redirect('/pages/test-preparation.php', false);
    }
 }

 function check_R($Sname, $Snumber, $Ttype)
  {
    $SeachR = find_all("test_realization");
    
    foreach ($SeachR as $SeachR) {
        if ($SeachR['Sample_Name'] == $Sname && $SeachR['Sample_Number'] == $Snumber && $SeachR['Test_Type'] == $Ttype) {
            return true;
        }
    }
    
    return false;
 }
?>
