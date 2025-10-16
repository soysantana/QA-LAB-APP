<?php
$page_title = 'Solicitados vs Entregados por Cliente';
$menu_active = 'reportes';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

// ==================== FILTROS ====================
$anio    = isset($_GET['anio'])    ? trim($_GET['anio'])    : '';
$mes     = isset($_GET['mes'])     ? trim($_GET['mes'])     : '';
$cliente = isset($_GET['cliente']) ? trim($_GET['cliente']) : '';

$years   = find_by_sql("SELECT DISTINCT YEAR(Sample_Date) AS y FROM lab_test_requisition_form ORDER BY y DESC");
$clients = find_by_sql("SELECT DISTINCT Client FROM lab_test_requisition_form ORDER BY Client");

// ==================== HELPERS ====================
function sql_norm($c){return "LOWER(REPLACE(REPLACE(TRIM($c),' ',''),'í','i'))";}
$norm_e = sql_norm('e.Test_Type');
$norm_d = sql_norm('d.Test_Type');

function pctClass($p){
  if($p>=90)return 'bg-success';
  if($p>=70)return 'bg-warning text-dark';
  return 'bg-danger';
}
function monthName($m){
  $n=[1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  return $n[(int)$m]??$m;
}

// ==================== SUBQUERY TOKEN ====================
$nums="SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL
SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL
SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL
SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL
SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25 UNION ALL
SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30";

$expSub="SELECT r.Client,r.Sample_ID,r.Sample_Number,r.Sample_Date,
TRIM(BOTH '\"' FROM TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(COALESCE(r.Test_Type,''),' ',''),',,',','),',',n.n),',',-1))) AS Test_Type
FROM lab_test_requisition_form r
JOIN ($nums)n
ON n.n<=1+LENGTH(REPLACE(REPLACE(COALESCE(r.Test_Type,''),' ',''),',,',','))-LENGTH(REPLACE(REPLACE(REPLACE(COALESCE(r.Test_Type,''),' ',''),',,',','),',',''))";

// ==================== WHERE ====================
$w=[];$base=["e.Test_Type IS NOT NULL","e.Test_Type<>''",$norm_e."<>'envio'"];
if($anio)$w[]="YEAR(e.Sample_Date)='".$db->escape($anio)."'";
if($mes)$w[]="MONTH(e.Sample_Date)=".(int)$mes;
if($cliente)$w[]="e.Client='".$db->escape($cliente)."'";
$where='WHERE '.implode(' AND ',array_merge($base,$w));

// ==================== QUERY PRINCIPAL ====================
$sql="
SELECT e.Client,YEAR(e.Sample_Date)an,MONTH(e.Sample_Date)ms,
COUNT(*)sol,SUM(EXISTS(SELECT 1 FROM test_delivery d WHERE d.Sample_ID=e.Sample_ID AND d.Sample_Number=e.Sample_Number AND $norm_d=$norm_e))ent
FROM(SELECT DISTINCT t.Client,t.Sample_ID,t.Sample_Number,t.Sample_Date,t.Test_Type FROM($expSub)t)e
$where
GROUP BY e.Client,an,ms
ORDER BY an DESC,ms DESC,e.Client";

$r=$db->query($sql);
if(!$r){error_log($db->error);die('Error de consulta');}

$rows=[];$ts=0;$te=0;
while($f=$r->fetch_assoc()){
  $f['pend']=$f['sol']-$f['ent'];
  $rows[]=$f;$ts+=$f['sol'];$te+=$f['ent'];
}
$pct=$ts?round($te/$ts*100,1):0;
?>

<main id="main" class="main">
<div class="pagetitle mb-3"><h1 class="fw-bold text-primary"><i class="bi bi-graph-up"></i> Solicitudes vs Entregas</h1></div>

<!-- ===== KPIs Modernos ===== -->
<section class="section">
<div class="row g-3 text-center">
  <div class="col-md-4">
    <div class="card shadow-lg border-0 text-white" style="background:linear-gradient(45deg,#007bff,#00c6ff)">
      <div class="card-body">
        <h6>Ensayos Solicitados</h6>
        <h2 class="fw-bold"><?=number_format($ts)?></h2>
        <i class="bi bi-inbox display-5"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-lg border-0 text-white" style="background:linear-gradient(45deg,#28a745,#5be17f)">
      <div class="card-body">
        <h6>Ensayos Entregados</h6>
        <h2 class="fw-bold"><?=number_format($te)?></h2>
        <i class="bi bi-send-check display-5"></i>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-lg border-0 text-white" style="background:linear-gradient(45deg,#ffc107,#ffeb3b)">
      <div class="card-body">
        <h6>Porcentaje de Entrega</h6>
        <h2 class="fw-bold"><?=$pct?>%</h2>
        <div class="progress mt-2" style="height:10px;">
          <div class="progress-bar <?=pctClass($pct)?>" style="width:<?=$pct?>%"></div>
        </div>
        <i class="bi bi-bar-chart-fill display-5 mt-2"></i>
      </div>
    </div>
  </div>
</div>

<!-- ===== FILTROS ===== -->
<div class="card mt-4 shadow-sm">
  <div class="card-body">
    <form class="row g-3" method="GET">
      <div class="col-md-3">
        <label class="form-label">Año</label>
        <select name="anio" class="form-select">
          <option value="">Todos</option>
          <?php foreach($years as $y):$v=$y['y'];?>
            <option value="<?=$v?>" <?=($anio==$v?'selected':'')?>><?=$v?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Mes</label>
        <select name="mes" class="form-select">
          <option value="">Todos</option>
          <?php for($m=1;$m<=12;$m++):?>
          <option value="<?=$m?>" <?=($mes==$m?'selected':'')?>><?=str_pad($m,2,'0',STR_PAD_LEFT).' - '.monthName($m)?></option>
          <?php endfor;?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Cliente</label>
        <select name="cliente" class="form-select">
          <option value="">Todos</option>
          <?php foreach($clients as $c):$cl=$c['Client']??'';?>
          <option value="<?=htmlspecialchars($cl)?>" <?=($cliente===$cl?'selected':'')?>><?=htmlspecialchars($cl?:'(Sin cliente)')?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100"><i class="bi bi-filter-circle"></i> Aplicar</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== TABLA ===== -->
<div class="card mt-4 shadow-sm">
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover table-striped align-middle">
<thead class="table-dark">
<tr>
<th>Cliente</th><th>Año</th><th>Mes</th>
<th class="text-end">Solicitados</th>
<th class="text-end">Entregados</th>
<th class="text-end">Pendientes</th>
<th class="text-center">% Entrega</th>
<th class="text-center">Detalles</th>
</tr></thead><tbody>
<?php if(empty($rows)){echo"<tr><td colspan='8' class='text-center text-muted py-3'>Sin datos</td></tr>";}
else{
$i=0;foreach($rows as $r):$i++;
$p=$r['sol']?round($r['ent']/$r['sol']*100,1):0;$b=pctClass($p);
$pend=max(0,$r['pend']);$id='det_'.$i;
?>
<tr>
<td><strong><?=htmlspecialchars($r['Client']?:'(Sin cliente)')?></strong></td>
<td><?=$r['an']?></td><td><?=monthName($r['ms'])?></td>
<td class="text-end"><?=number_format($r['sol'])?></td>
<td class="text-end"><?=number_format($r['ent'])?></td>
<td class="text-end"><?=number_format($pend)?></td>
<td class="text-center"><span class="badge <?=$b?>"><?=$p?>%</span></td>
<td class="text-center">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?=$id?>">
<i class="bi bi-eye"></i> Ver
<?php if($pend>0):?><span class="badge bg-danger ms-1"><?=$pend?></span><?php endif;?>
</button>
</td>
</tr>

<!-- ===== MODAL ===== -->
<div class="modal fade" id="<?=$id?>" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title"><i class="bi bi-person-lines-fill"></i> <?=$r['Client']?> — <?=$r['an']?>/<?=$r['ms']?></h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<ul class="nav nav-tabs" role="tablist">
<li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sol<?=$i?>">Solicitados</button></li>
<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#pen<?=$i?>">Pendientes</button></li>
</ul>
<div class="tab-content pt-3">
<!-- Solicitados -->
<div class="tab-pane fade show active" id="sol<?=$i?>">
<?php
$ex=$expSub." WHERE r.Client='".$db->escape($r['Client'])."' AND YEAR(r.Sample_Date)=".(int)$r['an']." AND MONTH(r.Sample_Date)=".(int)$r['ms'];
$sqls="SELECT DISTINCT e.Sample_ID,e.Sample_Number,e.Test_Type,DATE(e.Sample_Date)fecha
FROM($ex)e
WHERE e.Test_Type<>'' AND ".sql_norm('e.Test_Type')." <> 'envio' ORDER BY fecha DESC,Sample_ID";
$q=$db->query($sqls);$ds=$q?$q->fetch_all(MYSQLI_ASSOC):[];
if(!$ds){echo"<div class='text-muted'>No hay solicitados.</div>";}
else{
echo"<table class='table table-sm table-striped'><thead><tr><th>Fecha</th><th>Sample ID</th><th>Sample Number</th><th>Test Type</th></tr></thead><tbody>";
foreach($ds as $d){echo"<tr><td>{$d['fecha']}</td><td>{$d['Sample_ID']}</td><td>{$d['Sample_Number']}</td><td>{$d['Test_Type']}</td></tr>";}
echo"</tbody></table>";
}?>
</div>
<!-- Pendientes -->
<div class="tab-pane fade" id="pen<?=$i?>">
<?php
$sqlp="SELECT DISTINCT e.Sample_ID,e.Sample_Number,e.Test_Type,DATE(e.Sample_Date)fecha
FROM($ex)e
LEFT JOIN test_delivery d ON d.Sample_ID=e.Sample_ID AND d.Sample_Number=e.Sample_Number AND ".sql_norm('d.Test_Type')."=".sql_norm('e.Test_Type')."
WHERE e.Test_Type<>'' AND ".sql_norm('e.Test_Type')." <> 'envio' AND d.Sample_ID IS NULL
ORDER BY fecha DESC,Sample_ID";
$qp=$db->query($sqlp);$dp=$qp?$qp->fetch_all(MYSQLI_ASSOC):[];
if(!$dp){echo"<div class='text-success'>No hay pendientes ✅</div>";}
else{
echo"<table class='table table-sm table-striped'><thead><tr><th>Fecha</th><th>Sample ID</th><th>Sample Number</th><th>Test Type</th></tr></thead><tbody>";
foreach($dp as $d){echo"<tr><td>{$d['fecha']}</td><td>{$d['Sample_ID']}</td><td>{$d['Sample_Number']}</td><td>{$d['Test_Type']}</td></tr>";}
echo"</tbody></table>";
}?>
</div>
</div>
</div>
<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>
</div></div></div>
<?php endforeach;}?>
</tbody></table></div></div></div></section>
</main>

<?php include_once('../components/footer.php'); ?>

<script>
// Abrir pestaña pendientes automáticamente si hay badge roja
document.addEventListener('shown.bs.modal', e=>{
 const btn=document.querySelector(`[data-bs-target="#${e.target.id}"]`);
 if(!btn)return;
 const red=btn.querySelector('.badge.bg-danger');
 if(red){const t=e.target.querySelector('.nav-link[href="#pen'+e.target.id.split('_')[1]+'"]');if(t)t.click();}
},true);
</script>
