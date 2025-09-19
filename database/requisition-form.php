<!-- Delete Requisiton -->
<?php
page_require_level(2);
if (isset($_POST['delete-requisition'])) {
    $Search = $_GET['id'];

    // Asume que tienes una función delete_by_id definida que elimina registros de la tabla 'calendar'
    $ID = delete_by_id('lab_test_requisition_form', $Search);

    if ($ID) {
        $session->msg("s", "Borrado exitosamente");
    } else {
        $session->msg("d", "No encontrado");
    }

    // Redirige a la página de planificación semanal después de la operación
    redirect('/pages/requisition-form-view.php');
}
?>