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

// Verificar si se proporcionó un ID de usuario
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$documento = $_GET['id'];

// Obtener datos del usuario
$sql = $con->prepare("SELECT * FROM usuario WHERE documento = ? AND id_empresa = ? AND id_rol = 2");
$sql->execute([$documento, $id_empresa]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

// Verificar si el usuario existe y pertenece a la empresa
if (!$usuario) {
    header("Location: usuarios.php");
    exit();
}

if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    
    // Verificar si se proporcionó una nueva contraseña
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $passwordEncrip = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar usuario con nueva contraseña
        $sql = $con->prepare("UPDATE usuario SET nombre = ?, apellido = ?, telefono = ?, correo = ?, password = ? WHERE documento = ?");
        $sql->execute([$nombre, $apellido, $telefono, $correo, $passwordEncrip, $documento]);
    } else {
        // Actualizar usuario sin cambiar la contraseña
        $sql = $con->prepare("UPDATE usuario SET nombre = ?, apellido = ?, telefono = ?, correo = ? WHERE documento = ?");
        $sql->execute([$nombre, $apellido, $telefono, $correo, $documento]);
    }
    
    echo "<script>alert('Usuario actualizado correctamente');</script>";
    echo "<script>window.location = 'usuarios.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Editar Usuario</h2>

                        <form action="" id="form" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="number" class="form-control" id="documento" name="documento" value="<?php echo $usuario['documento']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electronico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $usuario['email']; ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" id="submit" name="submit" class="btn btn-primary">Actualizar Usuario</button>
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
        function validarNombre(nombre) {
            return nombre.length >= 3;
        }

        function validarEmail(email) {
            const regCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regCorreo.test(email);
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

            const nombre = document.getElementById('nombre').value;
            const correo = document.getElementById('email').value;

            let isValid = true;

            if (!validarNombre(nombre)) {
                showError('nombre', 'El nombre debe tener al menos 3 caracteres.');
                isValid = false;
            }

            if (!validarEmail(email)) {
                showError('email', 'Por favor ingrese un correo válido.');
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