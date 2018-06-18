<?php
   /**
    * Adrián Aguilar Escudero
    * Proyecto multi-asignatura 'Alarmovie', 2ºDAW
    * Curso 2017-2018
    */

    require_once("../conex/Connection.php"); // Se importa la librería Connection.php
    $conex = getConnection("localhost", "alarmovie"); // Se obtiene conexión

    if ($_POST["type"] == "changePassRequest") {
        $emailPassRecovery = $conex->real_escape_string($_POST["emailPassRecovery"]);
        $sql = "SELECT user_id, name FROM users WHERE email = '$emailPassRecovery'";
        $result = setSql($conex, $sql);

        if ($result->num_rows) { // Si existe en la bbdd
            $row = $result->fetch_object();
            $name = $row->name;
            $userId = $row->user_id;

            $sql2 = "SELECT user_id, last_recovery FROM pass_recovery WHERE user_id = '$userId'";
            $result2 = setSql($conex, $sql2);

            if ($result2->num_rows) { // Si existe una solicitud anterior de este usuario
                $row2 = $result2->fetch_object();
                $lastRequest = $row2->last_recovery;

                if ((time()-strtotime($lastRequest)) < 7200) { // Si no han pasado 2 horas no se puede mandar nueva solicitud
                    echo "requestoncourse";
                } else { // A las 2 horas, se renueva la solicitud
                    $newToken = generateRandomString(32);
                    $sql4 = "UPDATE pass_recovery SET token = '$newToken', valid = 1 WHERE user_id = '$userId'";
                    if (setSql($conex, $sql4)) {
                        sendMail($emailPassRecovery, $newToken, $name);
                        echo "mailsent";
                    } else {
                        echo "errorquery";
                    }
                }
            } else { // Crea una solicitud de recuperar contraseña para este usuario
                $token = generateRandomString(32);
                $sql3 = "INSERT INTO pass_recovery (user_id, token) VALUES('$userId', '$token')";

                if (setSql($conex, $sql3)) { // Si la consulta no da error, envía el correo al destinatario
                    sendMail($emailPassRecovery, $token, $name);
                    echo "mailsent";
                } else { // Si hay un fallo en la consulta
                    echo "errorquery";
                }
            }
        } else {
            echo "noexists";
        }
    } else if ($_POST["type"] == "changePass") {
        $newPass = $conex->real_escape_string($_POST["newPass"]);
        $changePassUserID = $_POST["changePassUserID"];

        $sql = "SELECT valid FROM pass_recovery WHERE user_id = '$changePassUserID'";
        $result = setSql($conex, $sql);
        $row = $result->fetch_object();
        $valid = $row->valid ?? "";

        if ($valid != 0) {
            $sql2 = "UPDATE users SET password = md5('$newPass') WHERE user_id = '$changePassUserID'";

            if (setSql($conex, $sql2)) {
                $sql3 = "UPDATE pass_recovery SET valid = 0 WHERE user_id = '$changePassUserID'";
                setSql($conex, $sql3);
                echo "changesuccess";
            } else {
                echo "changefail";
            }
        } else {
            echo "alreadychanged";
        }
    }

    // Genera un token
    function generateRandomString($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    // Envía un email al destinatario que solicitó el cambio de contraseña
    function sendMail($to, $token, $name) {
        $subject = "Recuperar contraseña";
        $msg = "
        <html>
        <head>
        <title>Recuperar contraseña</title>
        </head>
        <body>
            <h2>¡Hola $name!</h2>
            <p>Si ha solicitado un cambio de contraseña <a href='http://localhost/alarmovie?passRecovery=true&email=$to&token=$token'>Pulse aquí</a> para restablecerla.</p>
            <p>Si tiene problemas para pulsar el botón, copie esto en su navegador -></p>
            <p><a href='http://localhost/alarmovie?passRecovery=true&email=$to&token=$token'>http://localhost/alarmovie?passRecovery=true&email=$to&token=$token</a></p>
            <p>Si usted no ha solicitado nada, por favor ignore este correo.</p>
            <p><i>&lt;Este enlace tiene una validez de 2 horas&gt;</i></p>
        </body>
        </html>";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $headers .= "From: alarmovie@localhost" . "\r\n" .
        "Reply-To: alarmovie@localhost" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();

        mail($to, $subject, $msg, $headers);
    }
    $conex->close();