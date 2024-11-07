<!-- Save Calendar -->
<?php
 page_require_level(1);
 if (isset($_POST['save-calendar'])) {
    $req_fields = array(
        'Tec',
        'Act',
        'FecIni',
        'FecFin'
    );

    validate_fields($req_fields);

    if (empty($errors)) {
        $id = uuid();
        $Tec = $db->escape($_POST['Tec']);
        $Act = $db->escape($_POST['Act']);
        $FecIni = $db->escape($_POST['FecIni']);
        $FecFin = $db->escape($_POST['FecFin']);
        $ColPic = $db->escape($_POST['ColPic']);
        $Rotation = 'Rotation';

        $sql = "INSERT INTO calendar (
            id,
            Technician,
            Activity,
            Color,
            Start_Date,
            End_Date,
            Rotation
            )
        VALUES (
            '$id',
            '$Tec',
            '$Act',
            '$ColPic',
            '$FecIni',
            '$FecFin',
            '$Rotation'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "rotacion guardada");
            redirect('/pages/job-rotation.php', false);
        } else {
            $session->msg('w', 'Lo sentimos, la rotacion no se pudo guardar.');
            redirect('/pages/job-rotation.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('/pages/job-rotation.php', false);
    }
 }
?>