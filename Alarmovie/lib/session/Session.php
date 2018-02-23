<?php 
   /**
    * Adrián Aguilar Escudero
    * Proyecto multi-asignatura 'Alarmovie', 2ºDAW
    * Curso 2017-2018
    */
    
    # Se abre sesión.
    session_start();

    # Si no hay abierta una sesión para dicho usuario, se le manda al índice.
	if (!isset($_SESSION["id"])) {
        header("Location: index.php");
        exit();
    }

    # La sesión expira a los 30 minutos.
    if (!isset($_SESSION["admin"]) && time() - $_SESSION["time"] > 1800) {
        header("Location: index.php?expired");
        $_SESSION["id"] = array();
        session_destroy();
        exit();
    }

    # Cierra la sesión del usuario.
    if (isset($_GET["sessionClose"])) {
        header("Location: index.php");
        $_SESSION["id"] = array();
        session_destroy();
        exit();
    }

    # Actualiza la sesión del usuario.
    $_SESSION["time"] = time();