<?php
require_once('config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (!isset($_SESSION['documento'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id_empresa'])) {
    echo "<script>alert('Empresa no encontrada');</script>";
    echo "<script>window.location = 'index.php';</script>";
    exit();
}

$id_empresa = $_GET['id_empresa'];

$sql = $con->prepare("SELECT nombre_empresa FROM empresa WHERE id_empresa = ?");
$sql->execute([$id_empresa]);
$empresa = $sql->fetch(PDO::FETCH_ASSOC);

$sql = $con->prepare("SELECT nombre, email FROM usuario WHERE documento = ? AND Id_rol = 1");
$sql->execute([$_SESSION['documento']]);
$admin = $sql->fetch(PDO::FETCH_ASSOC);

if (!$empresa || !$admin) {
    echo "<script>alert('No se pudo obtener la informacion necesaria');</script>";
    echo "<script>window.location = 'index.php';</script>";
    exit();
}

if (isset($_POST['enviar'])) {
    $mensaje = $_POST['mensaje'];

    if(empty(trim($mensaje))) {
        echo "<script>alert('El mensaje es obligatorio. Por favor escriba un mensaje para el administrador.');</script>";
    } else {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'gabrieladeviamarin@gmail.com';
            $mail->Password   = 'awuyttiaknxgcdfq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom($admin['email'], $admin['nombre']);
            $mail->addAddress($superadmin_email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = "Solicitud de renovación de licencia - " . $empresa['nombre_empresa'];
            $mail->Body    = "
            <p>Solicitud de renovación de licencia</p>
            <p><strong>Empresa:</strong> {$empresa['nombre_empresa']}</p>
            <p><strong>ID Empresa:</strong> {$id_empresa}</p>
            <p><strong>Administrador:</strong> {$admin['nombre']}</p>
            <p><strong>Correo:</strong> {$admin['email']}</p>
            <p><strong>Mensaje:</strong> {$mensaje}</p>";

            $mail->send();
            echo "<script>alert('Solicitud enviada correctamente. El administrador del sistema se pondrá en contacto con usted.');</script>";
            echo "<script>window.location = 'index.php';</script>";
            exit();
        } catch (Exception $e) {
            echo "<script>alert('Error al enviar el correo: {$mail->ErrorInfo}');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovar Licencia</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Solicitar renovacion de licencia</h2>
                        <div class="alert alert-warning">
                            <p>La licencia de su empresa <strong><?php echo $empresa['nombre_empresa']; ?></strong> ha expirado</p>
                            <p>Por favor complete el siguiente formulario para solicitar una renovacion</p>
                        </div>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Mensaje (opcional)</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="4" placeholder="Tenga en cuenta que contamos con 3 planes de licencia, la licencia demo que es gratis, la licencia anual que es de $70.000 y la licencia de 2 años que es de $120.000, por favor describa el tipo de licencia que desea..." ></textarea>
                            </div>
                            <button type="submit" name="enviar" class="btn btn-primary w-100">Enviar Solicitud</button>
                            <a href="index.php" class="btn btn-secondary w-100 mt-3">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>