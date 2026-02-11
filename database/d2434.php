<?php
require_once('../config/load.php');
page_require_level(2);
$db = $GLOBALS['db'];

function n($v){
  if($v===null) return null;
  $v=str_replace(',','.',trim((string)$v));
  return is_numeric($v)?(float)$v:null;
}

$Technician = $db->escape($_POST['Technician'] ?? '');
$TestDate   = $_POST['TestDate'] ?? null;
$ReportDate = $_POST['ReportDate'] ?? null;

$D=n($_POST['D']); $L=n($_POST['L']); $A=n($_POST['A']); $V=n($_POST['V']);
$rows=$_POST['rows'] ?? [];

if(!$rows || !$A || !$L){
  $session->msg('d','Datos incompletos');
  redirect('../pages/constant_head.php',false); exit;
}

$db->query("START TRANSACTION");

try{

$db->query("
INSERT INTO d2434_tests
(technician,test_date,report_date,d_m,l_m,a_m2,v_m3)
VALUES
('$Technician',
".($TestDate?"'$TestDate'":"NULL").",
".($ReportDate?"'$ReportDate'":"NULL").",
$D,$L,$A,$V)
");

$test_id=(int)$db->insert_id();
if($test_id<=0) throw new Exception("No ID");

foreach($rows as $i=>$r){
  $h1=n($r['h1']); $h2=n($r['h2']); $Q=n($r['Q']); $t=n($r['t']); $T=n($r['Temp']);
  if($h1===null && $h2===null && $Q===null) continue;

  $db->query("
    INSERT INTO d2434_runs
    (test_id,run_no,h1_m,h2_m,q_m3,t_s,temp_c)
    VALUES
    ($test_id,$i,
     ".($h1??"NULL").",".($h2??"NULL").",".($Q??"NULL").",".($t??"NULL").",".($T??"NULL").")
  ");
}

$db->query("COMMIT");
$session->msg('s',"Ensayo D2434 guardado (#$test_id)");
redirect('../pages/constant_head.php?ok=1',false); exit;

}catch(Exception $e){
  $db->query("ROLLBACK");
  $session->msg('d',$e->getMessage());
  redirect('../pages/constant_head.php',false); exit;
}
