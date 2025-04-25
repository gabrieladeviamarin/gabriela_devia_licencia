<?php
require_once('../config/conex.php');
// Si usas Composer, añade esto:
require_once('../vendor/autoload.php');
// Si no usas Composer, incluye la librería manualmente:
// require_once('../libs/barcode/src/BarcodeGenerator.php');
// require_once('../libs/barcode/src/BarcodeGeneratorPNG.php');

use Picqer\Barcode\BarcodeGeneratorPNG;

$conex = new Database;
$con = $conex->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
    $codigo_barras = $_GET['codigo'];
}

$sql_empresa = $con->prepare("SELECT id_empresa FROM usuario WHERE documento = ?");
$sql_empresa->execute([$_SESSION['documento']]);
$empresa = $sql_empresa->fetch(PDO::FETCH_ASSOC);
$id_empresa = $empresa['id_empresa'];

$sql = $con->prepare("SELECT id_material, nombre_material FROM material ORDER BY nombre_material ASC");
$sql->execute();
$materiales = $sql->fetchAll(PDO::FETCH_ASSOC);


$sql = $con->prepare("SELECT id_tipo_accesorio, nombre_tipo FROM tipo_accesorio WHERE id_empresa = ? ORDER BY nombre_tipo ASC");
$sql->execute([$id_empresa]);
$tipos_accesorios = $sql->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['agregar'])) {
    $caracteristicas = $_POST['caracteristicas'];
    $peso = $_POST['peso'];
    $precio = $_POST['precio'];
    $id_material = $_POST['id_material'];
    $id_tipo_accesorio = $_POST['id_tipo_accesorio'];

    try {
        $con->beginTransaction();

        if ($codigo_barras){

            $sql = $con->prepare("INSERT INTO accesorio (codigo_barras, caracteristicas, peso, precio, id_material, id_tipo_accesorio, id_empresa)
                             VALUES ('$codigo_barras', '$caracteristicas', '$peso', $precio, $id_material, $id_tipo_accesorio, $id_empresa)");
            $sql->execute();
        } else {

            $prefijo = 'JYA';
            $numero_aleatorio = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $codigo_barras = $prefijo . $numero_aleatorio;

            $check = $con->prepare("SELECT COUNT(*) FROM accesorio WHERE codigo_barras = ?");
            $check->execute([$codigo_barras]);
            if($check->fetchColumn() > 0) {

                $numero_aleatorio = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                $codigo_barras = $prefijo . $numero_aleatorio;
            }

            $sql = $con->prepare("INSERT INTO accesorio (codigo_barras, caracteristicas, peso, precio, id_material, id_tipo_accesorio, id_empresa)
                                 VALUES ('$codigo_barras', '$caracteristicas', '$peso', $precio, $id_material, $id_tipo_accesorio, $id_empresa)");
            $sql->execute();

        }

        $id_accesorio = $con->lastInsertId();

        $generator = new BarcodeGeneratorPNG();

        $dir = "../codigos_barras/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $barcode = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);
        file_put_contents($dir . "barcode_$id_accesorio.png", $barcode);

        $con->commit();

        echo "<script>alert('Accesorio agregado correctamente con codigo de barras: " . $codigo_barras . "');</script>";
        echo "<script>window.location = 'index.php?id=" . $id_accesorio . "';</script>";

    } catch (Exception $e) {

        $con->rollBack();
        echo "<script>alert('Error al agregar el accesorio: " . $e->getMessage() . "');</script>";
        echo "<script>window.location = 'index.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Accesorio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- QuaggaJS para lectura de códigos de barras -->
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Agregar Accesorio</h2>


                        <form method="POST" id="form">
                            <div class="mb-3">
                                <label for="caracteristicas" class="form-label">Características</label>
                                <textarea class="form-control" id="caracteristicas" name="caracteristicas" rows="3" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="peso" class="form-label">Peso</label>
                                <input type="text" class="form-control" id="peso" name="peso" placeholder="EJ: 10.5 gramos" required>
                            </div>

                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="number" class="form-control" id="precio" name="precio" placeholder="sin puntos ni comas" required  >
                            </div>

                            <div class="mb-3">
                                <label for="id_material" class="form-label">Material</label>
                                <select class="form-select" id="id_material" name="id_material" required>
                                    <option value="" selected disabled>Seleccione un material</option>
                                    <?php foreach($materiales as $material): ?>
                                        <option value="<?php echo $material['id_material']; ?>"><?php echo $material['nombre_material']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="id_tipo_accesorio" class="form-label">Tipo de Accesorio</label>
                                <select class="form-select" id="id_tipo_accesorio" name="id_tipo_accesorio" required>
                                    <option value="" selected disabled>Seleccione un tipo de accesorio</option>
                                    <?php foreach($tipos_accesorios as $tipo): ?>
                                        <option value="<?php echo $tipo['id_tipo_accesorio']; ?>"><?php echo $tipo['nombre_tipo']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="agregar" class="btn btn-primary">Agregar</button>
                                <a href="index.php" class="btn btn-secondary">Volver</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el escáner de código de barras -->
    <div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scannerModalLabel">Escanear código de barras</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopScanner()"></button>
                </div>
                <div class="modal-body">
                    <div id="scanner-container" style="position: relative;">
                        <div id="interactive" class="viewport" style="width: 100%; height: 300px; position: relative;"></div>
                    </div>
                    <div class="mt-3">
                        <p id="scanned-result" class="text-center fw-bold"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopScanner()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Validación del formulario
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

            if (peso <= 0) {
                showError('peso', 'El peso debe ser mayor que cero');
                isValid = false;
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

            // Iniciar escáner cuando se abre el modal
            document.getElementById('scannerModal').addEventListener('shown.bs.modal', startScanner);
        });

        // Código para el escáner de códigos de barras
        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'),
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment" // usar cámara trasera en móviles
                    },
                },
                locator: {
                    patchSize: "medium",
                    halfSample: true
                },
                numOfWorkers: navigator.hardwareConcurrency || 4,
                frequency: 10,
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader", "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader"]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.error(err);
                    alert("Error al iniciar el escáner de códigos de barras: " + err);
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(result) {
                var code = result.codeResult.code;
                document.getElementById('scanned-result').innerText = "Código detectado: " + code;

                // Buscar el accesorio con este código
                buscarAccesorio(code);

                // Detener el escáner después de la detección
                stopScanner();

                // Cerrar el modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('scannerModal'));
                modal.hide();
            });
        }

        function stopScanner() {
            if (Quagga) {
                Quagga.stop();
            }
        }

        function buscarAccesorio(codigo) {
            // Hacer una solicitud AJAX para buscar el accesorio en la base de datos
            fetch('buscar_accesorio.php?codigo=' + codigo)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Redireccionar a la página de ver accesorio
                        window.location.href = 'ver_accesorio.php?id=' + data.id_accesorio;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al buscar el accesorio: ' + error);
                });
        }
    </script>
</body>
</html>