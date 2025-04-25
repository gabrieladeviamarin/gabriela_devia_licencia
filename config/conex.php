<?php


class Database{      

    private $hostname = "localhost:3307";
    private $user = "root";
    private $passw = "";
    private $bd = "ej_licencia";
    private $charset = "utf8";

    function conectar()
    {

        try{
            $conexion = "mysql:host=". $this->hostname. ";dbname=". $this->bd. ";charset=". $this->charset;

            $option = [
                PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES=>false
            ];

            $pdo = new PDO($conexion,$this->user,$this->passw,$option);
            return $pdo;
        }

        catch(PDOException $e)
        {
            echo"Error de conexion: ". $e->getMessage();
            exit();
        }
    }

}


?>