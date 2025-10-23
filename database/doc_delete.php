<?php
// pages/doc_delete.php
require_once('../config/load.php');
page_require_level(2);

try {
  if (session_status() === PHP_SESSION_NONE) session_start();

  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) throw new Exception('ID inválido');

  // === Helpers ===
  function norm_slashes(string $p): string {
    $p = str_replace('\\','/',$p);
    $p = preg_replace('#/+#','/',$p);
    return $p;
  }
  function is_within(string $child, string $parent): bool {
    $child = norm_slashes($child);
    $parent = rtrim(norm_slashes($parent), '/') . '/';
    return strpos($child, $parent) === 0;
  }
  /**
   * Sube y elimina directorios vacíos hasta llegar a alguna de las raíces permitidas.
   * Nunca borra las raíces.
   */
  function prune_empty_dirs(string $startDir, array $allowedRoots): void {
    $startDir = norm_slashes($startDir);
    foreach ($allowedRoots as $root) {
      $root = rtrim(norm_slashes($root), '/');
    }
    $dir = $startDir;
    while ($dir && $dir !== '/' && $dir !== '.' ) {
      $isAllowedAncestor = false;
      foreach ($allowedRoots as $root) {
        if ($dir === $root || is_within($dir, $root)) { $isAllowedAncestor = true; break; }
      }
      if (!$isAllowedAncestor) break; // fuera de límites

      if (@is_dir($dir)) {
        // ¿Está vacío?
        $items = @scandir($dir);
        if (is_array($items)) {
          $entries = array_values(array_diff($items, ['.','..']));
          if (count($entries) === 0) {
            // borrar y seguir subiendo
            @rmdir($dir);
            $dir = dirname($dir);
            $dir = $dir === '\\' ? '/' : $dir;
            continue;
          }
        }
      }
      break; // tiene algo o no se puede leer; paramos
    }
  }

  // === Buscar file_path del doc ===
  $res = find_by_sql("SELECT file_path FROM doc_files WHERE id={$id} LIMIT 1");
  if (!$res || empty($res[0]['file_path'])) {
    // Aun si no hay file_path, borrar el registro para no dejar basura
    $db->query("DELETE FROM doc_files WHERE id={$id} LIMIT 1");
    $session->msg('w','Documento sin ruta; registro eliminado.');
    header('Location: /pages/docs_list.php'); exit;
  }

  $webPath = trim($res[0]['file_path']);
  $webPath = norm_slashes($webPath);
  if ($webPath !== '' && $webPath[0] === '/') $webPath = substr($webPath, 1); // quitar slash inicial

  // === Construir ruta absoluta segura dentro del proyecto ===
  $PROJECT_ROOT = norm_slashes(realpath(dirname(__DIR__,1)) ?: dirname(__DIR__,1));
  $abs = norm_slashes($PROJECT_ROOT . '/' . $webPath);

  // Rutas base permitidas donde sí vamos a limpiar subcarpetas vacías
  $allowedRoots = [
    $PROJECT_ROOT . '/uploads',
    $PROJECT_ROOT . '/storage/pdfs',
    $PROJECT_ROOT . '/storage/backups',
  ];
  $allowedRoots = array_map('norm_slashes', $allowedRoots);

  // Validar que el path absoluto esté dentro del proyecto
  if (!is_within($abs, $PROJECT_ROOT)) {
    throw new Exception('Ruta fuera del proyecto: operación bloqueada.');
  }

  // === Borrar el archivo físico (si existe) ===
  $deletedFile = false;
  if (is_file($abs)) {
    // Guardar carpeta padre para posible limpieza
    $parentDir = norm_slashes(dirname($abs));
    if (!@unlink($abs)) {
      throw new Exception('No se pudo eliminar el archivo físico.');
    }
    $deletedFile = true;

    // Limpiar directorios vacíos hacia arriba sin salir de los roots permitidos
    prune_empty_dirs($parentDir, $allowedRoots);
  } else {
    // No existe el archivo físico, continuamos con la eliminación del registro
    $deletedFile = false;
  }

  // === Eliminar registro en DB ===
  $db->query("DELETE FROM doc_files WHERE id={$id} LIMIT 1");

  if ($deletedFile) {
    $session->msg('s','Documento y archivo físico eliminados del hosting.');
  } else {
    $session->msg('w','Registro eliminado. El archivo físico no existía (ya había sido removido).');
  }

} catch (Throwable $e) {
  $session->msg('d','Error: '.$e->getMessage());
}

header('Location: /pages/docs_list.php'); 
exit;
