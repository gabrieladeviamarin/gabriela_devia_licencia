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
    echo"<script>alert('Primero inicie sesion');</script>";
    echo "<script>window.location='index.php'</script>";
    exit();
}

$documento = $_SESSION['documento'];
$sql = $con->prepare("SELECT email FROM superadmin where documento = '$documento'");
$sql->execute();
$email = $sql->fetchColumn();


if (!isset($_SESSION['codigo_verificacion'])) {
    
    $_SESSION['codigo_verificacion'] = rand(100000, 999999);
    $codigo_verificacion = $_SESSION['codigo_verificacion'];

    $mail= new PHPMailer(true);

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
        $mail->setFrom('gabrieladeviamarin@gmail.com', 'administrador');
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);
        $mail->Subject = "Correo de verificación";
        $mail->Body    = "
        <p>Buen dia,</p>
        <p>Realizaste una solicitud para iniciar sesion en el software de accesorios. Si no fuiste tú, ignora este correo.</p>
        <p>Copia el siguiente codigo en la interfaz para verificar:</p>
        <p><strong style='font-size: 24px;'>{$codigo_verificacion}</strong></p>
        <p>Este enlace de verificacion caducara en 15 minutos.</p>";

        $mail->send();

    } catch (Exception $e) {
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        header("Location: verificar_rol.php?message=error");
    }
}

if(isset($_POST['submit'])){
    $codigo = $_POST['codigo'];
    if($codigo == $_SESSION['codigo_verificacion']){
        //Borrar codigo de verificacion de la session
        unset($_SESSION['codigo_verificacion']);
        header("Location: superadmin/index.php");
        exit(); 
    } else {
        $error_mensaje = "Cidigo de verificacion incorrecto";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <title>Verificar usuario</title>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Verificacion de usuario</h2>
                        <p class="text-cemter mb-4">Por favor digita el codigo de verificacion enviado a tu correo</p>
                        <form action="" method="POST" autocomplete="off">
                            <div class="mb-4">
                                <input type="codigo" class="form-control" id="codigo" name="codigo" placeholder="Codigo de verificacion">
                            </div>
                            <button type="submit" id="submit" name="submit" class="btn btn-primary w-100">Iniciar Sesion</button>
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
