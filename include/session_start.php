<?php
require_once('../config/conex.php');
$conex = new Database;
$con = $conex->conectar();
session_start();

if (isset($_POST['submit'])){

    $documento = $_POST['documento'];
    $passwordDesc = htmlentities(addslashes($_POST['password']));
    $sql = $con->prepare("SELECT * FROM usuario where documento = '$documento'");
    $sql->execute();
    $fila = $sql->fetch(PDO::FETCH_ASSOC);

    $sql = $con->prepare("SELECT * FROM superadmin where documento = '$documento'");
    $sql->execute();
    $fila1 = $sql->fetch(PDO::FETCH_ASSOC);

    if ($fila) {

        if (password_verify($passwordDesc, $fila['password'])) {

            $_SESSION['documento'] = $fila['documento'];
            $rol = $fila['Id_rol'];
            $id_empresa = $fila['id_empresa'];

            $sql_licencia = $con->prepare("SELECT * FROM licencia l
                INNER JOIN (SELECT id_empresa, MAX(fecha_fin) as fecha_vencimiento_maxima FROM licencia
                GROUP BY id_empresa) fechas_max ON l.id_empresa = fechas_max.id_empresa AND l.fecha_fin = fechas_max.fecha_vencimiento_maxima
                WHERE l.id_empresa = $id_empresa");
            $sql_licencia->execute();
            $licencia = $sql_licencia->fetch(PDO::FETCH_ASSOC);

            $fecha_actual = date('Y-m-d');

            if (!$licencia) {
                if ($rol == 1) {
                    echo "<script>
                        if(confirm('Su empresa no cuenta con licencia, ¿Desea enviar un correo al administrador para adquirir una?')) {
                            window.location = '../comprar_licencia.php?id_empresa=" . $id_empresa . "';
                        } else {
                            window.location = '../index.php';
                        }
                    </script>";
                    exit();
                } else {
                    echo "<script>alert('Su empresa no cuenta con licencia, por favor contacte al administrador de su empresa');</script>";
                    echo "<script>window.location='../index.php';</script>";
                    exit();
                }
            } elseif ($licencia['fecha_fin'] < $fecha_actual) {
                if ($rol == 1) {
                    echo "<script>
                        if(confirm('Su licencia ha expirado, ¿Desea enviar un correo al administrador para renovarla?')) {
                            window.location = '../renovar_licencia.php?id_empresa=" . $id_empresa . "';
                        } else {
                            window.location = '../index.php';
                        }
                    </script>";
                    exit();
                } else {
                    echo "<script>alert('La licencia de su empresa ha expirado, por favor contacte al administrador de su empresa');</script>";
                    echo "<script>window.location='../index.php';</script>";
                    exit();
                }
            }

            if ($rol == 1) {
                header("Location: ../admin/index.php");
                exit();
            } else {
                header("Location: ../usuario/index.php");
                exit();
            }

        } elseif (password_verify($passwordDesc, $fila1['password'])){
            
            $_SESSION['documento'] = $fila1['documento'];
            header("Location:../superadmin/index.php");
            exit(); 
    
        } else {

            echo "<script>alert('Contraseña incorrecta. Intenta de nuevo.')</script>";
            echo "<script>window.location='../index.php'</script>";
            exit();
        }

    } elseif ($fila1) {
        if (password_verify($passwordDesc, $fila1['password'])) {

            $_SESSION['documento'] = $fila1['documento'];
            header("Location: ../verificar_rol.php");
            exit();

        } else {

            echo "<script>alert('Contraseña incorrecta. Intenta de nuevo.')</script>";
            echo "<script>window.location='../index.php'</script>";
            exit();
        }
    } else {

        echo "<script>alert('Usuario no encontrado. Intenta de nuevo.')</script>";
        echo "<script>window.location='../index.php'</script>";
        exit();
    }

}

?>