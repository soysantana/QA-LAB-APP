<?php
  require_once('../config/load.php');
  if(isset($_POST['add_user'])){

   $req_fields = array('full-name','username','password','accesslevel' );
   validate_fields($req_fields);

   if(empty($errors)){
       $id = uuid();
       $name   = remove_junk($db->escape($_POST['full-name']));
       $username   = remove_junk($db->escape($_POST['username']));
       $password   = remove_junk($db->escape($_POST['password']));
       $user_level = (int)$db->escape($_POST['accesslevel']);
       $email   = remove_junk($db->escape($_POST['email']));
       $phone   = remove_junk($db->escape($_POST['phone']));
       $job   = remove_junk($db->escape($_POST['job']));
       $password = sha1($password);
        $query = "INSERT INTO users (";
        $query .="id,name,username,password,user_level,email,phone,job,status,alias";
        $query .=") VALUES (";
        $query .=" '{$id}', '{$name}', '{$username}', '{$password}', '{$user_level}','{$email}','{$phone}','{$job}','1',''";
        $query .=")";
        if($db->query($query)){
          //sucess
          $session->msg('s'," Cuenta de usuario ha sido creada");
          redirect('/pages/users-register.php', false);
        } else {
          //failed
          $session->msg('d',' No se pudo crear la cuenta.');
          redirect('/pages/users-register.php', false);
        }
   } else {
     $session->msg("d", $errors);
      redirect('/pages/users-register.php',false);
   }
 }
?>