<?php
/**
 * Sidebar refactor — moderno, DRY y con activo server‑side (Bootstrap 5)
 *
 * - Define la estructura del menú en un solo array ($MENU)
 * - Calcula la clase "active" en el servidor por patrón de URL
 * - Abre/cierra grupos (collapse) automáticamente si un hijo está activo
 * - Soporta íconos Bootstrap (bi ...)
 * - Permite visibilidad por rol mediante 'roles' (opcional)
 * - Sin dependencias JS para activo (el script final es opcional)
 */

require_once dirname(__DIR__) . '/config/load.php';
page_require_level(2); // ajusta según convenga

// =============================
// Helpers
// =============================
function current_path(): string {
  // Ruta como '/pages/home.php'
  $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
  return rtrim($uri, '/');
}

function path_matches(string $path, array $patterns): bool {
  $path = current_path();
  foreach ($patterns as $p) {
    // Patrones pueden ser rutas exactas o expresiones regulares delimitadas por '#'
    if ($p === $path) return true;
    if (strlen($p) > 2 && $p[0] === '#' && substr($p, -1) === '#') {
      if (preg_match($p, $path)) return true;
    } else {
      // Contiene substring
      if (strpos($path, $p) !== false) return true;
    }
  }
  return false;
}

function user_has_role(array $roles = null): bool {
  if ($roles === null || $roles === []) return true; // visible para todos
  // Ajusta esta lógica a tu sistema de roles/grupos
  $user = current_user();
  $ugrp = $user['user_level'] ?? null; // ejemplo
  return in_array($ugrp, $roles, true);
}

function render_nav_link(array $item, bool $isActive): string {
  $href  = $item['href'];
  $icon  = $item['icon'] ?? 'bi-circle';
  $label = $item['label'];
  $extra = $item['extra_class'] ?? '';
  $cls   = 'nav-link' . ($isActive ? ' active' : '') . ($extra ? ' ' . $extra : '');
  return <<<HTML
  <li class="nav-item">
    <a class="$cls" href="$href">
      <i class="bi $icon"></i>
      <span>$label</span>
    </a>
  </li>
HTML;
}

function render_nav_group(array $group, bool $open): string {
  $gid   = $group['id'];
  $icon  = $group['icon'] ?? 'bi-folder';
  $label = $group['label'];
  $itemsHtml = $group['items_html'];
  $linkCls = 'nav-link' . ($open ? '' : ' collapsed');
  $ulCls   = 'nav-content collapse' . ($open ? ' show' : '');
  return <<<HTML
  <li class="nav-item">
    <a class="$linkCls" data-bs-target="#$gid" data-bs-toggle="collapse" href="#">
      <i class="bi $icon"></i><span>$label</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="$gid" class="$ulCls" data-bs-parent="#sidebar-nav">
      $itemsHtml
    </ul>
  </li>
HTML;
}

function render_group_item(array $item, bool $isActive): string {
  $href  = $item['href'];
  $label = $item['label'];
  $icon  = $item['icon'] ?? 'bi-circle';
  $cls   = $isActive ? 'active' : '';
  return <<<HTML
  <li>
    <a href="$href" class="$cls">
      <i class="bi $icon"></i><span>$label</span>
    </a>
  </li>
HTML;
}

// =============================
// Definición del menú (config única)
// patterns: se usan para marcar activo por coincidencia de URL
// roles: restringe visibilidad (opcional)
// =============================
$MENU = [
  // Dashboard
  ['type' => 'item', 'href' => '/pages/home.php', 'label' => 'Panel Control', 'icon' => 'bi-grid', 'patterns' => ['/pages/home.php']],

  // Seguimiento de muestras (grupo)
  ['type' => 'group', 'id' => 'Tracking-nav', 'label' => 'Seguimiento de muestras', 'icon' => 'bi-eye', 'items' => [
    ['href' => '/pages/test-preparation.php',   'label' => 'Ensayos en preparación',  'icon' => 'bi-circle', 'patterns' => ['/pages/test-preparation.php']],
    ['href' => '/pages/test-realization.php',   'label' => 'Ensayos en realización',  'icon' => 'bi-circle', 'patterns' => ['/pages/test-realization.php']],
    ['href' => '/pages/test-delivery.php',      'label' => 'Ensayos en entrega',      'icon' => 'bi-circle', 'patterns' => ['/pages/test-delivery.php']],
    ['href' => '/pages/test-repeat.php',        'label' => 'Ensayos en repetición',   'icon' => 'bi-circle', 'patterns' => ['/pages/test-repeat.php']],
    ['href' => '/pages/test-review.php',        'label' => 'Ensayos en revisión',     'icon' => 'bi-circle', 'patterns' => ['/pages/test-review.php']],
  ]],

  // Inventarios, Bandejas, Hojas
  ['type' => 'item', 'href' => '/components/menu_inventarios.php',  'label' => 'Inventarios',          'icon' => 'bi-box',            'patterns' => ['/components/menu_inventarios.php']],
  ['type' => 'item', 'href' => '/pages/bandejas_descartar.php',     'label' => 'Muestras a Botar',     'icon' => 'bi-trash3',         'patterns' => ['/pages/bandejas_descartar.php']],
  ['type' => 'item', 'href' => '/components/menu_hojastrabajos.php','label' => 'Hojas de Trabajos',    'icon' => 'bi-clipboard-check','patterns' => ['/components/menu_hojastrabajos.php']],

  ['type' => 'heading', 'label' => 'Páginas'],

  // Páginas
  ['type' => 'item', 'href' => '/pages/requisition-form.php', 'label' => 'Formulario de requisición', 'icon' => 'bi-file-earmark', 'patterns' => ['/pages/requisition-form.php']],
  ['type' => 'item', 'href' => '/pages/control_ensayo_concreto.php', 'label' => 'Control Ensayo de Concreto', 'icon' => 'bi-clipboard-data', 'patterns' => ['/pages/control_ensayo_concreto.php']],
  ['type' => 'item', 'href' => '/pages/pendings-list.php', 'label' => 'Lista de Pendientes', 'icon' => 'bi-question-circle', 'patterns' => ['/pages/pendings-list.php']],
  ['type' => 'item', 'href' => '/pages/weekly-planning.php','label' => 'Planificación Semanal', 'icon' => 'bi-calendar3', 'patterns' => ['/pages/weekly-planning.php']],
  ['type' => 'item', 'href' => '/pages/job-rotation.php',   'label' => 'Rotación Laboral',    'icon' => 'bi-calendar2', 'patterns' => ['/pages/job-rotation.php']],

  ['type' => 'heading', 'label' => 'Documentación'],

  // Registro de ensayos (grupo)
  ['type' => 'group', 'id' => 'forms-nav', 'label' => 'Registro de ensayos', 'icon' => 'bi-journal-text', 'items' => [
    ['href' => '/pages/moisture-content-menu.php', 'label' => 'Moisture Content', 'patterns' => ['/pages/moisture-content-menu.php']],
    ['href' => '/pages/atterberg-limit.php',       'label' => 'Atterberg Limit',  'patterns' => ['/pages/atterberg-limit.php']],
    ['href' => '/pages/reactivity-menu.php',       'label' => 'Reactividad',      'patterns' => ['/pages/reactivity-menu.php']],
    ['href' => '/pages/grain-size-menu.php',       'label' => 'Grain Size',       'patterns' => ['/pages/grain-size-menu.php']],
    ['href' => '/pages/specific-gravity-menu.php', 'label' => 'Specific Gravity', 'patterns' => ['/pages/specific-gravity-menu.php']],
    ['href' => '/pages/standard-proctor.php',      'label' => 'Standard Proctor', 'patterns' => ['/pages/standard-proctor.php']],
    ['href' => '/pages/LAA-menu.php',              'label' => 'Los Angeles Abrasion', 'patterns' => ['/pages/LAA-menu.php']],
    ['href' => '/pages/compressive-menu.php',      'label' => 'Compresión',       'patterns' => ['/pages/compressive-menu.php']],
    ['href' => '/pages/dispercion-menu.php',       'label' => 'Dispersión',       'patterns' => ['/pages/dispercion-menu.php']],
    ['href' => '/pages/leeb-hardness.php',         'label' => 'Leeb Hardness',    'patterns' => ['/pages/leeb-hardness.php']],
    ['href' => '/pages/point-Load.php',            'label' => 'Point Load',       'patterns' => ['/pages/point-Load.php']],
    ['href' => '/pages/brazilian.php',             'label' => 'Brazilian',        'patterns' => ['/pages/brazilian.php']],
    ['href' => '/pages/soundness.php',             'label' => 'Soundness',        'patterns' => ['/pages/soundness.php']],
    ['href' => '/pages/densidades-menu.php',       'label' => 'Density',          'patterns' => ['/pages/densidades-menu.php']],
  ]],

  ['type' => 'item', 'href' => '/pages/essay.php',   'label' => 'Ensayos Registrados', 'icon' => 'bi-database',       'patterns' => ['/pages/essay.php']],
  ['type' => 'item', 'href' => '/pages/sumary.php',  'label' => 'Sumarios',            'icon' => 'bi-clipboard-data', 'patterns' => ['/pages/sumary.php']],

  ['type' => 'heading', 'label' => 'Supervisión'],

  ['type' => 'item', 'href' => '/pages/essay-review.php', 'label' => 'Revisión de ensayo', 'icon' => 'bi-card-checklist', 'patterns' => ['/pages/essay-review.php']],
  ['type' => 'item', 'href' => '/pages/rendimiento.php',  'label' => 'Desempeño',         'icon' => 'bi-robot',          'patterns' => ['/pages/rendimiento.php']],
  ['type' => 'item', 'href' => '/pages/detalle-cliente.php','label' => 'Detalles de clientes','icon' => 'bi-info-circle', 'patterns' => ['/pages/detalle-cliente.php']],
  ['type' => 'item', 'href' => '/components/menu_reporte_diario.php','label' => 'Reporte Diario','icon' => 'bi-calendar-event', 'patterns' => ['/components/menu_reporte_diario.php']],
   ['type' => 'item', 'href' => '/pages/docs_list.php','label' => 'Firma de Resultados','icon' => 'bi-calendar-event', 'patterns' => ['/pages/docs_list.php']],

    ['type' => 'heading', 'label' => 'Configuración'],

  ['type' => 'item', 'href' => '/pages/users-profile.php', 'label' => 'Perfil',       'icon' => 'bi-person',    'patterns' => ['/pages/users-profile.php']],
  ['type' => 'item', 'href' => '/pages/users-register.php','label' => 'Nueva cuenta', 'icon' => 'bi-card-list', 'patterns' => ['/pages/users-register.php']],
  ['type' => 'item', 'href' => '/pages/users-group.php',  'label' => 'Usuarios / Grupos', 'icon' => 'bi-people', 'patterns' => ['/pages/users-group.php']],
];

// =============================
// Render
// =============================
ob_start();
?>
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <?php
      $path = current_path();
      foreach ($MENU as $node) {
        if (isset($node['roles']) && !user_has_role($node['roles'])) continue;

        if (($node['type'] ?? 'item') === 'heading') {
          echo '<li class="nav-heading">' . htmlspecialchars($node['label']) . '</li>';
          continue;
        }

        if ($node['type'] === 'item') {
          $isActive = path_matches($path, $node['patterns'] ?? []);
          echo render_nav_link($node, $isActive);
          continue;
        }

        if ($node['type'] === 'group') {
          // Procesar hijos
          $itemsHtml = '';
          $groupActive = false;
          foreach ($node['items'] as $child) {
            if (isset($child['roles']) && !user_has_role($child['roles'])) continue;
            $isActive = path_matches($path, $child['patterns'] ?? []);
            $groupActive = $groupActive || $isActive;
            $itemsHtml .= render_group_item($child, $isActive);
          }
          $node['items_html'] = $itemsHtml !== '' ? $itemsHtml : '<li><span class="text-muted small px-3">(Sin items)</span></li>';
          echo render_nav_group($node, $groupActive);
          continue;
        }
      }
    ?>
  </ul>
</aside>
<?php
$html = ob_get_clean();

echo $html;
?>

<!-- Script opcional: resalta activo por coincidencia estricta del href (fallback) -->
<script>
(function(){
  const here = location.pathname.replace(/\/$/, '');
  document.querySelectorAll('#sidebar-nav a.nav-link').forEach(a => {
    const href = a.getAttribute('href');
    if (!href) return;
    if (here === href.replace(/\/$/, '')) a.classList.add('active');
  });
})();
</script>
