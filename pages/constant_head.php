<?php
$page_title = 'Hydraulic Conductivity - ASTM D2434';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

function hnum($v){
  $v = str_replace(',', '.', trim((string)$v));
  return is_numeric($v) ? (float)$v : null;
}

$prefill = [
  'D' => '0.113',
  'L' => '0.150',
  'A' => '0.010100',
  'V' => '0.0015',
  'SpecWeight' => '',
  'rows' => []
];
?>

<main id="main" class="main">
<div class="pagetitle">
  <h1>Hydraulic Conductivity (Constant Head)</h1>
</div>

<section class="section">
<div class="row">

<form class="row" action="../database/d2434.php" method="post" id="d2434Form">

<?php echo display_msg($msg); ?>

<!-- ===================== CARD 1 ===================== -->
<div class="col-lg-12">
<div class="card"><div class="card-body">
<h5 class="card-title">Trial Information</h5>
<div class="row g-3">
  <div class="col-md-4"><label>Technician</label><input class="form-control" name="Technician"></div>
  <div class="col-md-4"><label>Test Date</label><input type="date" class="form-control" name="TestDate"></div>
  <div class="col-md-4"><label>Report Date</label><input type="date" class="form-control" name="ReportDate"></div>
</div>
</div></div>
</div>

<!-- ===================== CARD 2 ===================== -->
<div class="col-lg-12">
<div class="card"><div class="card-body">
<h5 class="card-title">Specimen Geometry</h5>
<div class="row g-3">
  <div class="col-md-3"><label>D (m)</label><input id="D" name="D" class="form-control" value="<?=$prefill['D']?>"></div>
  <div class="col-md-3"><label>L (m)</label><input id="L" name="L" class="form-control" value="<?=$prefill['L']?>"></div>
  <div class="col-md-3"><label>A (m²)</label><input id="A" name="A" class="form-control" value="<?=$prefill['A']?>" readonly></div>
  <div class="col-md-3"><label>V (m³)</label><input id="V" name="V" class="form-control" value="<?=$prefill['V']?>" readonly></div>
</div>
</div></div>
</div>

<!-- ===================== TABLE ===================== -->
<div class="col-lg-12">
<div class="card"><div class="card-body">
<h5 class="card-title">Test Data (15 Runs)</h5>

<table class="table table-bordered table-sm">
<thead>
<tr>
<th>#</th><th>h1</th><th>h2</th><th>Q</th><th>t</th><th>Temp</th>
<th>h</th><th>v</th><th>i</th><th>K</th><th>K20</th>
</tr>
</thead>
<tbody>
<?php for($i=1;$i<=15;$i++): ?>
<tr>
<td><?=$i?></td>
<td><input id="h1_<?=$i?>" name="rows[<?=$i?>][h1]" class="form-control form-control-sm"></td>
<td><input id="h2_<?=$i?>" name="rows[<?=$i?>][h2]" class="form-control form-control-sm"></td>
<td><input id="Q_<?=$i?>"  name="rows[<?=$i?>][Q]"  class="form-control form-control-sm"></td>
<td><input id="t_<?=$i?>"  name="rows[<?=$i?>][t]"  class="form-control form-control-sm" value="60"></td>
<td><input id="Temp_<?=$i?>" name="rows[<?=$i?>][Temp]" class="form-control form-control-sm"></td>

<td><input id="h_<?=$i?>" readonly class="form-control form-control-sm"></td>
<td><input id="v_<?=$i?>" readonly class="form-control form-control-sm"></td>
<td><input id="i_<?=$i?>" readonly class="form-control form-control-sm"></td>
<td><input id="K_<?=$i?>" readonly class="form-control form-control-sm"></td>
<td><input id="K20_<?=$i?>" readonly class="form-control form-control-sm"></td>
</tr>
<?php endfor; ?>
</tbody>
</table>
</div></div>
</div>

<div class="col-lg-3">
<button type="submit" name="d2434-save" class="btn btn-success w-100">Guardar Ensayo</button>
</div>

</form>
</div>
</section>
</main>

<script src="../js/d2434.js"></script>

<script>
document.addEventListener('DOMContentLoaded',()=>{
  const form=document.getElementById('d2434Form');
  let timer=null, running=false;

  function recalc(){
    if(window.D2434_CalcGeom) D2434_CalcGeom();
    if(window.D2434_Recalc) D2434_Recalc();
  }

  recalc();

  form.addEventListener('input',()=>{
    if(running) return;
    clearTimeout(timer);
    timer=setTimeout(()=>{
      running=true;
      try{ recalc(); } finally{ running=false; }
    },200);
  });
});
</script>

<?php include_once('../components/footer.php'); ?>
