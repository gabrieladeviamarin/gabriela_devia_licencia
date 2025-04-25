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

// Configuración de paginación
$registros_por_pagina = 5;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener total de registros para la paginación (solo usuarios con rol 2)
$sql_total = $con->prepare("SELECT COUNT(*) as total FROM usuario WHERE id_empresa = ? AND id_rol = 2");
$sql_total->execute([$id_empresa]);
$total_registros = $sql_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta con límite para paginación
$sql = $con->prepare("SELECT documento, nombre, email FROM usuario WHERE id_empresa = $id_empresa AND id_rol = 2
                      ORDER BY nombre ASC
                      LIMIT $inicio, $registros_por_pagina");
$sql->execute();
$usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);

// Eliminar usuario
if (isset($_POST['eliminar'])) {
    $documento = $_POST['usuario'];

    $sql = $con->prepare("DELETE FROM usuario WHERE documento = $documento");
    $sql->execute();
    echo "<script>alert('Usuario eliminado correctamente');</script>";
    echo "<script>window.location = 'usuarios.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
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
                        <a class="nav-link" href="material.php">Materiales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tipo_accesorio.php">Tipo de Accesorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="usuarios.php">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="escanear.php">Escanear codigo de barra</a>
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
            <a href="agg_usuario.php" class="btn"><i class="bi bi-plus-circle"></i> Agregar Usuario</a>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Correo</th>
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
                            <td class="text-center">
                                <a href="editar_usuario.php?id=<?php echo $usuario['documento']; ?>" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i></a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="usuario" value="<?php echo $usuario['documento']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este usuario?')"><i class="bi bi-trash"></i></button>
                                </form>
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

        <!-- Paginación -->
        <?php if($total_paginas > 1): ?>
        <nav aria-label="Navegación de páginas">
            <ul class="pagination">
                <?php if($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=1" aria-label="Primera">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                // Mostrar un número limitado de páginas
                $rango = 2;
                $inicio_rango = max(1, $pagina_actual - $rango);
                $fin_rango = min($total_paginas, $pagina_actual + $rango);

                for($i = $inicio_rango; $i <= $fin_rango; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>" aria-label="Última">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>