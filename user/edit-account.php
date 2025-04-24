<?php
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
            $imagen_tmp = $_FILES['file_upload']['tmp_name'];
            $tipo_imagen = exif_imagetype($imagen_tmp);

            // Cargar la imagen según su tipo
            switch ($tipo_imagen) {
                case IMAGETYPE_JPEG:
                    $imagen = imagecreatefromjpeg($imagen_tmp);
                    break;
                case IMAGETYPE_PNG:
                    $imagen = imagecreatefrompng($imagen_tmp);
                    break;
                case IMAGETYPE_GIF:
                    $imagen = imagecreatefromgif($imagen_tmp);
                    break;
                case IMAGETYPE_BMP:
                    $imagen = imagecreatefrombmp($imagen_tmp);
                    break;
                case IMAGETYPE_WEBP:
                    $imagen = imagecreatefromwebp($imagen_tmp);
                    break;
                default:
                    $session->msg('w', 'Formato de imagen no soportado.');
                    redirect('../pages/users-profile.php', false);
            }

            // Establecer el nuevo tamaño y la calidad
            $nuevo_ancho = 120; // Ajusta el ancho deseado
            $nuevo_alto = 120;  // Ajusta la altura deseada

            // Crear una nueva imagen con las dimensiones especificadas
            $imagen_comprimida = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);

            // Manejar la transparencia si es necesario
            if ($tipo_imagen == IMAGETYPE_PNG || $tipo_imagen == IMAGETYPE_GIF) {
                imagealphablending($imagen_comprimida, false);
                $color_transparente = imagecolorallocatealpha($imagen_comprimida, 255, 255, 255, 127);
                imagefilledrectangle($imagen_comprimida, 0, 0, $nuevo_ancho, $nuevo_alto, $color_transparente);
                imagealphablending($imagen_comprimida, true);
                imagesavealpha($imagen_comprimida, true); // Guardar la información de la transparencia
            }

            // Redimensionar la imagen
            imagecopyresampled($imagen_comprimida, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, imagesx($imagen), imagesy($imagen));

            // Guardar la imagen comprimida en un archivo temporal
            $temp_file = tempnam(sys_get_temp_dir(), 'img');
            switch ($tipo_imagen) {
                case IMAGETYPE_JPEG:
                    imagejpeg($imagen_comprimida, $temp_file, 75); // Calidad 75
                    break;
                case IMAGETYPE_PNG:
                    imagepng($imagen_comprimida, $temp_file, 6); // Compresión de 0 a 9
                    break;
                case IMAGETYPE_GIF:
                    imagegif($imagen_comprimida, $temp_file);
                    break;
                case IMAGETYPE_BMP:
                    imagebmp($imagen_comprimida, $temp_file);
                    break;
                case IMAGETYPE_WEBP:
                    imagewebp($imagen_comprimida, $temp_file, 75); // Calidad 75
                    break;
            }

            // Leer el contenido del archivo comprimido
            $imagen_data = file_get_contents($temp_file);
            $imagen_data = $db->escape($imagen_data); // Escapa los datos para evitar SQL Injection

            // Limpiar
            imagedestroy($imagen);
            imagedestroy($imagen_comprimida);
            unlink($temp_file); // Eliminar archivo temporal
        } else {
            $session->msg('w', 'Inserte una imagen.');
            redirect('../pages/users-profile.php', false);
        }

        // Actualización de la base de datos
        $sql = "UPDATE users SET image = '{$imagen_data}' WHERE id='{$id}'";
        
        // Ejecutar la consulta SQL
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
