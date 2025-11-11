<?php
namespace TECWEB\MYAPI;

class DataBase {
    protected $conexion;

    public function __construct($user = 'root', $pass = 'diegord17', $db = 'marketzone') {
        $this->conexion = new \mysqli('localhost', $user, $pass, $db);

        if ($this->conexion->connect_error) {
            die('Error de conexión: ' . $this->conexion->connect_error);
        }
    }
}
?>
