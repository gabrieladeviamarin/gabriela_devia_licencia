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

if (isset($_POST['actualizar'])) {
    $nombre = $_POST['nombre_empresa'];

    $sql = $con->prepare("UPDATE empresa SET nombre_empresa = '$nombre' WHERE id_empresa = '$nit'");
    $sql->execute();
    echo "<script>alert('Empresa actualizada correctamente');</script>";
    echo "<script>window.location = 'index.php';</script>";

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
                        <h2 class="text-center mb-4">Editar empresa</h2>

                        <form method="POST" id="form">
                            <div class="mb-3">
                                <label for="nit" class="form-label">NIT de la empresa</label>
                                <input type="number" class="form-control" id="nit" name="nit" value="<?php echo $nit; ?>" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_empresa" class="form-label">Nombre empresa</label>
                                <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" value="<?php echo $empresa['nombre_empresa']; ?>" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
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

        function validarNombreEmpresa(nombre_empresa) {
            return nombre_empresa.length >= 2;
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

            const nombre_empresa = document.getElementById('nombre_empresa').value;
            let isValid = true;

            if (!validarNombreEmpresa(nombre_empresa)) {
                showError('nombre_empresa', 'El nombre de la empresa debe tener al menos 2 caracteres');
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