<?php
// pages/job-rotation.php — COMPLETO con Generar / Actualizar / Eliminar rotaciones (calendar_rotation)
declare(strict_types=1);

$page_title = 'Rotación Laboral';
$ropln = 'show';

require_once('../config/load.php');
page_require_level(3);

/* -------------------- Helpers -------------------- */
function e($v){ return $GLOBALS['db']->escape((string)$v); }
function j($v){ return json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }

function initials($name){
  $parts = preg_split('/\s+/', trim($name));
  $ini=[]; foreach($parts as $p){ if($p!=='') $ini[] = mb_strtoupper(mb_substr($p,0,1,'UTF-8')).'.'; }
  return implode('', $ini);
}
function abbr_pair(array $pair){ return initials($pair[0]).'&'.initials($pair[1]); }
function shortify($s,$max=120){
  return (mb_strlen($s,'UTF-8')>$max) ? (mb_substr($s,0,$max-1,'UTF-8').'…') : $s;
}
function next_sunday(string $dateYmd): string {
  $ts = strtotime($dateYmd);
  $w = (int)date('w',$ts); // 0=Domingo
  return date('Y-m-d', $ts + (($w==0)?0:(7-$w))*86400);
}
function week_index_1based(string $dateYmd, string $startYmd): int {
  $d1 = new DateTime($startYmd); $d2 = new DateTime($dateYmd);
  return intdiv($d1->diff($d2)->days, 7) + 1;
}
function block_2m_index_1based(string $dateYmd, string $startYmd): int {
  $start = new DateTime($startYmd); $cur = new DateTime($dateYmd);
  $idx=1; $bStart=clone $start;
  while ($cur >= $bStart) { $bEnd = (clone $bStart)->modify('+2 months'); if ($cur < $bEnd) break; $idx++; $bStart=$bEnd; }
  return $idx;
}
function flash($type, $text){ $_SESSION['msg'] = ['type'=>$type,'text'=>$text]; }
function display_flash(){
  if (function_exists('display_msg')) { echo display_msg(); return; }
  if (!empty($_SESSION['msg'])) {
    $m = $_SESSION['msg']; unset($_SESSION['msg']);
    $txt = is_string($m) ? $m : ($m['text'] ?? '');
    $cls = 'info';
    if (is_array($m) && isset($m['type'])) {
      $cls = $m['type']==='success'?'success':($m['type']==='danger'?'danger':'info');
      $txt = $m['text'] ?? $txt;
    }
    echo '<div class="alert alert-'.$cls.'">'.$txt.'</div>';
  }
}
function get_distinct_tags(): array {
  $rows = find_by_sql("SELECT DISTINCT tag FROM calendar_rotation WHERE tag IS NOT NULL AND tag<>'' ORDER BY tag");
  $out=[]; foreach($rows as $r){ $out[] = $r['tag']; } return $out;
}

/* -------------------- Router AJAX: eventos -------------------- */
$action = $_GET['action'] ?? '';
if ($action === 'events') {
  $start = $_GET['start'] ?? '';
  $end   = $_GET['end'] ?? '';
  $start = $start ? date('Y-m-d', strtotime($start)) : date('Y-m-01');
  $end   = $end   ? date('Y-m-d', strtotime($end))   : date('Y-m-t');

  $rows = find_by_sql("
    SELECT id, technician, activity, start_date, end_date, color, tag
    FROM calendar_rotation
    WHERE start_date >= '{$start}' AND start_date < '{$end}'
    ORDER BY start_date, id
  ");

  $events = [];
  foreach ($rows as $r) {
    $end_plus = date('Y-m-d', strtotime(($r['end_date'] ?? $r['start_date']).' +1 day')); // end exclusivo
    $grp = 'A';
    if (stripos((string)$r['technician'], 'grupo b') !== false) $grp = 'B';
    $events[] = [
      'id'    => $r['id'],
      'title' => ($r['activity'] ?? ''),
      'start' => $r['start_date'],
      'end'   => $end_plus,
      'color' => $r['color'],
      'allDay'=> true,
      'extendedProps' => [
        'tech' => (string)$r['technician'],
        'tag'  => (string)($r['tag'] ?? ''),
        'grp'  => $grp,
      ],
    ];
  }
  header('Content-Type: application/json; charset=utf-8');
  echo j($events); exit;
}

/* -------------------- POST: Eliminar Rotación -------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete-rotation'])) {
  $d_start = $_POST['del_start'] ?? '';
  $d_end   = $_POST['del_end']   ?? '';
  $d_tag   = trim($_POST['del_tag'] ?? '');

  if ($d_start === '' && $d_end === '' && $d_tag === '') {
    flash('danger','Debes indicar al menos un criterio (rango de fechas y/o tag) para eliminar.');
    header('Location: job-rotation.php'); exit;
  }

  $where = [];
  if ($d_start !== '') $where[] = "start_date >= '".e($d_start)."'";
  if ($d_end   !== '') $where[] = "start_date <= '".e($d_end)."'";
  if ($d_tag   !== '') $where[] = "tag = '".e($d_tag)."'";

  $sql = "DELETE FROM calendar_rotation WHERE ".implode(' AND ', $where);
  $GLOBALS['db']->query($sql);

  flash('success','Rotación eliminada correctamente.');
  header('Location: job-rotation.php'); exit;
}

/* -------------------- POST: Generar / Actualizar Rotación -------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['generate-rotation']) || isset($_POST['update-rotation']))) {
  // 1) Parámetros
  $start_date = $_POST['start_date'] ?? date('Y-m-d');
  $modeSel    = $_POST['mode']       ?? '2w';  // '2w' (quincenal + all-hands), '1w','3w','4w' alternancias
  $horizon    = $_POST['horizon']    ?? '8w';  // 8w, 12w, 6m, 12m
  $tag        = $_POST['tag']        ?? '';

  // 2) Normalizar inicio al próximo domingo
  $start_date = next_sunday($start_date);

  // 3) Calcular fin
  switch ($horizon) {
    case '12w': $end_date = date('Y-m-d', strtotime($start_date.' +12 weeks')); break;
    case '6m':  $end_date = date('Y-m-d', strtotime($start_date.' +6 months')); break;
    case '12m': $end_date = date('Y-m-d', strtotime($start_date.' +12 months')); break;
    default:    $end_date = date('Y-m-d', strtotime($start_date.' +8 weeks'));  break;
  }

  // 4) Equipos
  $grupoA = [
    'nombre'     => 'Grupo A',
    'tecnicos'   => ['Wilson Martínez','Jonathan Vargas','Rony Vargas','Rafi Leocadio'],
    'dc'         => 'Frandy Espinal',
    'supervisor' => 'Diana Vázquez',
  ];
  $grupoB = [
    'nombre'     => 'Grupo B',
    'tecnicos'   => ['Rafael Reyes','Darielvy Félix','Jordany Almonte','Melvin Castillo'],
    'dc'         => 'Arturo Santana',
    'supervisor' => 'Víctor Mercedes',
  ];

  // Parejas 16:00 / 18:00 (incluye la regla solicitada: Rafael y Darielvy separados)
  $parejas = [
    'Grupo A' => [
      ['Wilson Martínez','Jonathan Vargas'],
      ['Rony Vargas','Rafi Leocadio'],
    ],
    'Grupo B' => [
      ['Rafael Reyes','Jordany Almonte'],  // B1
      ['Darielvy Félix','Melvin Castillo'], // B2
    ],
  ];

  // Mapa de días: 0=Dom,1=Lun,2=Mar,3=Mié,4=Jue,5=Vie,6=Sáb
  $ALLHANDS_W = 3; // Miércoles siempre All-Hands
  $colorDM = '#0d6efd'; // azul  -> quien cubre Dom–Mié
  $colorMS = '#198754'; // verde -> quien cubre Mié–Sáb
  $colorALL = '#6f42c1'; // morado (resaltar all-hands)

  // 5) Si es "Actualizar", primero borra el rango/tag y luego regenera
  if (isset($_POST['update-rotation'])) {
    $GLOBALS['db']->query("
      DELETE FROM calendar_rotation
      WHERE start_date >= '".e($start_date)."' AND start_date < '".e($end_date)."'".
      ($tag !== '' ? " AND tag='".e($tag)."'" : '')
    );
  }

  // Helper de inserción de un evento
  $insert_day = function(string $grupo, string $color, string $fecha) use ($parejas, $tag){
    $w = (int)date('w', strtotime($fecha));
    $p1 = $parejas[$grupo][0];
    $p2 = $parejas[$grupo][1];
    $out16 = ($w % 2 === 0) ? $p1 : $p2; // alterna por paridad del día
    $out18 = ($w % 2 === 0) ? $p2 : $p1;
    $p16 = abbr_pair($out16);
    $p18 = abbr_pair($out18);
    $franja = ($grupo === 'Grupo A') ? 'Dom–Mié' : 'Mié–Sáb';
    $title = shortify(sprintf('%s · %s | 16:%s | 18:%s', $grupo, $franja, $p16, $p18), 120);
    $tech  = $grupo;

    $sql = sprintf(
      "INSERT INTO calendar_rotation (technician, activity, start_date, end_date, color, tag)
       VALUES ('%s','%s','%s','%s','%s','%s')",
      e($tech), e($title), e($fecha), e($fecha), e($color), e($tag)
    );
    $GLOBALS['db']->query($sql);
  };

  // 6) Periodicidad de swap según modo
  $periodDays = 14; // default 2w
  if ($modeSel === '1w') $periodDays = 7;
  if ($modeSel === '3w') $periodDays = 21;
  if ($modeSel === '4w') $periodDays = 28;

  // 7) Generación día por día
  $cur = $start_date;
  while ($cur < $end_date) {
    $w = (int)date('w', strtotime($cur)); // 0..6
    // bloque de rotación y swap
    $days_from_start = (new DateTime($start_date))->diff(new DateTime($cur))->days;
    $block = intdiv($days_from_start, $periodDays);
    $swap  = ($block % 2 === 1);

    $isAll = ($w === $ALLHANDS_W);

    if ($isAll) {
      // Siempre ambos grupos (resaltados con color ALL)
      $insert_day('Grupo A', $colorALL, $cur);
      $insert_day('Grupo B', $colorALL, $cur);
    } else {
      if (!$swap) {
        // Bloque par: A = Dom–Mar (0..2), B = Jue–Sáb (4..6)
        if ($w >= 0 && $w <= 2) { $insert_day('Grupo A', $colorDM, $cur); }
        elseif ($w >= 4 && $w <= 6) { $insert_day('Grupo B', $colorMS, $cur); }
      } else {
        // Bloque impar (swap): A = Jue–Sáb, B = Dom–Mar
        if ($w >= 0 && $w <= 2) { $insert_day('Grupo B', $colorDM, $cur); }
        elseif ($w >= 4 && $w <= 6) { $insert_day('Grupo A', $colorMS, $cur); }
      }
    }

    $cur = date('Y-m-d', strtotime($cur.' +1 day'));
  }

  $msg = isset($_POST['update-rotation']) ? 'Rotación actualizada correctamente.' : 'Rotación generada correctamente.';
  flash('success', $msg);
  header('Location: job-rotation.php'); exit;
}

/* -------------------- UI -------------------- */
$tags = get_distinct_tags();
include_once('../components/header.php');
?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Rotación Laboral</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
        <li class="breadcrumb-item">Páginas</li>
        <li class="breadcrumb-item active">Rotación Laboral</li>
      </ol>
    </nav>
  </div>

  <div class="col-md-8">
    <?php display_flash(); ?>
  </div>

  <section class="section">
    <div class="row gy-4">

      <!-- Generar / Actualizar -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <h5 class="card-title mb-3">Generar / Actualizar Rotación</h5>
            <form class="row gy-2 gx-3 align-items-end" action="job-rotation.php" method="post" onsubmit="return confirmGenUpd(this);">
              <div class="col-md-3">
                <label for="start_date" class="form-label">Fecha inicio (domingo)</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
              </div>
              <div class="col-md-2">
                <label for="horizon" class="form-label">Horizonte</label>
                <select class="form-select" id="horizon" name="horizon" required>
                  <option value="8w" selected>8 semanas</option>
                  <option value="12w">12 semanas</option>
                  <option value="6m">6 meses</option>
                  <option value="12m">12 meses</option>
                </select>
              </div>
              <div class="col-md-2">
                <label for="mode" class="form-label">Cadencia swap</label>
                <select class="form-select" id="mode" name="mode" required>
                  <option value="2w" selected>Quincenal (2 semanas)</option>
                  <option value="1w">Semanal (1 semana)</option>
                  <option value="3w">Trisemanal (3 semanas)</option>
                  <option value="4w">Mensual (4 semanas)</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="tag" class="form-label">Tag (opcional)</label>
                <input type="text" class="form-control" id="tag" name="tag" placeholder="ej. Q1-2026">
              </div>
              <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill" name="generate-rotation">Generar</button>
                <button type="submit" class="btn btn-outline-warning flex-fill" name="update-rotation">Actualizar</button>
              </div>
            </form>

            <hr class="my-4">

            <h6 class="mb-2">Tags existentes</h6>
            <?php if (count($tags)): ?>
              <div class="d-flex flex-wrap gap-2">
                <?php foreach($tags as $t): ?>
                  <span class="badge rounded-pill text-bg-secondary"><?php echo htmlspecialchars($t); ?></span>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-muted">No hay tags registrados aún.</p>
            <?php endif; ?>

          </div>
        </div>
      </div>

      <!-- Eliminar -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <h5 class="card-title mb-3 text-danger">Eliminar Rotación</h5>
            <form class="row gy-2 gx-3 align-items-end" action="job-rotation.php" method="post" onsubmit="return confirmDeleteRot();">
              <div class="col-md-3">
                <label class="form-label">Inicio (opcional)</label>
                <input type="date" class="form-control" name="del_start">
              </div>
              <div class="col-md-3">
                <label class="form-label">Fin (opcional)</label>
                <input type="date" class="form-control" name="del_end">
              </div>
              <div class="col-md-3">
                <label class="form-label">Tag (opcional)</label>
                <select class="form-select" name="del_tag">
                  <option value="">—</option>
                  <?php foreach($tags as $t): ?>
                    <option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-outline-danger ms-auto" name="delete-rotation">Eliminar</button>
              </div>
            </form>
            <p class="small text-muted mt-2">Puedes eliminar por rango de fechas, por tag, o combinando ambos. Al menos uno es obligatorio.</p>

          </div>
        </div>
      </div>

      <!-- Calendario -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-1">Calendario</h5>
            <div id="calendar"></div>
          </div>
        </div>
      </div>

      <!-- Modal detalle legible -->
      <div class="modal fade" id="rotDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Detalle de la rotación</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <div class="fs-5" id="rotDate"></div>
              <hr class="my-2">
              <pre id="rotText" class="mb-0" style="white-space: pre-wrap; font-size: 1.05rem;"></pre>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</main>

<!-- FullCalendar -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js"></script>

<script>
function confirmGenUpd(form){
  const isUpdate = form.querySelector('[name="update-rotation"]:focus');
  const ini = form.start_date.value || '';
  if(!ini){ alert('Selecciona fecha de inicio.'); return false; }
  if(isUpdate){ return confirm('Esto ACTUALIZARÁ (sobrescribirá) la rotación en el rango elegido (y tag si lo pusiste). ¿Continuar?'); }
  return true;
}
function confirmDeleteRot(){
  const f = document.querySelector('form[action="job-rotation.php"][onsubmit*="confirmDeleteRot"]');
  const a = f.del_start.value.trim();
  const b = f.del_end.value.trim();
  const t = f.del_tag.value.trim();
  if(!a && !b && !t){
    alert('Debes indicar al menos un criterio: rango de fechas y/o tag.');
    return false;
  }
  return confirm('¿Eliminar la rotación seleccionada? Esta acción no se puede deshacer.');
}

document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(el, {
    locale: 'es',
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    buttonText: { today:'Hoy', month:'Mes', week:'Semana', list:'Lista' },
    dayMaxEventRows: 3,
    eventDisplay: 'block',
    displayEventTime: false,

    events: 'job-rotation.php?action=events',

    eventDidMount: function(arg){
      if (arg.event && arg.event.title) {
        arg.el.setAttribute('title', arg.event.title);
      }
    },
    eventClick: function(info){
      const e = info.event;
      const modal  = document.getElementById('rotDetail');
      const rotTxt = document.getElementById('rotText');
      const rotDate= document.getElementById('rotDate');

      const start = e.start;
      const fmt = start ? start.toLocaleDateString('es-DO', {weekday:'long', year:'numeric', month:'long', day:'numeric'}) : '';

      rotDate.textContent = fmt;
      // Mostrar “Grupo A/B - título completo” más tag si existe
      const grp = (e.extendedProps?.grp === 'B') ? 'Grupo B' : 'Grupo A';
      const tag = e.extendedProps?.tag ? '  ['+e.extendedProps.tag+']' : '';
      rotTxt.textContent = grp + ' — ' + (e.title || '(sin título)') + tag;

      const bs = new bootstrap.Modal(modal);
      bs.show();
    }
  });
  calendar.render();
});
</script>

<?php include_once('../components/footer.php'); ?>
