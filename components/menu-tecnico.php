<?php

require_once dirname(__DIR__) . '/config/load.php';
page_require_level(3);



function current_path(): string
{
    $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $path = rtrim($uri, '/');
    return $path === '' ? '/' : $path;
}

function is_active_href(string $href): bool
{
    $here   = current_path();
    $target = rtrim($href, '/') ?: '/';
    return $here === $target;
}

function user_has_role(?array $roles = null): bool
{
    if ($roles === null || $roles === []) return true;
    $user  = current_user();
    $level = $user['user_level'] ?? null;
    return in_array($level, $roles, true);
}

// Mini helper para definir items
function nav_item(string $href, string $label, string $icon = 'bi-circle', array $extra = []): array
{
    return array_merge([
        'type'  => 'item',
        'href'  => $href,
        'label' => $label,
        'icon'  => $icon,
    ], $extra);
}

// Render de item
function render_nav_item(array $item): string
{
    $href   = htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8');
    $icon   = htmlspecialchars($item['icon'] ?? 'bi-circle', ENT_QUOTES, 'UTF-8');
    $label  = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
    $extra  = $item['extra_class'] ?? '';
    $extra  = $extra ? ' ' . htmlspecialchars($extra, ENT_QUOTES, 'UTF-8') : '';
    $active = is_active_href($item['href']) ? ' active' : '';

    $cls = 'nav-link d-flex align-items-center gap-2' . $active . $extra;

    return <<<HTML
    <li class="nav-item">
      <a class="{$cls}" href="{$href}">
        <i class="bi {$icon}"></i>
        <span>{$label}</span>
      </a>
    </li>
HTML;
}


$MENU_TOP = [

    nav_item('/pages/home.php',                'Panel',               'bi-speedometer2'),

    nav_item('/components/menu_requisicion.php',    'Requisición',         'bi-file-earmark-plus'),

    nav_item('/components/menu_seguimiento.php',     'Seguimiento','bi-kanban'),

   

    // Hub de ensayos: desde aquí tú muestras tarjetas/links a Atterberg, GS, etc.
    nav_item('/components/menu_hojasdetrabajos.php', 'Ensayos de laboratorio', 'bi-flask' ),

    // Hub de reportes: desde aquí puedes enlazar diario, semanal, etc.
    nav_item('/components/menu_reporte_diario.php', 'Reportes',       'bi-bar-chart'),

    // Hub de inventario
    nav_item('/components/menu_inventarios.php',    'Inventario',     'bi-box-seam'),

   
];



$MENU_BOTTOM = [
    nav_item('/components/menu_configuracion.php', 'Configuración', 'bi-gear'),
];

?>
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav d-flex flex-column h-100" id="sidebar-nav">

    <?php
    // Parte superior: hubs
    foreach ($MENU_TOP as $node) {
        if (isset($node['roles']) && !user_has_role($node['roles'])) continue;
        echo render_nav_item($node);
    }

    // Espaciador flexible para empujar Perfil/Admin al fondo
    echo '<li class="flex-grow-1"></li>';

    // Parte inferior: perfil / administración
    foreach ($MENU_BOTTOM as $node) {
        if (isset($node['roles']) && !user_has_role($node['roles'])) continue;
        echo render_nav_item($node);
    }
    ?>
  </ul>
</aside>

<style>
/* Toque moderno básico (puedes mover esto a tu CSS global si quieres) */
.sidebar .nav-link {
  border-radius: .75rem;
  padding: .55rem .85rem;
  font-size: 0.9rem;
}
.sidebar .nav-link i {
  font-size: 1rem;
}
.sidebar .nav-link.active {
  background: rgba(13, 110, 253, 0.1); /* primary suave */
  color: #0d6efd;
}
.sidebar .nav-link.active i {
  color: #0d6efd;
}
.sidebar {
  padding-top: .75rem;
  padding-bottom: .75rem;
}
</style>
