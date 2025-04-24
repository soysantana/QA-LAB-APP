<?php
  require_once('../config/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);

  $e_user = find_by_id('users',$_POST['id']);
  $groups  = find_all('user_groups');
  if(!$e_user){
    $session->msg("d","Falta el ID del usuario.");
    redirect('/pages/users-group.php');
  }

//Update User basic info
  if(isset($_POST['update_user'])) {
    $req_fields = array('name','username','role');
    validate_fields($req_fields);
    if(empty($errors)){
             $id = $e_user['id'];
           $name = remove_junk($db->escape($_POST['name']));
       $username = remove_junk($db->escape($_POST['username']));
          $level = (int)$db->escape($_POST['role']);
       $status   = remove_junk($db->escape($_POST['status']));
            $sql = "UPDATE users SET name ='{$name}', username ='{$username}',user_level='{$level}',status='{$status}' WHERE id='{$db->escape($id)}'";
         $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"Cuenta actualizada ");
            redirect('/pages/users-group.php', false);
          } else {
            $session->msg('d',' Lo siento no se actualizó los datos.');
            redirect('/pages/users-group.php', false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('/pages/users-group.php',false);
    }
  }
?>