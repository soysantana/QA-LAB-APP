<?php
require_once('../config/load.php');
$user = current_user();

if (isset($_POST['update'])) {
    $required_fields = ['New-Password', 'Current-Password', 'id', 'Renew-Password'];
    validate_fields($required_fields);

    if (empty($errors)) {
        // Check if the new password matches the re-entered password
        if ($_POST['New-Password'] !== $_POST['Renew-Password']) {
            $session->msg('d', "Las contraseñas no coinciden.");
            redirect('../pages/users-profile.php', false);
        }

        if (sha1($_POST['Current-Password']) !== $user['password']) {
            $session->msg('d', "Tu antigua contraseña no coincide");
            redirect('../pages/users-profile.php', false);
        }

        $id = $_POST['id'];
        $new_password = remove_junk($db->escape(sha1($_POST['New-Password'])));
        $sql = "UPDATE users SET password = '{$new_password}' WHERE id = '{$db->escape($id)}'";
        $result = $db->query($sql);

        if ($result && $db->affected_rows() === 1) {
            $session->logout();
            $session->msg('s', "Inicia sesión con tu nueva contraseña.");
            redirect('../index.php', false);
        } else {
            $session->msg('d', 'Lo siento, la actualización falló.');
            redirect('../pages/users-profile.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('../pages/users-profile.php', false);
    }
}
?>
