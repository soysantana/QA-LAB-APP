<?php
require_once('../config/load.php');
page_require_level(2); // Ajusta nivel si solo ciertos roles pueden borrar

try {
  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) throw new Exception('ID inválido.');

  // Buscar registro
  $row = find_by_sql("SELECT file_path FROM doc_files WHERE id={$id} LIMIT 1");
  if (empty($row)) throw new Exception('Documento no encontrado.');

  $fileRel = $row[0]['file_path'] ?? '';
  $root    = realpath(__DIR__ . '/..'); // /pages -> raíz del proyecto
  $abs     = $root . $fileRel;

  // Borrar archivo físico si existe
  if (is_string($fileRel) && $fileRel !== '') {
    if (file_exists($abs)) {
      if (!@unlink($abs)) {
        $session->msg('w', 'No se pudo borrar el archivo del disco, pero se eliminará el registro.');
      }
    }
  }

  // Borrar fila en BD
  $db->query("DELETE FROM doc_files WHERE id={$id} LIMIT 1");

  $session->msg('s', 'Documento eliminado correctamente.');
  header('Location: ../pages/docs_list.php'); exit;

} catch (Throwable $e) {
  $session->msg('d', 'Error: '.$e->getMessage());
  header('Location: ../pages/docs_list.php'); exit;
}
