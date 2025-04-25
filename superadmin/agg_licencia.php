<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre_tipo_licencia'];
    $tipo_duracion = strtolower($_POST['tipo_duracion']);
    $duracion = $_POST['duracion'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    $sql = $con->prepare("SELECT * FROM tipo_licencia WHERE nombre_tipo_licencia = '$nombre'");
    $sql->execute();
    $licencia = $sql->fetch(PDO::FETCH_ASSOC);

    if ($licencia) {
        echo "<script>alert('La licencia ya existe');</script>";
        echo "<script>window.location = 'licencias.php';</script>";
        exit();
    } else{
        $sql = $con->prepare("INSERT INTO tipo_licencia (nombre_tipo_licencia,tipo_duracion, duracion, descripcion,precio) VALUES ('$nombre','$tipo_duracion', $duracion,' $descripcion', $precio)");
        $sql->execute();
        echo "<script>alert('Licencia agregada correctamente');</script>";
        echo "<script>window.location = 'licencias.php';</script>";
    }


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Licencia</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Agregar licencia</h2>

                        <form method="POST" id="form">
                            <div class="mb-3">
                                <label for="nombre_tipo_licencia" class="form-label">Nombre licencia</label>
                                <input type="text" class="form-control" id="nombre_tipo_licencia" name="nombre_tipo_licencia" placeholder="EJ: Licencia Demo, Licencia 1 año, Licencia 2 años" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_duracion" class="form-label">Tipo de duracion</label>
                                <input type="text" class="form-control" id="tipo_duracion" name="tipo_duracion" placeholder="EJ: dias, años" required>
                            </div>
                            <div class="mb-3">
                                <label for="duracion" class="form-label">Tiempo de duracion</label>
                                <input type="number" class="form-control" id="duracion" name="duracion" placeholder="EJ: 1, 2" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripcion</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Duracion de la licencia y su descripcion" required>
                            </div>
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="text" class="form-control" id="precio" name="precio" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="agregar" class="btn btn-primary">Agregar</button>
                                <a href="licencias.php" class="btn btn-secondary">Volver</a>
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

        function validarNombreEmpresa(nombre_tipo_licencia) {
            return nombre_tipo_licencia.length >= 2;
        }

        function validarTipoDuracion(duracion) {
            return descripcion.length = 4;
        }

        function validarDuracion(duracion) {
            return !isNaN(duracion) && duracion >= 0;
        }

        function validarDescripcion(descripcion) {
            return descripcion.length >= 10;
        }

        function validarPrecio(precio) {
            return !isNaN(precio) && precio >= 0;
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

            const nombre_tipo_licencia = document.getElementById('nombre_tipo_licencia').value;
            const tipo_duracion = document.getElementById('tipo_duracion').value;
            const duracion = document.getElementById('duracion').value;
            const descripcion = document.getElementById('descripcion').value;
            const precio = document.getElementById('precio').value;
            let isValid = true;

            if (!validarNombreEmpresa(nombre_tipo_licencia)) {
                showError('nombre_tipo_licencia', 'El nombre de la empresa debe tener al menos 2 caracteres');
                isValid = false;
            }
            if (!validarTipoDuracion(tipo_duracion)) {
                showError('tipo_duracion', 'La tipo_duracion debe ser DIAS o AÑOS');
                isValid = false;
            }
            if (!validarDuracion(duracion)) {
                showError('duracion', 'La duracion debe ser mayor o igual a 0');
                isValid = false;
            }
            if (!validarDescripcion(descripcion)) {
                showError('descripcion', 'La descripcion debe tener al menos 10 caracteres');
                isValid = false;
            }

            if (!validarPrecio(precio)) {
                showError('precio', 'El precio minimo de una licencia debe ser 0');
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