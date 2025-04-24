<?php
  if(isset($_POST['add_group'])){

   $req_fields = array('newGroupName','newGroupLevel');
   validate_fields($req_fields);

   if(find_by_groupName($_POST['newGroupName']) === false ){
     $session->msg('d','<b>Error!</b> Este nombre existe');
     redirect('add_group.php', false);
   }elseif(find_by_groupLevel($_POST['newGroupLevel']) === false) {
     $session->msg('d','<b>Error!</b> Este nivel existe ');
     redirect('add_group.php', false);
   }
   if(empty($errors)){
           $name = remove_junk($db->escape($_POST['newGroupName']));
          $level = remove_junk($db->escape($_POST['newGroupLevel']));
         $status = remove_junk($db->escape($_POST['newStatus']));

        $query  = "INSERT INTO user_groups (";
        $query .="group_name,group_level,group_status";
        $query .=") VALUES (";
        $query .=" '{$name}', '{$level}','{$status}'";
        $query .=")";
        if($db->query($query)){
          //sucess
          $session->msg('s',"Grupo ha sido creado! ");
          redirect('/pages/views-group.php', false);
        } else {
          //failed
          $session->msg('d','Lamentablemente no se pudo crear el grupo!');
          redirect('/pages/views-group.php', false);
        }
   } else {
     $session->msg("d", $errors);
      redirect('/pages/views-group.php',false);
   }
 }
?>