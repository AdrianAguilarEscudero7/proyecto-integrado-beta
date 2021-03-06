<?php
   /**
    * Adrián Aguilar Escudero
    * Proyecto multi-asignatura 'Alarmovie', 2ºDAW
    * Curso 2017-2018
    */

    # Se abre sesión
    session_start();

    require_once("../conex/Connection.php"); // Se importa la librería Connection.php
    $conex = getConnection("localhost", "alarmovie"); // Se obtiene conexión

    $sql2 = "SELECT last_email FROM send_email";
    $result2 = setSql($conex, $sql2);

    if ($result2->num_rows) {
        $lastEmail = $result2->fetch_object()->last_email;
        if (time() - strtotime($lastEmail) > 24*3600) {
            generateAlarmMail($conex);
            $sql4 = "UPDATE send_email SET last_email = '".date("Y-m-d", time())."' WHERE id = 1";
            setSql($conex, $sql4);
        }
    } else {
        generateAlarmMail($conex);
        $sql3 = "INSERT INTO send_email (last_email) VALUES('".date("Y-m-d", time())."')";
        setSql($conex, $sql3);
    }

    function generateAlarmMail($conex) {
        $sql = "SELECT m.title, l.user_id, u.name, u.email FROM movies m, linked l, users u WHERE m.movie_id = l.movie_id AND l.user_id = u.user_id 
            AND m.release_date = CURRENT_DATE";
        $result = setSql($conex, $sql);

        $moviesInfo = [];
        $lastUser = "";
        while ($row = $result->fetch_object()) {
            if ($lastUser != $row->user_id && $lastUser != "") {
                sendMail($moviesInfo);
                $moviesInfo = [];
            }
            $lastUser = $row->user_id;
            $array = [
                "title" => $row->title,
                "name" => $row->name,
                "email" => $row->email
            ];
            $moviesInfo[] = $array;
        }
        sendMail($moviesInfo);
    }

    function sendMail($moviesInfo) {
        $title = "";
        $name = "";
        $email = "";
        foreach($moviesInfo as $m) {
            foreach($m as $campo=>$registro) {
                if ($campo == "title") {
                    $title .= $registro.", ";
                } else if ($campo == "name") {
                    $name = $registro;
                } else {
                    $email = $registro;
                }
            }
        }
        $title = substr($title, 0, -2);

        $subject = "¡$name, hoy hay estrenos!";
        $msg = "
        <html>
        <head>
        <title>Recuperar contraseña</title>
        </head>
        <body>
            <h2>¡Hola $name!</h2>
            <p>Estos son los estrenos para hoy: $title</p>
            <p>¡No te los puedes perder!</p>
        </body>
        </html>";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $headers .= "From: alarmovie@localhost" . "\r\n" .
        "Reply-To: alarmovie@localhost" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();

        mail($email, $subject, $msg, $headers);
    }
    $conex->close();