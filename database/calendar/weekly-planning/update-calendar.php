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
            redirect('../pages/weekly-planning.php', false);
        } else {
            $session->msg('w', 'No se hicieron cambios');
            redirect('weekly-planning.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/weekly-planning.php', false);
    }
 }
?>