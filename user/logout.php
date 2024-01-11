<?php
  require_once('../config/load.php');
  if(!$session->logout()) {redirect("../index.php");}
?>