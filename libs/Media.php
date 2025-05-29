<?php

class Media {
  public $upload_dir;
  public $errors = [];

  public function __construct() {
    $this->upload_dir = dirname(__DIR__) . '/uploads/products/';
    $this->ensure_upload_dir();
  }

  // Verifica que la carpeta exista y sea escribible
  private function ensure_upload_dir() {
    if (!file_exists($this->upload_dir)) {
      mkdir($this->upload_dir, 0755, true);
    }
    if (!is_writable($this->upload_dir)) {
      $this->errors[] = "La carpeta de destino no tiene permisos de escritura.";
    }
  }

  public function upload($file) {
    if (!$file || empty($file['name'])) {
      $this->errors[] = "No se seleccionó ningún archivo.";
      return false;
    }

    if ($file['error'] !== 0) {
      $this->errors[] = "Error al subir el archivo. Código: " . $file['error'];
      return false;
    }

    $filename = basename($file['name']);
    $target_path = $this->upload_dir . $filename;
    $file_type = $file['type'];

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
      $this->errors[] = "No se pudo mover el archivo.";
      return false;
    }

    // Guardar en la base de datos
    global $db;
    $filename_escaped = $db->escape($filename);
    $file_type_escaped = $db->escape($file_type);

    $query = "INSERT INTO media (file_name, file_type) VALUES ('{$filename_escaped}', '{$file_type_escaped}')";
    if (!$db->query($query)) {
      $this->errors[] = "Error al guardar en la base de datos.";
      return false;
    }

    return true;
  }

  public function process_media() {
    return empty($this->errors);
  }
}
