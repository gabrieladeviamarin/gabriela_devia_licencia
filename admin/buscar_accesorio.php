<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
    echo json_encode(['error' => 'No se encontro el codigo de barras']);
    exit();
}

$codigo = $_GET['codigo'];

$sql = $con->prepare("SELECT * FROM accesorio WHERE codigo_barras = ?");
$sql->bindParam(1, $codigo);
$sql->execute();
$accesorio = $sql->fetch(PDO::FETCH_ASSOC);

if ($accesorio) {
    echo json_encode(['id_accesorio' => $accesorio['Id_accesorio']]);
} else {
    echo json_encode(['codigo' => $codigo]);
}
?>