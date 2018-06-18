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