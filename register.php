<?php
require_once('config/conex.php');
$conex = new Database;
$con = $conex->conectar();

if(isset($_POST["submit"])){

    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $email  = $_POST['correo'];
    $password = $_POST['password'];
    $passwordEncrip = password_hash($password,PASSWORD_DEFAULT);


    $sql = $con->prepare("INSERT INTO superadmin (documento,nombre,email,password)
    VALUES ('$documento','$nombre','$email','$passwordEncrip')");
    $sql->execute();

};

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="register.css">
    <title>Registro</title>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Registro</h2>
                        <form action="register.php" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="number" class="form-control" id="documento" name="documento">
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre">
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electronico</label>
                                <input type="email" class="form-control" id="correo" name="correo">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <button type="submit" id="submit" name="submit" class="btn btn-primary w-100">Registrarse</button>
                            <p class="text-center mt-3">¿Ya tienes una cuenta? <a href="index.php">Inicia Sesion!</a></p>
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

        function validarEmail(correo) {
            const RegCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return RegCorreo.test(correo);
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

            const documento = document.getElementById('documento').value;
            const nombre = document.getElementById('nombre').value;
            const correo = document.getElementById('correo').value;

            let isValid = true;

            if (!validarDocumento(documento)) {
                showError('documento', 'El documento debe tener al menos 6 dígitos.');
                isValid = false;
            }

            if (!validarNombre(nombre)) {
                showError('nombre', 'El nombre debe tener al menos 3 caracteres.');
                isValid = false;
            }

            if (!validarEmail(correo)) {
                showError('correo', 'Por favor ingrese un correo válido.');
                isValid = false;
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                if (!validateRegisterForm()) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>