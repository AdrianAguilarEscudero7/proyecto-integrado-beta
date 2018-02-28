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

    # Si es un inicio de sesión, se comprueba en la bbdd si el usuario y contraseña es correcto
    if ($_POST["form"] == "signIn") {
        $userId = $conex->real_escape_string($_POST["user"]);
        $pass = $conex->real_escape_string($_POST["password"]);

        // Se valida longitud de usuario y pass
        if (strlen($userId) > 16 || strlen($pass) > 16) {
            echo "Ha debido introducir más caracteres de lo permitido.";
        } else {
            $sql = "SELECT * FROM users WHERE BINARY user_id = '$userId' AND password = '".base64_encode($pass)."';";
            $result = setSql($conex, $sql);
            $row = $result->fetch_object();

            // Si existe en la bbdd, se guardan las variables de sesión necesarias
            if ($result->num_rows) {
                if ($userId == "administrador") {
                    $_SESSION["id"] = session_id();
                    $_SESSION["time"] = time();
                    $_SESSION["username"] = $row->user_id;
                    $_SESSION["name"] = $row->name;
                    $_SESSION["admin"] = "admin";
                } else {
                    $_SESSION["id"] = session_id();
                    $_SESSION["username"] = $row->user_id;
                    $_SESSION["name"] = $row->name;
                    $_SESSION["time"] = time();   
                }
                echo true;
            } else {
                echo false;
            }
        }
    }
    
    # Si es una comprobación de usuario disponible, devuelve verdadero o falso según el caso
    if ($_POST["form"] == "checkUser") {
        $regUserId = $conex->real_escape_string($_POST["regUserId"]);
        $sql = "SELECT user_id FROM users WHERE user_id = '$regUserId';";
        $result = setSql($conex, $sql);
        
        if ($result->num_rows) { // Si el usuario ya existe
            echo false; 
        } else {
            echo true;
        }
    }

    # Si es un registro, se valida y se lleva a cabo
    if ($_POST["form"] == "signUp") {
        $regUserId = $conex->real_escape_string($_POST["regUserId"]);
        $regPass = $conex->real_escape_string($_POST["regPass"]);
        $regName = $conex->real_escape_string($_POST["regName"]);
        $regSurname = $conex->real_escape_string($_POST["regSurname"]);
        $email = $conex->real_escape_string($_POST["email"]);
        $regExpUserPass = "/^[a-zA-Z][a-zA-Z0-9]{5,16}$/"; // Expresión regular letras y números
        $regExpNameSurname = "/^([A-ZÁÉÍÓÚ][a-zñáéíóú]+[\s]*){1,40}$/"; // Expresión regular solo letras con la primera mayúscula
        $regExpMail = "/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i"; // Expresión regular formato correo

        if (preg_match($regExpUserPass, $regUserId) && preg_match($regExpUserPass, $regPass) && preg_match($regExpNameSurname, $regName) 
        && preg_match($regExpNameSurname, $regSurname) && preg_match($regExpMail, $email)) {
            $sql = "INSERT INTO users (user_id, password, name, surname, email) VALUES ('$regUserId', '".base64_encode($regPass)."',
                '$regName', '$regSurname', '$email');";
            if (setSql($conex, $sql)) {
                echo true;    
            } else { // Si hay un fallo en la consulta
                echo false;
            }
        } else { // Si falla la validación de los datos
            echo "Ha habido un error con los datos de los campos del registro.";
        }
    }
    $conex->close();