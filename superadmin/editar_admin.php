<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['documento']) && isset($_GET['nom']) && isset($_GET['id'])) {
    $documento = $_GET['documento'];
    $nombre_empresa = $_GET['nom'];
    $id_empresa = $_GET['id'];

} else {
    echo "<script>alert('No se ha seleccionado ningun usuario administrador');</script>";
    echo "<script>window.location = 'index.php';</script>";
    exit();
}

$sql = $con->prepare("SELECT * FROM usuario WHERE documento = '$documento'");
$sql->execute();
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['eliminar'])) {
    $documento = $_POST['admin'];
    $sql = $con->prepare("DELETE FROM usuario WHERE documento = '$documento'"); 
    $sql->execute();
    echo "<script>alert('Administrador eliminado correctamente');</script>";
    echo "<script>window.location = 'index.php';</script>";
    exit();
}

if (isset($_POST['actualizar'])) {

    $nombre = $_POST['nombre'];
    $email  = $_POST['correo'];

    $sql = $con->prepare("UPDATE usuario SET nombre = '$nombre', email = '$email' WHERE documento = '$documento'");
    $sql->execute();
    echo "<script>alert('Usuario actualizado correctamente');</script>";
    echo "<script>window.location = 'index.php';</script>";

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar administrador </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="d-flex justify-content-end ">
                    <a href="agg_admin.php?id=<?php echo $id_empresa ?>&&nom=<?php echo $nombre_empresa; ?>" class="btn fs-4"><i class="bi bi-plus-circle"></i></a>
                    <form method="POST" style="display:inline;">
                                <input type="hidden" name="admin" value="<?php echo $usuario['documento']; ?>">
                                <button type="submit" name="eliminar" class="btn fs-4" onclick="return confirm('¿Está seguro de eliminar este administrador?')"><i class="bi bi-trash"></i></button>
                    </form>
                    </div>
                    <div class="card-body p-5">
                        
                        <h2 class="text-center mb-5">Editar administrador de <?php echo $nombre_empresa; ?> </h2>

                        <form action="" id="form" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="number" class="form-control" id="documento" name="documento" value="<?php echo $usuario['documento']?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $usuario['nombre']?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electronico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $usuario['email']?>" required>
                            </div>
                            <button type="submit" id="submit" name="actualizar" class="btn btn-primary w-100">Actualizar</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-3">Volver</a>
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

        function validarPassword(password) {
            const RegPassword = /^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
            return RegPassword.test(password);
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
            const password = document.getElementById('password').value;

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

            if (!validarPassword(password)) {
                showError('password', 'La contraseña debe contener al menos 8 caracteres, 1 mayúscula y 1 carácter especial.');
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