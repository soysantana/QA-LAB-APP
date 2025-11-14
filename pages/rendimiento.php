<?php
// ===============================================
// rendimiento.php  (Parte 1)
// ===============================================
$page_title = 'Rendimiento de Técnicos';
require_once('../config/load.php');
page_require_level(2);
include_once('../components/header.php');

date_default_timezone_set('America/Santo_Domingo');

function v($k,$d=null){return isset($_REQUEST[$k])?trim($_REQUEST[$k]):$d;}
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}

// ===============================================
// 1. RANGO DE FECHAS
// ===============================================
$quick  = v('quick','7d');
$fromIn = v('from');
$toIn   = v('to');

$today = date('Y-m-d');

switch($quick){
  case 'today':
    $from=$fromIn?:$today; $to=$toIn?:$today;
    break;
  case '30d':
    $from=$fromIn?:date('Y-m-d',strtotime('-30 days')); $to=$toIn?:$today;
    break;
  case '12m':
    $from=$fromIn?:date('Y-m-d',strtotime('-12 months')); $to=$toIn?:$today;
    break;
  case 'custom':
    $from=$fromIn?:$today; $to=$toIn?:$today;
    break;
  case '7d':
  default:
    $from=$fromIn?:date('Y-m-d',strtotime('-7 days')); $to=$toIn?:$today;
}

$from_dt = $db->escape("$from 00:00:00");
$to_dt   = $db->escape("$to 23:59:59");

// días del rango
$days = max(1, (int)((strtotime($to)-strtotime($from))/86400)+1);

// ===============================================
// 2. CARGAR USUARIOS (para mapear alias->nombre)
// ===============================================

$userMap = [];   // alias => Name
$userMapFull = []; // Name => alias
$qU = $db->query("SELECT name, alias, job FROM users WHERE alias IS NOT NULL AND alias<>''");
if($qU){
  while($u=$qU->fetch_assoc()){
    $alias = strtoupper(trim($u['alias']));
    $name  = trim($u['name']);
    if($alias!==''){
      $userMap[$alias] = $name;
      $userMapFull[$name] = $alias;
    }
  }
}

// ===============================================
// 3. PARSER DE ALIAS MÚLTIPLES
//    A/J, A-J-B, A+B+C, A J B, A|J
// ===============================================

function parse_alias($raw){
  $raw = strtoupper(trim((string)$raw));
  if($raw==='') return [];

  // Separadores válidos
  $seps = ['+', '/', '-', ',', ';', '|', ' '];

  $pattern = '/[+\-\/,;|\s]+/';
  $parts = preg_split($pattern,$raw);

  $clean=[];
  foreach($parts as $p){
    $p=trim($p);
    if($p!=='') $clean[]=$p;
  }
  return $clean;
}

// ===============================================
// 4. LISTAS PARA COMBOS
// ===============================================
$techList=[];
$resTech = $db->query("
  SELECT Technician AS T FROM test_preparation WHERE Technician<>'' AND Technician IS NOT NULL
  UNION
  SELECT Technician FROM test_realization WHERE Technician<>'' AND Technician IS NOT NULL
  UNION
  SELECT Technician FROM test_delivery WHERE Technician<>'' AND Technician IS NOT NULL
  UNION
  SELECT Register_By FROM lab_test_requisition_form WHERE Register_By<>'' AND Register_By IS NOT NULL
  UNION
  SELECT Register_By FROM test_review WHERE Register_By<>'' AND Register_By IS NOT NULL
");
if($resTech) while($r=$resTech->fetch_assoc()){ $t=trim($r['T']); if($t!=='')$techList[$t]=true; }
$techList=array_keys($techList);
sort($techList,SORT_NATURAL|SORT_FLAG_CASE);

$ttList=[];
$resTT=$db->query("
  SELECT DISTINCT UPPER(TRIM(Test_Type)) AS T
  FROM (
    SELECT Test_Type FROM lab_test_requisition_form
    UNION ALL
    SELECT Test_Type FROM test_preparation
    UNION ALL
    SELECT Test_Type FROM test_realization
    UNION ALL
    SELECT Test_Type FROM test_delivery
  ) x
  WHERE Test_Type IS NOT NULL AND Test_Type<>''
");
if($resTT) while($r=$resTT->fetch_assoc()){ $tt=trim($r['T']); if($tt!=='')$ttList[$tt]=true; }
$ttList=array_keys($ttList);
sort($ttList,SORT_NATURAL|SORT_FLAG_CASE);

$filterTech = v('tech','');
$filterTT   = strtoupper(trim(v('ttype','')));

// ===============================================
// 5. FUNCIÓN QUE EXPLOTA ALIAS MÚLTIPLES
//    Y devuelve una fila por alias individual
// ===============================================
function expand_rows_with_alias($rows, $userMap){
  $out=[];
  foreach($rows as $r){
    $raw = $r['Tech'];
    $aliasList = parse_alias($raw); // ["A","J"]
    if(empty($aliasList)){
      $aliasList=['?'];
    }
    foreach($aliasList as $a){
      $name = $userMap[$a] ?? $a;
      $copy=$r;
      $copy['TechAlias']=$a;
      $copy['Tech']=$name;
      $out[]=$copy;
    }
  }
  return $out;
}

// ===============================================
// 6. FUNCIÓN PARA CONSULTAR UNA ETAPA
// ===============================================

function fetch_stage($db,$table,$dateField,$techField,$from_dt,$to_dt,$filterTech,$filterTT,$userMap){
  $where=[];
  $where[]="$dateField BETWEEN '$from_dt' AND '$to_dt'";
  $where[]="Test_Type<>'' AND Test_Type IS NOT NULL";
  $where[]="$techField<>'' AND $techField IS NOT NULL";
  if($filterTT!==''){
    $ft=$db->escape($filterTT);
    $where[]="UPPER(TRIM(Test_Type))='$ft'";
  }

  $whereSql=implode(' AND ',$where);

  $sql="
    SELECT $techField AS Tech,
           UPPER(TRIM(Test_Type)) AS Test_Type,
           COUNT(*) AS Total
    FROM $table
    WHERE $whereSql
    GROUP BY $techField, UPPER(TRIM(Test_Type))
  ";
  $res=$db->query($sql);
  $rows=[];
  if($res) while($r=$res->fetch_assoc()) $rows[]=$r;

  // expand alias
  $expanded = expand_rows_with_alias($rows,$userMap);

  // aplicar filtro por técnico (por nombre)
  if($filterTech!==''){
    $out=[];
    foreach($expanded as $e){
      if($e['Tech']===$filterTech) $out[]=$e;
    }
    return $out;
  }

  return $expanded;
}

// ===============================================
// 7. CONSULTAR TODAS LAS ETAPAS
// ===============================================
$rows_reg = fetch_stage($db,'lab_test_requisition_form','Registed_Date','Register_By',$from_dt,$to_dt,$filterTech,$filterTT,$userMap);
$rows_pre = fetch_stage($db,'test_preparation','Start_Date','Technician',$from_dt,$to_dt,$filterTech,$filterTT,$userMap);
$rows_rea = fetch_stage($db,'test_realization','Start_Date','Technician',$from_dt,$to_dt,$filterTech,$filterTT,$userMap);
$rows_ent = fetch_stage($db,'test_delivery','Start_Date','Technician',$from_dt,$to_dt,$filterTech,$filterTT,$userMap);
$rows_dig = fetch_stage($db,'test_review','Start_Date','Register_By',$from_dt,$to_dt,$filterTech,$filterTT,$userMap);

// ===============================================
// 8. KPI GLOBAL POR ETAPA
// ===============================================
function total_stage($rows){
  $n=0;
  foreach($rows as $r) $n += (int)$r['Total'];
  return $n;
}

$kpi_reg = total_stage($rows_reg);
$kpi_pre = total_stage($rows_pre);
$kpi_rea = total_stage($rows_rea);
$kpi_ent = total_stage($rows_ent);
$kpi_dig = total_stage($rows_dig);
$kpi_total = $kpi_reg+$kpi_pre+$kpi_rea+$kpi_ent+$kpi_dig;

// ===============================================
// 9. REPEAT (test_repeat)
// ===============================================
$repSet=[];
$qRep=$db->query("
  SELECT Sample_ID,Sample_Number,UPPER(TRIM(Test_Type)) AS Test_Type
  FROM test_repeat
  WHERE Start_Date BETWEEN '$from_dt' AND '$to_dt'
");
if($qRep){
  while($r=$qRep->fetch_assoc()){
    $k=strtoupper(trim($r['Sample_ID'])).'|'.strtoupper(trim($r['Sample_Number'])).'|'.$r['Test_Type'];
    $repSet[$k]=true;
  }
}
$kpi_rep = count($repSet);

// ===============================================
// 10. CONSTRUIR ESTADÍSTICAS POR TÉCNICO
// ===============================================
$stats=[];

function addRows(&$stats,$rows,$stageKey,$isReal=false){
  foreach($rows as $r){
    $t=$r['Tech'];
    $tt=$r['Test_Type'];
    $n=(int)$r['Total'];

    if(!isset($stats[$t])){
      $stats[$t]=[
        'reg'=>0,'pre'=>0,'rea'=>0,'ent'=>0,'dig'=>0,
        'total'=>0,'rep'=>0,'types'=>[]
      ];
    }
    $stats[$t][$stageKey]+=$n;
    $stats[$t]['total']+=$n;

    if($isReal){
      if(!isset($stats[$t]['types'][$tt]))$stats[$t]['types'][$tt]=0;
      $stats[$t]['types'][$tt]+=$n;
    }
  }
}

addRows($stats,$rows_reg,'reg',false);
addRows($stats,$rows_pre,'pre',false);
addRows($stats,$rows_rea,'rea',true);
addRows($stats,$rows_ent,'ent',false);
addRows($stats,$rows_dig,'dig',false);

// % repetición: cualquier ensayo que esté en repSet
foreach($stats as $t=>&$st){
  $st['avg_per_day']=$days>0?round($st['total']/$days,2):0;
  $st['rep_pct']=$st['total']>0?round(($st['rep']/$st['total'])*100,1):0;
}
unset($st);

// Ordenar ranking
$statsSorted=$stats;
uasort($statsSorted,function($a,$b){return $b['total']<=>$a['total'];});

// Técnico seleccionado
$selectedTech=$filterTech;
if($selectedTech==='' && !empty($statsSorted)){
  $selectedTech=array_key_first($statsSorted);
}

?>
<?php
// ===============================================
// rendimiento.php  (Parte 2)
// ===============================================

// ===============================================
// Últimos 50 ensayos del técnico seleccionado
// ===============================================
$lastRows=[];
if($selectedTech!==''){
  $techAlias = $userMapFull[$selectedTech] ?? $selectedTech;

  $techEsc=$db->escape($techAlias);

  $sqlLast="
    SELECT Registed_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)) AS Test_Type, 'Registrada' AS Stage
    FROM lab_test_requisition_form
    WHERE Register_By='$techEsc' AND Registed_Date BETWEEN '$from_dt' AND '$to_dt'

    UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)), 'Preparación'
    FROM test_preparation
    WHERE Technician='$techEsc' AND Start_Date BETWEEN '$from_dt' AND '$to_dt'

    UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)), 'Realización'
    FROM test_realization
    WHERE Technician='$techEsc' AND Start_Date BETWEEN '$from_dt' AND '$to_dt'

    UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)), 'Entrega'
    FROM test_delivery
    WHERE Technician='$techEsc' AND Start_Date BETWEEN '$from_dt' AND '$to_dt'

    UNION ALL
    SELECT Start_Date AS Dt, Sample_ID, Sample_Number, UPPER(TRIM(Test_Type)), 'Digitado'
    FROM test_review
    WHERE Register_By='$techEsc' AND Start_Date BETWEEN '$from_dt' AND '$to_dt'

    ORDER BY Dt DESC
    LIMIT 50
  ";

  $resL=$db->query($sqlLast);
  if($resL){
    while($r=$resL->fetch_assoc()){
      $key=strtoupper(trim($r['Sample_ID'])).'|'.strtoupper(trim($r['Sample_Number'])).'|'.$r['Test_Type'];
      $r['is_rep']=isset($repSet[$key]);
      $lastRows[]=$r;
    }
  }
}

?>
<main id="main" class="main" style="padding:18px;">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="mb-0">Desempeño de Técnicos</h1>
      <small class="text-muted">Rendimiento por etapa, tipo de ensayo y repetición.</small>
    </div>

    <span class="badge bg-light text-dark border d-none d-md-inline-flex align-items-center gap-1">
      <i class="bi bi-calendar-range"></i>
      Rango aplicado: <strong><?=h($from)?></strong> a <strong><?=h($to)?></strong>
    </span>
  </div>

  <!-- ===========================================
       FILTROS
       =========================================== -->
  <section class="section mb-3">
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body py-3">
        <form class="row g-2 align-items-end" method="GET">
          <div class="col-12 col-md-3">
            <label class="form-label form-label-sm">Intervalo rápido</label>
            <select name="quick" class="form-select form-select-sm">
              <option value="today" <?=$quick==='today'?'selected':''?>>Hoy</option>
              <option value="7d"    <?=$quick==='7d'?'selected':''?>>Últimos 7 días</option>
              <option value="30d"   <?=$quick==='30d'?'selected':''?>>Últimos 30 días</option>
              <option value="12m"   <?=$quick==='12m'?'selected':''?>>Últimos 12 meses</option>
              <option value="custom"<?=$quick==='custom'?'selected':''?>>Personalizado</option>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Desde</label>
            <input type="date" name="from" class="form-control form-control-sm" value="<?=h($from)?>">
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Hasta</label>
            <input type="date" name="to" class="form-control form-control-sm" value="<?=h($to)?>">
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Técnico</label>
            <select name="tech" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach($techList as $t): ?>
                <option value="<?=h($t)?>" <?=$filterTech===$t?'selected':''?>><?=h($t)?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label form-label-sm">Tipo de ensayo</label>
            <select name="ttype" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php foreach($ttList as $tt): ?>
                <option value="<?=h($tt)?>" <?=$filterTT===$tt?'selected':''?>><?=h($tt)?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-md-1 d-grid">
            <button class="btn btn-primary btn-sm">
              <i class="bi bi-funnel"></i> Aplicar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ===========================================
         KPIs
         =========================================== -->
    <div class="row g-3 mb-3">
      <?php
      $kpis=[
        ['label'=>'Ensayos Totales','value'=>$kpi_total,'icon'=>'bi-collection','class'=>'kpi-icon-main','desc'=>'Procesados en el rango.'],
        ['label'=>'Registradas','value'=>$kpi_reg,'icon'=>'bi-clipboard-plus','class'=>'kpi-icon-prep','desc'=>'Ingresadas al sistema.'],
        ['label'=>'Preparadas','value'=>$kpi_pre,'icon'=>'bi-hammer','class'=>'kpi-icon-prep','desc'=>'Procesadas en Preparación.'],
        ['label'=>'Realizadas','value'=>$kpi_rea,'icon'=>'bi-activity','class'=>'kpi-icon-real','desc'=>'Ejecutadas en laboratorio.'],
        ['label'=>'Entregadas','value'=>$kpi_ent,'icon'=>'bi-box-arrow-up-right','class'=>'kpi-icon-ent','desc'=>'Con hoja de trabajo.'],
        ['label'=>'Repetidos','value'=>$kpi_rep.' ('.($kpi_total>0?round(($kpi_rep/$kpi_total)*100,1):0).'%)','icon'=>'bi-arrow-repeat','class'=>'kpi-icon-repeat','desc'=>'Ensayos repetidos.'],
      ];
      ?>

      <?php foreach($kpis as $k): ?>
      <div class="col-6 col-md">
        <div class="kpi-card">
          <div class="kpi-card-main">
            <div>
              <div class="kpi-label"><?=$k['label']?></div>
              <div class="kpi-value"><?=$k['value']?></div>
            </div>
            <div class="kpi-icon <?=$k['class']?>"><i class="bi <?=$k['icon']?>"></i></div>
          </div>
          <div class="kpi-subtext"><?=$k['desc']?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>


  <!-- ===========================================
       RANKING + CHART
       =========================================== -->
  <?php
  // construir series top 10
  $chartTechs=array_keys($statsSorted);
  $chartTechs=array_slice($chartTechs,0,10);

  $series_reg=[];$series_pre=[];$series_rea=[];$series_ent=[];$series_dig=[];
  foreach($chartTechs as $t){
    $st=$statsSorted[$t];
    $series_reg[]=(int)$st['reg'];
    $series_pre[]=(int)$st['pre'];
    $series_rea[]=(int)$st['rea'];
    $series_ent[]=(int)$st['ent'];
    $series_dig[]=(int)$st['dig'];
  }
  ?>

  <section class="mb-3">
    <div class="row g-3">
      <!-- RANKING -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
              <strong>Ranking por Técnico</strong>
              <div class="small text-muted">Volumen por etapa y porcentajes.</div>
            </div>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover mb-0 align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Técnico</th>
                    <th class="text-end">Reg</th>
                    <th class="text-end">Prep</th>
                    <th class="text-end">Real</th>
                    <th class="text-end">Ent</th>
                    <th class="text-end">Dig</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">% Rep</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(empty($statsSorted)): ?>
                    <tr><td colspan="8" class="text-center py-3 text-muted">Sin datos.</td></tr>
                  <?php else: ?>
                    <?php foreach($statsSorted as $t=>$st): ?>
                    <tr class="<?=($selectedTech===$t?'table-primary':'')?>"
                        onclick="window.location='?quick=<?=h($quick)?>&from=<?=h($from)?>&to=<?=h($to)?>&tech=<?=urlencode($t)?>&ttype=<?=urlencode($filterTT)?>'">
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="avatar-tech"><?=h(mb_substr($t,0,1))?></div>
                          <div>
                            <div class="fw-semibold"><?=h($t)?></div>
                            <div class="small text-muted"><?=$st['avg_per_day']?> / día</div>
                          </div>
                        </div>
                      </td>
                      <td class="text-end"><?=$st['reg']?:''?></td>
                      <td class="text-end"><?=$st['pre']?:''?></td>
                      <td class="text-end"><?=$st['rea']?:''?></td>
                      <td class="text-end"><?=$st['ent']?:''?></td>
                      <td class="text-end"><?=$st['dig']?:''?></td>
                      <td class="text-end fw-bold"><?=$st['total']?></td>
                      <td class="text-end <?=$st['rep_pct']>0?'text-danger':'text-success'?>">
                        <?=$st['rep']?'('.$st['rep_pct'].'%)':'0%'?>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>


      <!-- CHART -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-header bg-white">
            <strong>Distribución por Técnica (Etapas)</strong>
          </div>
          <div class="card-body">
            <div id="chartByTech" style="height:350px;"></div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ===========================================
       PERFIL DEL TÉCNICO SELECCIONADO
       =========================================== -->
  <section class="mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white d-flex justify-content-between">
        <div>
          <strong>Perfil del Técnico</strong>
          <div class="small text-muted">Resumen + Mix de Ensayos + Historial</div>
        </div>
        <span class="badge bg-light border text-muted">Seleccionado: <strong><?=h($selectedTech?:'—')?></strong></span>
      </div>

      <div class="card-body">
        <?php if(!$selectedTech || !isset($stats[$selectedTech])): ?>
          <div class="text-muted text-center py-4">Selecciona un técnico en el ranking.</div>

        <?php else:
          $stSel = $stats[$selectedTech];
          $donut=[];
          if(!empty($stSel['types'])){
            arsort($stSel['types']);
            foreach($stSel['types'] as $tt=>$val){
              $donut[]=['name'=>$tt,'value'=>(int)$val];
            }
          }
        ?>
        <div class="row g-3">
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar-tech avatar-lg"><?=h(mb_substr($selectedTech,0,1))?></div>
                  <div>
                    <div class="fw-bold"><?=h($selectedTech)?></div>
                    <div class="small text-muted">Técnico de Laboratorio</div>
                  </div>
                </div>

                <ul class="list-unstyled mt-2 small">
                  <li><strong>Total:</strong> <?=$stSel['total']?></li>
                  <li><strong>Ensayos/día:</strong> <?=$stSel['avg_per_day']?></li>
                  <li><strong>Repetidos:</strong> <?=$stSel['rep']??0?> (<?=$stSel['rep_pct']?>%)</li>
                </ul>

                <hr>

                <div class="small text-muted mb-1">Por etapa</div>
                <ul class="list-unstyled small">
                  <li>Registradas: <?=$stSel['reg']?></li>
                  <li>Preparadas: <?=$stSel['pre']?></li>
                  <li>Realizadas: <?=$stSel['rea']?></li>
                  <li>Entregadas: <?=$stSel['ent']?></li>
                  <li>Digitadas: <?=$stSel['dig']?></li>
                </ul>
              </div>
            </div>

            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="small text-muted mb-2">Mix de Ensayos (Realización)</div>
                <div id="chartTechDonut" style="height:220px;"></div>
              </div>
            </div>

          </div>

          <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between">
                  <div class="small text-muted">Últimos ensayos</div>
                  <span class="badge bg-light border small">Máx. 50</span>
                </div>

                <div class="table-responsive" style="max-height:310px;overflow:auto;">
                  <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Fecha</th>
                        <th>Sample ID</th>
                        <th>#</th>
                        <th>Ensayo</th>
                        <th>Etapa</th>
                        <th>Repetido</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(empty($lastRows)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">Sin registros.</td></tr>
                      <?php else: ?>
                        <?php foreach($lastRows as $r): ?>
                        <tr>
                          <td><?=h(substr($r['Dt'],0,10))?></td>
                          <td><?=h($r['Sample_ID'])?></td>
                          <td><?=h($r['Sample_Number'])?></td>
                          <td><code><?=h($r['Test_Type'])?></code></td>
                          <td><?=h($r['Stage'])?></td>
                          <td>
                            <?php if($r['is_rep']): ?>
                              <span class="badge bg-danger-subtle text-danger border">Sí</span>
                            <?php else: ?>
                              —
                            <?php endif; ?>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>
</main>

<?php include_once('../components/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

<script>
// =======================================================
//  CHART: DISTRIBUCIÓN POR TÉCNICO (apilado por etapas)
// =======================================================
(function(){
  const el = document.getElementById('chartByTech');
  if(!el) return;
  const chart = echarts.init(el);

  const techs = <?= json_encode($chartTechs, JSON_UNESCAPED_UNICODE); ?>;
  const reg   = <?= json_encode($series_reg); ?>;
  const pre   = <?= json_encode($series_pre); ?>;
  const rea   = <?= json_encode($series_rea); ?>;
  const ent   = <?= json_encode($series_ent); ?>;
  const dig   = <?= json_encode($series_dig); ?>;

  const option = {
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: {
      top: 0,
      textStyle: { fontSize: 12 }
    },
    grid: { left: 10, right: 10, bottom: 10, top: 45, containLabel: true },
    xAxis: { type: 'value' },
    yAxis: { type: 'category', data: techs },
    series: [
      { name:'Registradas', type:'bar', stack:'total', data:reg },
      { name:'Preparadas',  type:'bar', stack:'total', data:pre },
      { name:'Realizadas',  type:'bar', stack:'total', data:rea },
      { name:'Entregadas',  type:'bar', stack:'total', data:ent },
      { name:'Digitadas',   type:'bar', stack:'total', data:dig }
    ]
  };
  chart.setOption(option);
  window.addEventListener('resize',()=>chart.resize());
})();


// =======================================================
//  CHART: DONUT DEL TÉCNICO SELECCIONADO
// =======================================================
(function(){
  const el = document.getElementById('chartTechDonut');
  if(!el) return;

  const chart = echarts.init(el);
  const data  = <?= json_encode($donutData, JSON_UNESCAPED_UNICODE); ?>;

  if(!data || data.length === 0){
    chart.clear();
    return;
  }

  const option = {
    tooltip: { trigger: 'item' },
    legend: {
      bottom: 0,
      type: 'scroll',
      textStyle: { fontSize: 11 }
    },
    series: [
      {
        name: 'Ensayos',
        type: 'pie',
        radius: ['40%','70%'],
        animation: true,
        itemStyle: { borderWidth: 2, borderRadius: 6 },
        label: { show: false },
        emphasis: {
          label: { show: true, fontWeight: 'bold', fontSize: 14 }
        },
        data: data
      }
    ]
  };

  chart.setOption(option);
  window.addEventListener('resize',()=>chart.resize());
})();
</script>
<style>
/* ==========================================================
   TARJETAS KPI
   ========================================================== */
.kpi-card{
  border-radius:14px;
  border:1px solid #e5e7eb;
  background:#ffffff;
  box-shadow:0 4px 12px rgba(15,23,42,0.06);
  padding:0.75rem 0.95rem;
  height:100%;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  transition:0.2s;
}
.kpi-card:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 20px rgba(15,23,42,0.08);
}
.kpi-card-main{
  display:flex;
  align-items:center;
  justify-content:space-between;
}
.kpi-label{
  font-size:0.75rem;
  text-transform:uppercase;
  letter-spacing:0.06em;
  color:#64748b;
}
.kpi-value{
  font-size:1.55rem;
  font-weight:700;
  color:#0f172a;
}
.kpi-mini{
  font-size:0.8rem;
  font-weight:600;
}
.kpi-icon{
  width:38px;
  height:38px;
  border-radius:999px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1.1rem;
}
.kpi-icon-main{ background:#eff6ff; color:#1d4ed8; }
.kpi-icon-prep{ background:#ecfeff; color:#0891b2; }
.kpi-icon-real{ background:#fef9c3; color:#ca8a04; }
.kpi-icon-ent{  background:#ecfdf3; color:#15803d; }
.kpi-icon-repeat{ background:#fef2f2; color:#b91c1c; }

.kpi-subtext{
  font-size:0.75rem;
  color:#6b7280;
  margin-top:0.25rem;
}


/* ==========================================================
   AVATAR DE TÉCNICO
   ========================================================== */
.avatar-tech{
  width:32px;
  height:32px;
  border-radius:999px;
  background:#dbeafe;
  color:#1e40af;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:0.9rem;
  font-weight:bold;
}
.avatar-lg{
  width:42px !important;
  height:42px !important;
  font-size:1.15rem !important;
}


/* ==========================================================
   TABLA RANKING
   ========================================================== */
.table-hover tbody tr:hover{
  background:#f8fafc !important;
  cursor:pointer;
}


/* ==========================================================
   TARJETA PERFIL TÉCNICO
   ========================================================== */
.card{
  border-radius:14px;
}
.card-header{
  border-radius:14px 14px 0 0 !important;
}
.card-body{
  border-radius:0 0 14px 14px !important;
}


/* ==========================================================
   BADGES DE ETAPAS
   ========================================================== */
.badge{
  font-weight:500;
  padding:3px 8px;
  border-radius:6px;
}

.bg-light-subtle { background:#f8fafc!important;color:#475569!important; }
.bg-primary-subtle { background:#e0f2fe!important;color:#0284c7!important; }
.bg-info-subtle { background:#cffafe!important;color:#06b6d4!important; }
.bg-success-subtle { background:#dcfce7!important;color:#16a34a!important; }
.bg-warning-subtle { background:#fef9c3!important;color:#854d0e!important; }
.bg-danger-subtle { background:#fee2e2!important;color:#b91c1c!important; }

/* Scroll tabla historial */
.table-responsive{
  scrollbar-width:thin;
}
.table-responsive::-webkit-scrollbar{
  width:6px;
  height:6px;
}
.table-responsive::-webkit-scrollbar-thumb{
  background:#cbd5e1;
  border-radius:6px;
}

/* Hover filas */
.row-tech:hover{
  background:#f1f5f9!important;
}
</style>
