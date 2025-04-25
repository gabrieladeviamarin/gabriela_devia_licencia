<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener el id_empresa del usuario actual
$sql_empresa = $con->prepare("SELECT id_empresa FROM usuario WHERE documento = ?");
$sql_empresa->execute([$_SESSION['documento']]);
$empresa = $sql_empresa->fetch(PDO::FETCH_ASSOC);
$id_empresa = $empresa['id_empresa'];

// Consulta para obtener los materiales de la empresa
$sql = $con->prepare("SELECT id_material, nombre_material FROM material WHERE id_empresa = ? ORDER BY nombre_material ASC");
$sql->execute([$id_empresa]);
$materiales = $sql->fetchAll(PDO::FETCH_ASSOC);

// Eliminar material
if (isset($_POST['eliminar'])) {
    $id_material = $_POST['material'];

    // Verificar si el material está siendo utilizado en algún accesorio
    $sql = $con->prepare("SELECT COUNT(*) as total FROM accesorio WHERE id_material = ?");
    $sql->execute([$id_material]);
    $resultado = $sql->fetch(PDO::FETCH_ASSOC);

    if ($resultado['total'] > 0) {
        echo "<script>alert('No se puede eliminar el material porque está siendo utilizado en accesorios');</script>";
    } else {
        $sql = $con->prepare("DELETE FROM material WHERE id_material = ?");
        $sql->execute([$id_material]);
        echo "<script>alert('Material eliminado correctamente');</script>";
        echo "<script>window.location = 'material.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materiales</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
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
                        <a class="nav-link active" href="material.php">Materiales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tipo_accesorio.php">Tipo de Accesorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php">Usuarios</a>
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
            <h2>Materiales registrados</h2>
            <a href="agg_material.php" class="btn"><i class="bi bi-plus-circle"></i> Agregar Material</a>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Material</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($materiales) > 0): ?>
                    <?php foreach($materiales as $material): ?>
                        <tr>
                            <td><?php echo $material['id_material']; ?></td>
                            <td><?php echo $material['nombre_material']; ?></td>
                            <td class="text-center">
                                <a href="editar_material.php?id=<?php echo $material['id_material']; ?>" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i></a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="material" value="<?php echo $material['id_material']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este material?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay materiales registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>