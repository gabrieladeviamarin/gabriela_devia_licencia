<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $nit = $_GET['id'];

    $sql = $con->prepare("SELECT * FROM empresa WHERE id_empresa = $nit");
    $sql->execute();
    $empresa = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$empresa) {
        echo "<script>alert('Empresa no encontrada');</script>";
        echo "<script>window.location = 'index.php';</script>";
        exit(); 
    }

} else {
    echo "<script>alert('Empresa no seleccionada');</script>";
    echo "<script>window.location = 'index.php';</script>";
}

$sql = $con->prepare("SELECT * FROM tipo_licencia ORDER BY nombre_tipo_licencia ASC");
$sql->execute();
$tipos_licencia = $sql->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['asignar'])) {
    $id_tipo_licencia = $_POST['tipo_licencia'];
    $fecha_inicio = $_POST['fecha_inicio']; 
    $fecha_inicio_obj = new DateTime($fecha_inicio);
    $fecha_fin_obj = clone $fecha_inicio_obj;

    $sql = $con->prepare("SELECT * FROM tipo_licencia WHERE id_tipo_licencia = $id_tipo_licencia");
    $sql->execute();
    $tipo_licencia = $sql->fetch(PDO::FETCH_ASSOC);

    if($tipo_licencia['tipo_duracion'] == 'dias') {
        $dias = $tipo_licencia['duracion'] * 1; 
        $fecha_fin_obj->add(new DateInterval('P' . $dias . 'D'));
    } else if($tipo_licencia['tipo_duracion'] == 'meses') {
        $dias = $tipo_licencia['duracion'] * 30;
        $fecha_fin_obj->add(new DateInterval('P' . $dias . 'D'));
    } else if($tipo_licencia['tipo_duracion'] == 'años') {
        $dias = $tipo_licencia['duracion'] * 365; 
        $fecha_fin_obj->add(new DateInterval('P' . $dias . 'D'));
    }
    
    $fecha_fin = $fecha_fin_obj->format('Y-m-d');
    $bytes = random_bytes(5);
    $id_licencia = bin2hex($bytes);
    
    $sql = $con->prepare("INSERT INTO licencia (id_licencia, fecha_ini, fecha_fin, id_tipo_licencia, id_empresa) VALUES (?, ?, ?, ?, ?)");
    $sql->execute([$id_licencia, $fecha_inicio, $fecha_fin, $id_tipo_licencia, $nit]);
    
    header("Location: index.php?mensaje=licencia_asignada");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar empresa</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Asignar licencia</h2>

                        <form method="POST" id="form">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="tipo_licencia" class="form-label">Tipo de licencia</label>
                                <select class="form-select" id="tipo_licencia" name="tipo_licencia" required>
                                    <option value="" selected disabled>Seleccione un tipo de licencia</option>
                                    <?php foreach($tipos_licencia as $tipo) {
                                            echo '<option value="'.$tipo['id_tipo_licencia'].'">'.$tipo['nombre_tipo_licencia'].' - '.$tipo['descripcion'].' ($'.$tipo['precio'].')</option>';
                                    }?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="asignar" class="btn btn-primary">Asignar</button>
                                <a href="index.php" class="btn btn-secondary">Volver</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function validarTipoLicencia(tipo_licencia) {
            return !isNaN(tipo_licencia)
        }

        function showError(inputId, message) {
            const input = document.getElementById(inputId);
            let errorDiv = input.nextElementSibling;

            if (!errorDiv || !errorDiv.classList.contains('error-message')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.color = 'red';
                errorDiv.style.fontSize = '12px';
                errorDiv.style.marginTop = '5px';
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }

            errorDiv.textContent = message;
            input.classList.add('is-invalid');
        }

        function clearErrors() {
            const errors = document.querySelectorAll('.error-message');
            errors.forEach(error => error.remove());

            const inputs = document.querySelectorAll('.is-invalid');
            inputs.forEach(input => input.classList.remove('is-invalid'));
        }

        function validateRegisterForm() {
            clearErrors();

            const tipo_licencia = document.getElementById('tipo_licencia').value;
            let isValid = true;

            if (!validarTipoLicencia(tipo_licencia)) {
                showError('tipo_licencia', 'Debe seleccionar un tipo de licencia');
                isValid = false;
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form');
            form.addEventListener('submit', function(event) {
                if (!validateRegisterForm()) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>