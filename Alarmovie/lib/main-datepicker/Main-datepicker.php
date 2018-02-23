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
    
    $userName = $_SESSION["username"] ?? ""; // Se obtiene el usuario de la sesión
    
    if ($_POST["type"] == "checkUserMovie") { // Si se quiere comprobar si hay una vinculación

        $movieID = $_POST["movieID"] ?? ""; // Se obtiene el id de la película

        # Se consulta si ya existe dicho usuario con dicha película asociada
        $check = "SELECT user_id, movie_id FROM linked WHERE user_id = '$userName' AND movie_id = '$movieID'";

        $res = setSql($conex, $check);

        if ($res->num_rows) { // Si existe
            echo "off";
        }

    } else if ($_POST["type"] == "linkUserMovie") { // Si se quiere vincular al usuario con una película

        $movieID = $_POST["movieID"] ?? ""; // Se obtiene el id de la película

        # Se consulta si ya existe dicho usuario con dicha película asociada
        $check = "SELECT user_id, movie_id FROM linked WHERE user_id = '$userName' AND movie_id = '$movieID'";
        
        $res = setSql($conex, $check);

        if ($res->num_rows) { // Si existe

            # Se desvincula al usuario con la película
            $unlink = "DELETE FROM linked WHERE user_id = '$userName' AND movie_id = '$movieID'";
        
            if (!$res = setSql($conex, $unlink)) { // Si hubo algún error
                echo false;
            } else {
                echo "on";
            }
        } else {

            # Se vincula al usuario con la película
            $link = "INSERT INTO linked (user_id, movie_id) VALUES('$userName', '$movieID')";
        
            if (!$res = setSql($conex, $link)) { // Si hubo algún error
                echo false;
            } else {
                echo "off";
            }
        }
        
    } else if ($_POST["type"] == "getDates") { // Si se piden las fechas
        # Selecciona las películas y fechas de estreno de éstas, que dicho usuario tiene en favoritos
        $sql = "SELECT m.release_date FROM movies m, linked l WHERE l.user_id = '$userName' AND m.movie_id = l.movie_id";

        $result = setSql($conex, $sql);

        $dateMovies = array();
        while ($row = $result->fetch_object()) { // Almacena en un array los resultados
            $dateMovies[] = array(
                "releaseDate" => $row->release_date
            );
        }
        $datesUI = $_POST["datesUI"] ?? "";
        $datesTrue = array();
        foreach ($datesUI as $date) { // Construye un array con las películas coincidentes
            foreach ($dateMovies as $dateM) {
                if ($date == $dateM["releaseDate"]) {
                    $datesTrue[] = array(
                        "dateTrue" => $date
                    );
                    break;
                }
            }
        }
        echo json_encode($datesTrue); // Codifica en formato json para poder enviar el array como respuesta ajax
        
    } else if ($_POST["type"] == "getMovies") { // Si se piden las películas
        $date = $_POST["date"] ?? "";
        $sql = "SELECT m.title FROM movies m, linked l WHERE l.user_id = '$userName' AND m.release_date = '$date'
            AND m.movie_id = l.movie_id ORDER BY title";

        $result = setSql($conex, $sql);

        $movies = array();
        while ($row = $result->fetch_object()) { // Almacena en un array los resultados
            $movies[] = array(
                "title" => $row->title
            );
        }
        echo json_encode($movies); // Codifica en formato json para poder enviar el array como respuesta ajax
    }
    $conex->close();

