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
    $documento = $_POST['documento'];

    $sql = $con->prepare("DELETE FROM usuario WHERE documento = ?");
    $sql->execute([$documento]);
    header("Location: usuarios.php");
    exit();
}

$sql = $con->prepare("SELECT u.documento, u.nombre, u.email, e.nombre_empresa, e.id_empresa, r.nombre_rol 
                     FROM usuario u
                     LEFT JOIN empresa e ON u.id_empresa = e.id_empresa
                     LEFT JOIN rol r ON u.Id_rol = r.Id_rol
                     ORDER BY u.nombre ASC");
$sql->execute();
$usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Registrados</title>
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
                        <a class="nav-link" href="index.php">Empresas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="licencias.php">Licencias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="usuarios.php">Usuarios</a>
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
            <h2>Usuarios registrados</h2>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Empresa</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($usuarios) > 0): ?>
                    <?php foreach($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['documento']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['email']; ?></td>
                            <td><?php echo $usuario['nombre_empresa'] ? $usuario['nombre_empresa'] : '<span class="badge bg-secondary">Sin empresa</span>'; ?></td>
                            <td><?php echo $usuario['nombre_rol']; ?></td>
                            <td class="text-center">
                                <a href="editar_admin.php?documento=<?php echo $usuario['documento']; ?>&&nom=<?php echo $usuario['nombre_empresa']?>&&id=<?php echo $usuario['id_empresa']?>" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i></a>
                                <?php if($_SESSION['documento'] != $usuario['documento']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="documento" value="<?php echo $usuario['documento']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este usuario?')"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay usuarios registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>