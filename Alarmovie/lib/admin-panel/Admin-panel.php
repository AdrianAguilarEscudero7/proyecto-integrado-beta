<?php
   /**
    * Adrián Aguilar Escudero
    * Proyecto multi-asignatura 'Alarmovie', 2ºDAW
    * Curso 2017-2018
    */

    require_once("../conex/Connection.php"); // Se importa la librería Connection.php
    $conex = getConnection("localhost", "alarmovie"); // Se obtiene conexión

    $limit = $_GET["limit"] ?? 10; // Establece el límite de registros a 10 por defecto
    $offset = $_GET["offset"] ?? 0; // Establece a 0 por defecto, desde que registro se empieza a contar

    // La consulta establecida por defecto
    $sql = "SELECT * FROM users ORDER BY user_id LIMIT $offset, $limit";
    if (isset($_GET["char"])) { // Si escriben en el buscador, devuelve otra consulta personalizada
        $char = $_GET["char"];
        $sql = "SELECT * FROM users WHERE (user_id LIKE '%$char%') OR (name LIKE '%$char%') OR (surname LIKE '%$char%') LIMIT $offset, $limit";
    } else if (isset($_GET["delete"])) { // Si borran un usuario
        $userId = $_GET["userId"];
        $sql2 = "DELETE FROM users WHERE user_id = '$userId';";
        if (!$result2 = setSql($conex, $sql2)) echo "Hubo algún problema. Inténtelo más tarde.";
    }
    
    $type = $_POST["type"] ?? ""; // Recoge si el tipo de acción es una modificación o, inserción
    if (($type != "edit") && ($type != "add")) { // No refresca sin permiso cuando sea insertar o modificar
        $result = setSql($conex, $sql);

        $i = $_GET["iterator"] ?? 1; // Iterador para las filas
        while ($row = $result->fetch_object()) { // Mientras haya resultados devolvemos los registros en una tabla
            echo "<tr id='$i'>";
            echo "<td data-user>$row->user_id</td><td data-pass>".base64_decode($row->password)."</td><td data-name>$row->name</td>";
            echo "<td data-surname>$row->surname</td><td data-email>$row->email</td>";
            echo "<td><button class='waves-effect waves-light btn orange-background' data-user-edit>Editar</button></td>";
            echo "<td><button class='waves-effect waves-light btn orange-background' data-user-delete>Borrar</button></td><td>";
            echo "</tr>";
            $i++;
        }
    } else { // Si es modificación o inserción
        $userEdit = $_POST["userEdit"];
        $passEdit = $_POST["passEdit"];
        $nameEdit = $_POST["nameEdit"];
        $surnameEdit = $_POST["surnameEdit"];
        $emailEdit = $_POST["emailEdit"];
        if ($type == "edit") { // Si es modificación, modificamos el usuario con id elegido

            $userId = $_POST["userId"];
            $sql2 = "UPDATE users SET user_id = '$userEdit', password = '".base64_encode($passEdit)."', name = '$nameEdit', 
                surname = '$surnameEdit', email = '$emailEdit' WHERE user_id = '$userId';";
            if (!$result2 = setSql($conex, $sql2)) { // Si hubo algún error
                echo "Hubo algún problema. Inténtelo más tarde.";
            } else {
                echo "Usuario modificado correctamente.";
            }
        } else { // Si es una inserción
            
            $sql2 = "INSERT INTO users (user_id, password, name, surname, email) 
                VALUES ('$userEdit', '".base64_encode($passEdit)."', '$nameEdit', '$surnameEdit', '$emailEdit');";
            if (!$result2 = setSql($conex, $sql2)) { // Si hubo algún error
                echo "Hubo algún problema. Inténtelo más tarde.";
            } else {
                echo "Usuario insertado correctamente.";
            }
        }
    }
    $conex->close();