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
    $id_empresa = $_POST['empresa'];

    $sql = $con->prepare("DELETE FROM empresa WHERE id_empresa = $id_empresa");
    $sql->execute();
    header("Location: index.php");
    exit();
}

$sql = $con->prepare("SELECT e.id_empresa, e.nombre_empresa, licencia_reciente.fecha_ini, licencia_reciente.fecha_fin, admin.documento, admin.nombre
    FROM empresa e
    LEFT JOIN (SELECT lic.* FROM licencia lic
    INNER JOIN (SELECT id_empresa, MAX(fecha_fin) as fecha_vencimiento_maxima FROM licencia
    GROUP BY id_empresa) fechas_max ON lic.id_empresa = fechas_max.id_empresa AND lic.fecha_fin = fechas_max.fecha_vencimiento_maxima) licencia_reciente ON e.id_empresa = licencia_reciente.id_empresa
    LEFT JOIN (SELECT * FROM usuario usuario_admin WHERE usuario_admin.Id_rol = 1
    GROUP BY usuario_admin.id_empresa) admin ON e.id_empresa = admin.id_empresa
    ORDER BY e.nombre_empresa ASC");
$sql->execute();
$empresas = $sql->fetchAll(PDO::FETCH_ASSOC);

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
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php">Usuarios</a>
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
            <h2>Empresas registradas</h2>
            <a href="agg_empresa.php" class="btn "><i class="bi bi-plus-circle"></i> Agregar Empresa</a>
        </div>

        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>NIT</th>
                    <th>Nombre Empresa</th>
                    <th>Inicio Licencia</th>
                    <th>Fin Licencia</th>
                    <th>Estado</th>
                    <th>Administrador</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($empresas) > 0): ?>
                    <?php foreach($empresas as $empresa): ?>
                        <tr>
                            <td><?php echo $empresa['id_empresa']; ?></td>
                            <td><?php echo $empresa['nombre_empresa']; ?></td>
                            <td><?php echo $empresa['fecha_ini'] ? $empresa['fecha_ini'] : '<span class="badge bg-secondary">Sin licencia</span>'; ?></td>
                            <td><?php echo $empresa['fecha_fin'] ? $empresa['fecha_fin'] : '<span class="badge bg-secondary">Sin licencia</span>'; ?></td>
                            <td><?php $fecha_actual = date('Y-m-d');
                                if(!empty($empresa['fecha_fin'])) {
                                    if($empresa['fecha_fin'] >= $fecha_actual) {
                                        echo '<span class="badge bg-success">Vigente</span>';
                                    } else {
                                        echo '<a href="asignar_licencia.php?id='.$empresa['id_empresa'].'" class="badge bg-danger" style="text-decoration:none;">Vencida<br><br>RENOVAR</a>';
                                    }
                                } else {
                                    echo '<a href="asignar_licencia.php?id='.$empresa['id_empresa'].'" class="badge bg-secondary" style="text-decoration:none;">Sin licencia <br><br>ASIGNAR</a>';
                                }?> </td>
                            <td><?php
                            if (!empty ($empresa['nombre'])) {

                                echo $empresa['nombre']; ?>
                                <a href="editar_admin.php?documento=<?php echo $empresa['documento']; ?>&&id=<?php echo $empresa['id_empresa']; ?>&&nom=<?php echo $empresa['nombre_empresa']?>" class="btn btn-sm"><i class="bi bi-pencil"></i></a>
                                <?php
                                } else {
                                ?> <a href="agg_admin.php?id=<?php echo $empresa['id_empresa']; ?>&&nom=<?php echo $empresa['nombre_empresa']?>" class="badge bg-primary" style="text-decoration: none; ">Asignar administrador</a>
                            <?php }?> </td>
                            <td class="text-center">
                                <a href="editar_empresa.php?id=<?php echo $empresa['id_empresa']; ?>" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i></a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="empresa" value="<?php echo $empresa['id_empresa']; ?>">
                                    <button type="submit" name="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Esta seguro de eliminar esta empresa?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay empresas registradas</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>