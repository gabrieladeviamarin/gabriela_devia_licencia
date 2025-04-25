<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['eliminar'])) {
    $licencia = $_POST['licencia'];

    $sql = $con->prepare("DELETE FROM tipo_licencia WHERE id_tipo_licencia = $licencia");
    $sql->execute();
    header("Location: licencias.php");
    exit();
}

$sql = $con->prepare("SELECT * FROM tipo_licencia t");
$sql->execute();
$licencias = $sql->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
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
                        <a class="nav-link active" href="index.php">Empresas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="licencias.php">Licencias</a>
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
            <h2>Licencias registradas</h2>
            <a href="agg_licencia.php" class="btn "><i class="bi bi-plus-circle"></i> Agregar Licencia</a>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nombre Licencia</th>
                    <th>Descripcion</th>
                    <th>Tipo de duracion</th>
                    <th>Tiempo de duracion</th>
                    <th>Precio</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($licencias) > 0): ?>
                    <?php foreach($licencias as $licencia): ?>
                        <tr>
                            <td><?php echo $licencia['nombre_tipo_licencia']; ?></td>
                            <td><?php echo $licencia['descripcion']; ?></td>
                            <td><?php echo $licencia['tipo_duracion'];?></td>
                            <td><?php echo $licencia['duracion'];?></td>
                            <td><?php echo $licencia['precio']; ?></td>
                            <td class="text-center">
                                <a href="editar_licencia.php?id=<?php echo $licencia['id_tipo_licencia']; ?>" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i></a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="licencia" value="<?php echo $licencia['id_tipo_licencia']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta licencia?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay licencias registradas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>