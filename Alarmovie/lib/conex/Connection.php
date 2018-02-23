<?php
    /*
    * Adrián Aguilar Escudero
    * Proyecto multi-asignatura 'Alarmovie', 2ºDAW
    * Curso 2017-2018
    */

    # Se obtiene conexión de la bbdd.
    function getConnection($server, $dbName) {
        $connection = @new mysqli($server, "root", "", $dbName) or die ("#Error: ".$connection->connect_error);
        $connection->set_charset("utf8");
        return $connection;
    }

    # Se ejecuta una sentencia sql, devolverá objeto result en caso de que la sentencia sea SELECT o similar.
    function setSql($connection, $sql) {
        $result = $connection->query($sql);
        return $result;
    }