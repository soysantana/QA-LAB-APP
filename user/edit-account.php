<?php
  require_once('../config/load.php');
 //update user other info
  if(isset($_POST['Edit-Account'])){
    $req_fields = array('fullName','username', 'company', 'job', 'country', 'phone', 'email' );
    validate_fields($req_fields);
    if(empty($errors)){
             $id = $_SESSION['user_id'];
           $name = remove_junk($db->escape($_POST['fullName']));
           $username = remove_junk($db->escape($_POST['username']));
           $company = remove_junk($db->escape($_POST['company']));
           $job = remove_junk($db->escape($_POST['job']));
           $country = remove_junk($db->escape($_POST['country']));
           $phone = remove_junk($db->escape($_POST['phone']));
           $email = remove_junk($db->escape($_POST['email']));
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}',
            company ='{$company}', job ='{$job}', country ='{$country}', phone ='{$phone}',
            email ='{$email}' WHERE id='{$id}'";
    $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s'," Hola {$name} Tu cuenta ha sido actualizada. ");
            redirect('../pages/users-profile.php', false);
          } else {
            $session->msg('d'," Lo siento {$name} tu actualización falló.");
            redirect('../pages/users-profile.php', false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('../pages/users-profile.php',false);
    }
  }
?>

<?php
if (isset($_POST['upload'])) {
    $req_fields = array();
    validate_fields($req_fields);

    if (empty($errors)) {
        $id = $_SESSION['user_id'];
        // Manejo de la carga de la imagen
        if ($_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
            $imagen_tmp = $_FILES['file_upload']['tmp_name']; // Usar 'tmp_name' en lugar de 'file_upload'
            $imagen_data = file_get_contents($imagen_tmp); // Lee el contenido del archivo
            $imagen_data = $db->escape($imagen_data); // Escapa los datos para evitar SQL Injection
        } else {
            $session->msg('w', 'Inserte una imagen.');
            redirect('../pages/users-profile.php', false);
        }

        // Actualización de la base de datos
        $sql = "UPDATE users SET image = '{$imagen_data}' WHERE id='{$id}'";
        // Ejecutar la consulta SQL aquí (falta esta parte en tu código)

        // Verificar si la actualización se realizó con éxito y mostrar un mensaje apropiado
        if ($db->query($sql)) {
            $session->msg('s', 'Imagen actualizada correctamente.');
            redirect('../pages/users-profile.php', false);
        } else {
            $session->msg('d', 'Error al actualizar la imagen.');
            redirect('../pages/users-profile.php', false);
        }
    }
}
?>

<?php
if (isset($_POST['delete'])) {
    $id = $_SESSION['user_id'];

    // Consulta para obtener la imagen actual del usuario
    $sql = "SELECT image FROM users WHERE id = '{$id}'";
    $result = $db->query($sql);

    if ($result) {
        $user = $db->fetch_assoc($result);
        $currentImage = $user['image'];

        // Verificar si el usuario ya tiene una imagen antes de eliminarla
        if (!empty($currentImage)) {
            // Eliminar la imagen de la base de datos
            $sql = "UPDATE users SET image = NULL WHERE id='{$id}'";
            if ($db->query($sql)) {
                $session->msg('s', 'Imagen eliminada correctamente.');
            } else {
                $session->msg('d', 'Error al eliminar la imagen.');
            }
        } else {
            $session->msg('d', 'El usuario no tiene una imagen para eliminar.');
        }
    } else {
        $session->msg('d', 'Error al obtener la imagen del usuario.');
    }

    redirect('../pages/users-profile.php', false);
}
?>
