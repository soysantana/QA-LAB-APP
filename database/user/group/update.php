<?php
  require_once('../config/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);

  $e_group = find_by_id('user_groups',$_POST['id']);

  if(!$e_group){
    $session->msg("d","Falta el ID del usuario.");
    redirect('/pages/users-group.php');
  }

//Update User basic info
  if(isset($_POST['update_group'])) {
    $req_fields = array('GroupName','GroupLevel','status');
    validate_fields($req_fields);
    if(empty($errors)){
             $id = $e_group['id'];
           $name = remove_junk($db->escape($_POST['GroupName']));
       $level = remove_junk($db->escape($_POST['GroupLevel']));
          $status = (int)$db->escape($_POST['status']);
            $sql = "UPDATE user_groups SET group_name ='{$name}', group_level ='{$level}',group_status='{$status}' WHERE id='{$db->escape($id)}'";
         $result = $db->query($sql);
          if($result && $db->affected_rows() === 1){
            $session->msg('s',"Grupo actualizada ");
            redirect('/pages/views-group.php', false);
          } else {
            $session->msg('d',' Lo siento no se actualizó.');
            redirect('/pages/views-group.php', false);
          }
    } else {
      $session->msg("d", $errors);
      redirect('/pages/views-group.php',false);
    }
  }
?>