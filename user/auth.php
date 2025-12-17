<?php
// DEBUG TEMPORAL (quitar luego)
file_put_contents(__DIR__ . "/auth_hit.log", date("c") . " HIT auth.php\n", FILE_APPEND);
header("X-AUTH-HIT: 1");
?>
<?php include_once('../config/load.php'); ?>


<?php include_once('../config/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if(empty($errors)){
  $user_id = authenticate($username, $password);
  if($user_id){
    //create session with id
     $session->login($user_id);
    //Update Sign in time
     updateLastLogIn($user_id);
     $session->msg("s", "Bienvenido a Laboratorio Mecanica de suelo.");
     redirect('../pages/home.php',false);

  } else {
    $session->msg("d", "Nombre de usuario y/o contraseña incorrecto.");
    redirect('../index.php',false);
  }

} else {
   $session->msg("w", $errors);
   redirect('../index.php',false);
}

?>