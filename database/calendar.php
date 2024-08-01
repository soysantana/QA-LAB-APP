<!-- Save Calendar -->
<?php
 require_once('../config/load.php');
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

        $sql = "INSERT INTO calendar (
            id,
            Technician,
            Activity,
            Color,
            Start_Date,
            End_Date
            )
        VALUES (
            '$id',
            '$Tec',
            '$Act',
            '$ColPic',
            '$FecIni',
            '$FecFin'
        )";

        if ($db->query($sql)) {
            $session->msg('s', "actividad guardada");
            redirect('../pages/weekly-planning.php', false);
        } else {
            $session->msg('d', 'Lo sentimos, la actividad no se pudo guardar.');
            redirect('../pages/weekly-planning.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/weekly-planning.php', false);
    }
 }
?>

<!-- Update Calendar -->
<?php
 $Search = $_POST['event-id'];
 if (isset($_POST['update-calendar'])) {
    $req_fields = array(
        'Tecnico',
        'Actividad',
        'Inicio',
        'Final'
    );
    validate_fields($req_fields);

    if (empty($errors)) {
        $Tec = $db->escape($_POST['Tecnico']);
        $Act = $db->escape($_POST['Actividad']);
        $FecIni = $db->escape($_POST['Inicio']);
        $FecFin = $db->escape($_POST['Final']);
        $ColPic = $db->escape($_POST['Color']);

        $query = "UPDATE calendar SET ";
        $query .= "Technician = '{$Tec}',";
        $query .= "Activity = '{$Act}', ";
        $query .= "Start_Date = '{$FecIni}', ";
        $query .= "End_Date = '{$FecFin}', ";
        $query .= "Color = '{$ColPic}'";
        $query .= "WHERE id = '{$Search}'";

        $result = $db->query($query);

        if ($result && $db->affected_rows() === 1) {
            $session->msg('s', 'El calendario ha sido actualizado.');
            redirect('../pages/weekly-planning.php?id=' . $Search, false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('../pages/weekly-planning.php?id=' . $Search, false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/weekly-planning.php?id=' . $Search, false);
    }
 }
?>

<!-- Delete Requisiton -->
<?php
 $Search = $_POST['event-id'];
 
 $ID = delete_by_id('calendar', $Search);

 if ($ID) {
    $session->msg("s", "Borrado exitosamente");
 } else {
    $session->msg("d", "No encontrado");
 }
 redirect('../pages/weekly-planning.php');
?>