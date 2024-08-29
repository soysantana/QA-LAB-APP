<?php
require_once(LIB_PATH_INC.DS."db-config.php");

class MySqli_DB {

    private $con;
    public $query_id;

    function __construct() {
        $this->db_connect();
    }

    /*--------------------------------------------------------------*/
    /* Function for Open database connection
    /*--------------------------------------------------------------*/
    public function db_connect() {
        // Usa un bloque try-catch para manejar excepciones
        try {
            $this->con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // Verifica la conexión
            if ($this->con->connect_error) {
                throw new Exception("Database connection failed: " . $this->con->connect_error);
            }
        } catch (Exception $e) {
            // Maneja el error de conexión aquí
            error_log($e->getMessage()); // Registra el error en el log del servidor
            // Muestra un mensaje genérico o redirige a una página de error
            echo 'Lo sentimos, no podemos conectar a la base de datos en este momento. Por favor, inténtelo más tarde.';
            exit; // Asegúrate de detener la ejecución después de mostrar el mensaje
        }
    }

    /*--------------------------------------------------------------*/
    /* Function for Close database connection
    /*--------------------------------------------------------------*/
    public function db_disconnect() {
        if (isset($this->con)) {
            $this->con->close();
            unset($this->con);
        }
    }

    /*--------------------------------------------------------------*/
    /* Function for mysqli query
    /*--------------------------------------------------------------*/
    public function query($sql) {
        if (trim($sql) != "") {
            $this->query_id = $this->con->query($sql);
        }
        if (!$this->query_id) {
            // Solo para el modo de desarrollo
            // die("Error en esta consulta :<pre> " . $sql ."</pre>");
            // Para el modo de producción
            error_log("Error en la consulta: " . $this->con->error); // Registra el error en el log del servidor
            die("Error en la consulta. Por favor, inténtelo más tarde.");
        }

        return $this->query_id;
    }

    /*--------------------------------------------------------------*/
    /* Function for Query Helper
    /*--------------------------------------------------------------*/
    public function fetch_array($statement) {
        return $statement->fetch_array();
    }
    public function fetch_object($statement) {
        return $statement->fetch_object();
    }
    public function fetch_assoc($statement) {
        return $statement->fetch_assoc();
    }
    public function num_rows($statement) {
        return $statement->num_rows;
    }
    public function insert_id() {
        return $this->con->insert_id;
    }
    public function affected_rows() {
        return $this->con->affected_rows;
    }

    /*--------------------------------------------------------------*/
    /* Function for Remove escapes special
    /* characters in a string for use in an SQL statement
    /*--------------------------------------------------------------*/
    public function escape($str) {
        return $this->con->real_escape_string($str ?? '');
    }

    /*--------------------------------------------------------------*/
    /* Function for while loop
    /*--------------------------------------------------------------*/
    public function while_loop($loop) {
        $results = array();
        while ($result = $this->fetch_array($loop)) {
            $results[] = $result;
        }
        return $results;
    }
}

$db = new MySqli_DB();

?>
