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

// Verificar si se proporcionó un ID de accesorio
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_accesorio = $_GET['id'];

// Obtener datos del accesorio
$sql = $con->prepare("SELECT * FROM accesorio WHERE Id_accesorio = ? AND id_empresa = ?");
$sql->execute([$id_accesorio, $id_empresa]);
$accesorio = $sql->fetch(PDO::FETCH_ASSOC);

// Verificar si el accesorio existe y pertenece a la empresa
if (!$accesorio) {
    header("Location: index.php");
    exit();
}

// Obtener materiales
$sql = $con->prepare("SELECT id_material, nombre_material FROM material ORDER BY nombre_material ASC");
$sql->execute();
$materiales = $sql->fetchAll(PDO::FETCH_ASSOC);

// Obtener tipos de accesorios
$sql = $con->prepare("SELECT id_tipo_accesorio, nombre_tipo FROM tipo_accesorio WHERE id_empresa = ? ORDER BY nombre_tipo ASC");
$sql->execute([$id_empresa]);
$tipos_accesorios = $sql->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['actualizar'])) {
    $caracteristicas = $_POST['caracteristicas'];
    $peso = $_POST['peso'];
    $precio = $_POST['precio'];
    $id_material = $_POST['id_material'];
    $id_tipo_accesorio = $_POST['id_tipo_accesorio'];

    // Actualizar accesorio
    $sql = $con->prepare("UPDATE accesorio SET caracteristicas = ?, peso = ?, precio = ?, id_material = ?, id_tipo_accesorio = ? WHERE Id_accesorio = ?");
    $sql->execute([$caracteristicas, $peso, $precio, $id_material, $id_tipo_accesorio, $id_accesorio]);
    
    echo "<script>alert('Accesorio actualizado correctamente');</script>";
    echo "<script>window.location = 'index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Accesorio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Editar Accesorio</h2>

                        <form method="POST" id="form">
                            <div class="mb-3">
                                <label for="caracteristicas" class="form-label">Características</label>
                                <textarea class="form-control" id="caracteristicas" name="caracteristicas" rows="3" required><?php echo $accesorio['caracteristicas']; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="peso" class="form-label">Peso (gramos)</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="peso" name="peso" value="<?php echo $accesorio['peso']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo $accesorio['precio']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="id_material" class="form-label">Material</label>
                                <select class="form-select" id="id_material" name="id_material" required>
                                    <option value="" disabled>Seleccione un material</option>
                                    <?php foreach($materiales as $material): ?>
                                        <option value="<?php echo $material['id_material']; ?>" <?php echo ($material['id_material'] == $accesorio['id_material']) ? 'selected' : ''; ?>>
                                            <?php echo $material['nombre_material']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="id_tipo_accesorio" class="form-label">Tipo de Accesorio</label>
                                <select class="form-select" id="id_tipo_accesorio" name="id_tipo_accesorio" required>
                                    <option value="" disabled>Seleccione un tipo de accesorio</option>
                                    <?php foreach($tipos_accesorios as $tipo): ?>
                                        <option value="<?php echo $tipo['id_tipo_accesorio']; ?>" <?php echo ($tipo['id_tipo_accesorio'] == $accesorio['id_tipo_accesorio']) ? 'selected' : ''; ?>>
                                            <?php echo $tipo['nombre_tipo']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
        function validateForm() {
            const caracteristicas = document.getElementById('caracteristicas').value;
            const peso = document.getElementById('peso').value;
            const precio = document.getElementById('precio').value;
            const id_material = document.getElementById('id_material').value;
            const id_tipo_accesorio = document.getElementById('id_tipo_accesorio').value;
            
            let isValid = true;
            
            // Limpiar mensajes de error previos
            clearErrors();
            
            if (caracteristicas.trim() === '') {
                showError('caracteristicas', 'Debe ingresar las características del accesorio');
                isValid = false;
            }
            
            if (peso <= 0 || isNaN(parseFloat(peso))) {
                showError('peso', 'El peso debe ser un número mayor que cero');
                isValid = false;
            } else {
                // Validar que tenga máximo 2 decimales
                const pesoStr = peso.toString();
                const decimalPart = pesoStr.includes('.') ? pesoStr.split('.')[1] : '';
                if (decimalPart.length > 2) {
                    showError('peso', 'El peso debe tener máximo 2 decimales');
                    isValid = false;
                }
            }
            
            if (precio <= 0) {
                showError('precio', 'El precio debe ser mayor que cero');
                isValid = false;
            }
            
            if (id_material === '') {
                showError('id_material', 'Debe seleccionar un material');
                isValid = false;
            }
            
            if (id_tipo_accesorio === '') {
                showError('id_tipo_accesorio', 'Debe seleccionar un tipo de accesorio');
                isValid = false;
            }
            
            return isValid;
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