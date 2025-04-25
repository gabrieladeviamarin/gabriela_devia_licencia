<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escanear Codigo de Barras</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- QuaggaJS para lectura de codigos de barras -->
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <style>
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        #interactive {
            width: 100%;
            max-width: 500px;
            height: 350px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>
<body class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 rounded">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-4 me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accesorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="material.php">Materiales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tipo_accesorio.php">Tipo de Accesorios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="escanear.php">Escanear codigo de barras</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../include/exit.php" class="btn btn-danger me-4">Cerrar sesion</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="card p-4 mx-auto" style="max-width: 90%;">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h2>Escanear codigo de barras</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-4">
                    <div id="scanner-container" class="d-flex justify-content-center">
                        <div id="interactive" class="viewport"></div>
                    </div>
                    <div class="mt-4">
                        <p id="scanned-result" class="fw-bold"></p>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-3 mb-3">
                    <button id="start-button" class="btn btn-success">
                        <i class="bi bi-camera-video"></i> Encender camara
                    </button>
                    <button id="stop-button" class="btn btn-danger" disabled>
                        <i class="bi bi-camera-video-off"></i> Apagar camara
                    </button>
                </div>
                <div class="mt-4 text-center">
                    <hr>
                    <h4>O ingresa el codigo manualmente</h4>
                    <form id="manual-form" class="mt-3">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="manual-code" placeholder="Ingresa el código del accesorio">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Botones para controlar el escaner
            const startButton = document.getElementById('start-button');
            const stopButton = document.getElementById('stop-button');
            const manualForm = document.getElementById('manual-form');

            startButton.addEventListener('click', function() {
                startScanner();
                startButton.disabled = true;
                stopButton.disabled = false;
            });

            stopButton.addEventListener('click', function() {
                stopScanner();
                startButton.disabled = false;
                stopButton.disabled = true;
            });

            // Manejar la entrada manual
            manualForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const codeInput = document.getElementById('manual-code');
                if (codeInput.value.trim()) {
                    document.getElementById('scanned-result').innerText = "Código ingresado: " + codeInput.value;
                    buscarAccesorio(codeInput.value);
                } else {
                    alert('Por favor ingresa un código');
                }
            });
        });

        // codigo para el escaner de codigos de barras
        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'),
                    constraints: {
                        width: 500,
                        height: 350,
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
                    alert("Error al iniciar el escaner de codigos de barras: " + err);
                    document.getElementById('start-button').disabled = false;
                    document.getElementById('stop-button').disabled = true;
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(result) {
                var code = result.codeResult.code;
                document.getElementById('scanned-result').innerText = "codigo detectado: " + code;

                // Buscar el accesorio con este codigo
                buscarAccesorio(code);

                // Detener el escaner después de la deteccion
                stopScanner();
                document.getElementById('start-button').disabled = false;
                document.getElementById('stop-button').disabled = true;
            });
        }

        function stopScanner() {
            if (Quagga) {
                Quagga.stop();
            }
        }

        async function buscarAccesorio(codigo) {
            try {
                const response = await fetch('buscar_accesorio.php?codigo=' + codigo);
                const data = await response.json();

                if (data.codigo) {
                    window.location.href = 'agg_accesorio.php?codigo=' + data.codigo;
                } else if (data.id_accesorio) {
                    window.location.href = 'mostrar_accesorio.php?id=' + data.id_accesorio;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al buscar el accesorio: ' + error);
            }
        }
    </script>
</body>
</html>