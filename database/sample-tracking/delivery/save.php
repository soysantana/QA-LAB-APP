<?php
 $user = current_user();
 if (isset($_POST['send-delivery'])) {
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
        $Status = "Delivery";
        $ExistingDelivery = check_D($Sname, $Snumber, $Ttype);

        if (!$ExistingDelivery) {
            $sql = "INSERT INTO test_delivery (
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
                $session->msg('s', "Muestra enviada para su entrega.");
                redirect('/pages/test-delivery.php', false);
            } else {
                $session->msg('d', 'Lo sentimos, no se pudo agregar la muestra enviada para entrega.');
                redirect('/pages/test-realization.php', false);
            }
        } else {
            $session->msg('w', 'Lo sentimos, la muestra existe.');
            redirect('/pages/test-realization.php', false);
        }
    } else {
        $session->msg("w", $errors);
        redirect('/pages/test-realization.php', false);
    }
 }

 function check_D($Sname, $Snumber, $Ttype)
  {
    $SeachD = find_all("test_delivery");
    
    foreach ($SeachD as $SeachD) {
        if ($SeachD['Sample_Name'] == $Sname && $SeachD['Sample_Number'] == $Snumber && $SeachD['Test_Type'] == $Ttype) {
            return true;
        }
    }
    
    return false;
 }
?>