<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}


$sql_empresa = $con->prepare("SELECT id_empresa FROM usuario WHERE documento = ?");
$sql_empresa->execute([$_SESSION['documento']]);
$empresa = $sql_empresa->fetch(PDO::FETCH_ASSOC);
$id_empresa = $empresa['id_empresa'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_accesorio = $_GET['id'];

$sql = $con->prepare("SELECT a.*, m.nombre_material, ta.nombre_tipo 
                      FROM accesorio a
                      LEFT JOIN material m ON a.id_material = m.id_material
                      LEFT JOIN tipo_accesorio ta ON a.id_tipo_accesorio = ta.id_tipo_accesorio
                      WHERE a.Id_accesorio = ? AND a.id_empresa = ?");
$sql->execute([$id_accesorio, $id_empresa]);
$accesorio = $sql->fetch(PDO::FETCH_ASSOC);

if (!$accesorio) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Accesorio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        .barcode-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-4 me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accesorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="escanear.php">Escanear codigo de barras</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../include/exit.php" class="btn btn-danger me-4">Cerrar sesion</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="card p-4 mx-auto" style="max-width: 90%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detalles del Accesorio</h2>
            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Codigo de Barras</h5>
                    </div>
                    <div class="card-body">
                        <img src="../codigos_barras/barcode_<?php echo $accesorio['Id_accesorio']; ?>.png" alt="Código de barras" class="barcode-image mb-3">
                        <p class="fw-bold">ID: <?php echo $accesorio['Id_accesorio']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Informacion del Accesorio</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Caracteristicas:</th>
                                    <td><?php echo $accesorio['caracteristicas']; ?></td>
                                </tr>
                                <tr>
                                    <th>Peso:</th>
                                    <td><?php echo $accesorio['peso']; ?></td>
                                </tr>
                                <tr>
                                    <th>Precio:</th>
                                    <td>$<?php echo number_format($accesorio['precio'], 2); ?></td>
                                </tr>
                                <tr>
                                    <th>Material:</th>
                                    <td><?php echo $accesorio['nombre_material']; ?></td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td><?php echo $accesorio['nombre_tipo']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>