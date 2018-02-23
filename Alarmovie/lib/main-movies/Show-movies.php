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

    # Selecciona los datos que se van a mostrar de manera general en el main
    $sql = "SELECT movie_id, info_id, title, release_date, language, synopsis, poster FROM movies 
    ORDER BY release_date, popularity DESC LIMIT $offset, $limit";

    $result = setSql($conex, $sql);

    while ($row = $result->fetch_object()) {

        $defaultSyn = $row->synopsis;
        
        if ($defaultSyn == "") { // Si aún no hay una sinopsis asociada
            $defaultSyn = "No hay sinopsis asociada actualmente...";
        }

        # Mostramos los datos en el main
        echo "
            <div class='row movie-card'>
                <div class='col s12 movie-container' data-id='$row->movie_id' data-info-id='$row->info_id'>
                    <div class='col s12 m6 l4' data-poster><img src='$row->poster' alt='No hay póster'></div>
                    <div class='col s12 m6 l8' data-content>
                        <div class='row'>
                            <div class='col s12'><h1>$row->title</h1></div>
                            <div class='col s12 m12 l6'><span class='orange-text'>Fecha de estreno: </span>".$row->release_date."<hr/></div>
                            <div class='col s12 m12 l6'><span class='orange-text'>Idioma: </span>".$row->language."<hr/></div>
                            <div class='col s12 general-synopsis'><span class='orange-text'>Sinopsis: </span><hr/>".$defaultSyn."</div>
                        </div>
                    </div>
                </div>
                <div class='preloader preloader-movie' data-id='$row->movie_id'>
                    <div class='preloader-wrapper big active'>
                        <div class='spinner-layer spinner-green-only'>
                            <div class='circle-clipper left'>
                                <div class='circle'></div>
                            </div>
                            <div class='gap-patch'>
                                <div class='circle'></div>
                            </div>
                            <div class='circle-clipper right'>
                                <div class='circle'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }