<?php
  $page_title = 'Rotacion Laboral';
  $ropln = 'show';
  require_once('../config/load.php');

  // Manejo de los formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['save-calendar'])) {
      include('../database/calendar/job-rotation/save-calendar.php');
  } elseif (isset($_POST['update-calendar'])) {
      include('../database/calendar/job-rotation/update-calendar.php');
  } elseif (isset($_POST['delete-calendar'])) {
      include('../database/calendar/job-rotation/delete-calendar.php');
  }
}
?>

<?php page_require_level(3); ?>
<?php include_once('../components/header.php');  ?>
<main id="main" class="main">

<div class="pagetitle">
  <h1>Rotacion Laboral</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="home.php">Home</a></li>
      <li class="breadcrumb-item">Paginas</li>
      <li class="breadcrumb-item active">Rotacion Laboral</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<div class="col-md-4"> <?php echo display_msg($msg); ?> </div>

<section class="section">
  <div class="row">
    
  <form class="row" action="job-rotation.php" method="post">
    
  <div class="col-lg-10">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"></h5>

        <div class="" id='calendar'></div>

      </div>
    </div>
  </div>

  <?php 
    $current_user = current_user(); 
    $modal_display = ($current_user['user_level'] < 3) ? 'none' : 'block'; 
  ?>

  <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true" style="display: <?php echo $modal_display; ?>;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Rotacion Laboral</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
        <div class="col-md-12">
          <label for="Tec" class="form-label">Tecnico</label>
          <input type="text" class="form-control" name="Tec" id="Tec" placeholder="Tecnico" required>
        </div>
        <div class="col-md-12">
          <label for="Act" class="form-label">Actividad</label>
          <input type="text" class="form-control" name="Act" id="Act" placeholder="Actividad" required>
        </div>
        <div class="col-md-12">
          <label for="FecIni" class="form-label">Fecha Inicio</label>
          <input type="date" class="form-control" name="FecIni" id="FecIni" required>
        </div>
        <div class="col-md-12">
          <label for="FecFin" class="form-label">Fecha Final</label>
          <input type="date" class="form-control" name="FecFin" id="FecFin" required>
        </div>
        <div class="col-md-12">
          <label for="ColPic" class="form-label">Selector de Color</label>
          <input type="color" class="form-control form-control-color" name="ColPic" id="ColPic" required>
        </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary" name="save-calendar">Guardar cambios</button>
      </div>
    </div>
  </div>
  </div>

  </form>

  <div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="updateForm" action="job-rotation.php" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Actualizar Rotacion Laboral</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-12">
              <label for="Tecnico" class="form-label">Tecnico</label>
              <input type="hidden" id="event-id" name="event-id">
              <input type="text" class="form-control" name="Tecnico" id="Tecnico" placeholder="Tecnico">
            </div>
            <div class="col-md-12">
              <label for="Actividad" class="form-label">Actividad</label>
              <input type="text" class="form-control" name="Actividad" id="Actividad" placeholder="Actividad">
            </div>
            <div class="col-md-12">
              <label for="Inicio" class="form-label">Fecha Inicio</label>
              <input type="date" class="form-control" name="Inicio" id="Inicio">
            </div>
            <div class="col-md-12">
              <label for="Final" class="form-label">Fecha Final</label>
              <input type="date" class="form-control" name="Final" id="Final">
            </div>
            <div class="col-md-12">
              <label for="Color" class="form-label">Selector de Color</label>
              <input type="color" class="form-control form-control-color" name="Color" id="Color">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" name="delete-calendar"onclick="openModalDelete()">Eliminar</button>
          <button type="submit" class="btn btn-primary" name="update-calendar">Actualizar cambios</button>
        </div>
      </form> <!-- Close the form tag -->
    </div>
  </div>
</div>

   <!-- Modal -->
   <div class="modal fade" id="ModalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <form class="modal-content text-center" action="job-rotation.php" method="post">
        <div class="modal-header d-flex justify-content-center">
          <h5>Estás seguro?</h5>
        </div>
        <div class="modal-body">
          <input type="hidden" name="event-id" id="delete-event-id">
          <button class="btn btn-secondary" onclick="closeModalDelete()">No</button>
          <button class="btn btn-outline-danger" name="delete-calendar" onclick="confirmDelete()">Si</button>
      </div>
    </form>
  </div>
  </div>
  <!-- End Modal -->

  </div>
</section>

</main><!-- End #main -->

<script>
  window.addEventListener('click', function(event) {
    var modal = document.getElementById('eventModal');
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  });
</script>

<script>
 document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    locale: 'es',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          list: 'Lista'
        },
    events: [
      <?php $Serch = find_by_sql("SELECT * FROM calendar WHERE Rotation = 'Rotation' "); ?>
      <?php foreach ($Serch as $event): ?>
        {
          id: '<?php echo $event['id']; ?>',
          title: '<?php echo $event['Technician'] . ' - ' . $event['Activity']; ?>',
          start: '<?php echo $event['Start_Date']; ?>',
          end: '<?php echo $event['End_Date']; ?>',
          color: '<?php echo $event['Color']; ?>',
          extendedProps: {
            Technician: '<?php echo $event['Technician']; ?>',
            Activity: '<?php echo $event['Activity']; ?>',
            Start_Date: '<?php echo $event['Start_Date']; ?>',
            End_Date: '<?php echo $event['End_Date']; ?>',
            Color: '<?php echo $event['Color']; ?>',
          }
        },
      <?php endforeach; ?>
    ],

    eventClick: function (info) {

      const event = info.event;

      $('#updateModal #Tecnico').val(event.extendedProps.Technician);
      $('#updateModal #Actividad').val(event.extendedProps.Activity);
      $('#updateModal #Inicio').val(event.extendedProps.Start_Date);
      $('#updateModal #Final').val(event.extendedProps.End_Date);
      $('#updateModal #Color').val(event.extendedProps.Color);
      $('#updateModal #event-id').val(event.id);
      $('#delete-event-id').val(event.id);
      $('#updateModal').modal('show');
    },

    dateClick: function (info) {
      $('#eventModal #Tec').val('');
      $('#eventModal #Act').val('');
      $('#eventModal #FecIni').val(info.dateStr);
      $('#eventModal #FecFin').val('');
      $('#eventModal #ColPic').val('#000000');

      $('#eventModal').modal('show');
    }
  });
  calendar.render();
 });

 function openModalDelete() {

        $('#updateModal').modal('hide');
        
        $('#ModalDelete').modal('show');
    }

    function closeModalDelete() {
        $('#ModalDelete').modal('hide');
    }

    function confirmDelete() {
      $.ajax({
        type: "POST",
        url: '../database/calendar.php',
        data: { id: event.id },
        success: function (datos) {
          document.getElementById("updateForm").submit();
        }
    });
      closeModalDelete();
    }
</script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

<?php include_once('../components/footer.php');  ?>