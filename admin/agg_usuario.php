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

if (isset($_POST['submit'])) {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordEncrip = password_hash($password, PASSWORD_DEFAULT);
    $rol = 2;

    $sql = $con->prepare("SELECT * FROM usuario WHERE documento = ?");
    $sql->execute([$documento]);
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo "<script>alert('El usuario ya existe');</script>";
        echo "<script>window.location = 'usuarios.php';</script>";
    } else {
        $sql = $con->prepare("INSERT INTO usuario (documento, nombre, email, password, Id_rol, id_empresa)
                             VALUES ($documento, '$nombre', '$email', '$passwordEncrip', $rol, $id_empresa)");
        $sql->execute();
        echo "<script>alert('Usuario agregado correctamente');</script>";
        echo "<script>window.location = 'usuarios.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light" onload="form.documento.focus()">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Agregar Usuario</h2>

                        <form action="" id="form" name="form" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="number" class="form-control" tabindex="2" id="documento" name="documento" required>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" tabindex="3" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electronico</label>
                                <input type="email" class="form-control" tabindex="4" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" tabindex="5" id="password" name="password" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" tabindex="6" id="submit" name="submit" class="btn btn-primary">Agregar Usuario</button>
                                <a href="usuarios.php" class="btn btn-secondary">Volver</a>
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
        function validarDocumento(documento) {
            return documento.length >= 6 && documento.length <= 10;
        }

        function validarNombre(nombre) {
            return nombre.length >= 3;
        }

        function validarEmail(email) {
            const regCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regCorreo.test(email);
        }

        function validarPassword(password) {
            const regPassword = /^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
            return regPassword.test(password);
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

            const documento = document.getElementById('documento').value;
            const nombre = document.getElementById('nombre').value;
            const apellido = document.getElementById('apellido').value;
            const telefono = document.getElementById('telefono').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            let isValid = true;

            if (!validarDocumento(documento)) {
                showError('documento', 'El documento debe tener entre 6 y 10 digitos.');
                isValid = false;
            }

            if (!validarNombre(nombre)) {
                showError('nombre', 'El nombre debe tener al menos 3 caracteres.');
                isValid = false;
            }

            if (!validarEmail(email)) {
                showError('email', 'Por favor ingrese un email valido.');
                isValid = false;
            }

            if (!validarPassword(password)) {
                showError('password', 'La contraseña debe contener al menos 8 caracteres, 1 mayuscula y 1 carácter especial.');
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