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

// Obtener total de registros para la paginación
$sql_total = $con->prepare("SELECT COUNT(*) as total FROM accesorio WHERE id_empresa = ?");
$sql_total->execute([$id_empresa]);
$total_registros = $sql_total->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta con límite para paginación
$sql = $con->prepare("SELECT a.*, m.nombre_material, ta.nombre_tipo
                      FROM accesorio a
                      LEFT JOIN material m ON a.id_material = m.id_material
                      LEFT JOIN tipo_accesorio ta ON a.id_tipo_accesorio = ta.id_tipo_accesorio
                      WHERE a.id_empresa = ?
                      ORDER BY a.Id_accesorio ASC");
$sql->execute([$id_empresa]);
$accesorios = $sql->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['eliminar'])) {
    $id_accesorio = $_POST['accesorio'];

    $sql = $con->prepare("DELETE FROM accesorio WHERE Id_accesorio = ?");
    $sql->execute([$id_accesorio]);
    header("Location: index.php");
    exit();
}
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
                        <a class="nav-link active" href="index.php">Accesorios</a>
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
            <h2>Accesorios</h2>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Imagen codigo de barras</th>
                    <th>Codigo de barras</th>
                    <th>Caracteristicas</th>
                    <th>Peso</th>
                    <th>Precio</th>
                    <th>Material</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($accesorios) > 0): ?>
                    <?php foreach($accesorios as $accesorio): ?>
                        <tr>
                            <td><?php echo $accesorio['Id_accesorio']; ?></td>
                            <td><img src="../codigos_barras/barcode_<?php echo $accesorio['Id_accesorio']; ?>.png" alt="Código de barras" class="img-fluid my-2"> <br>
                            <a href="../codigos_barras/barcode_<?php echo $accesorio['Id_accesorio']; ?>.png" download class="btn btn-sm btn-outline-primary mb-1">Descargar codigo de Barras</a></td>
                            <td><?php echo $accesorio['codigo_barras'];?></td>
                            <td><?php echo $accesorio['caracteristicas']; ?></td>
                            <td><?php echo $accesorio['peso']; ?></td>
                            <td>$<?php echo number_format($accesorio['precio'], 2); ?></td>
                            <td><?php echo $accesorio['nombre_material']; ?></td>
                            <td><?php echo $accesorio['nombre_tipo']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No hay accesorios registrados</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
                <?php endif; 

                $rango = 2;
                $inicio_rango = max(1, $pagina_actual - $rango);
                $fin_rango = min($total_paginas, $pagina_actual + $rango);
                
                for($i = $inicio_rango; $i <= $fin_rango; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; 
                
                if($pagina_actual < $total_paginas): ?>
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