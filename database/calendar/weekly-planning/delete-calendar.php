<!-- Delete Requisiton -->
<?php 
if (isset($_POST['delete-calendar'])) { 
    $Search = $_POST['event-id'];

    // Asume que tienes una función delete_by_id definida que elimina registros de la tabla 'calendar'
    $ID = delete_by_id('calendar', $Search);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    // Redirige a la página de planificación semanal después de la operación
    redirect('../pages/weekly-planning.php');
}


?>