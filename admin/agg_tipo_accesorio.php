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

if (isset($_POST['agregar'])) {
    $nombre_tipo = $_POST['nombre_tipo'];

    // Verificar si el tipo de accesorio ya existe para esta empresa
    $sql = $con->prepare("SELECT * FROM tipo_accesorio WHERE nombre_tipo = ? AND id_empresa = ?");
    $sql->execute([$nombre_tipo, $id_empresa]);
    $tipo = $sql->fetch(PDO::FETCH_ASSOC);

    if ($tipo) {
        echo "<script>alert('El tipo de accesorio ya existe');</script>";
    } else {
        // Insertar nuevo tipo de accesorio
        $sql = $con->prepare("INSERT INTO tipo_accesorio (nombre_tipo, id_empresa) VALUES (?, ?)");
        $sql->execute([$nombre_tipo, $id_empresa]);
        echo "<script>alert('Tipo de accesorio agregado correctamente');</script>";
        echo "<script>window.location = 'tipo_accesorio.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tipo de Accesorio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Agregar Tipo de Accesorio</h2>

                        <form method="POST" id="form">
                            <div class="mb-3">
                                <label for="nombre_tipo" class="form-label">Nombre del Tipo</label>
                                <input type="text" class="form-control" id="nombre_tipo" name="nombre_tipo" placeholder="Ej: Collar, Pulsera, Anillo" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="agregar" class="btn btn-primary">Agregar</button>
                                <a href="tipo_accesorio.php" class="btn btn-secondary">Volver</a>
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
        function validarNombreTipo(nombre) {
            return nombre.length >= 2;
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

        function validateForm() {
            clearErrors();

            const nombre_tipo = document.getElementById('nombre_tipo').value;
            let isValid = true;

            if (!validarNombreTipo(nombre_tipo)) {
                showError('nombre_tipo', 'El nombre del tipo debe tener al menos 2 caracteres');
                isValid = false;
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form');
            form.addEventListener('submit', function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>