<?php 
   /**
    * Adrián Aguilar Escudero
    * Proyecto multi-asignatura 'Alarmovie', 2ºDAW
    * Curso 2017-2018
    */

    require_once("../conex/Connection.php"); // Se importa la librería Connection.php
    $conex = getConnection("localhost", "alarmovie"); // Se obtiene conexión

    if ($_GET["type"] == "generalUpdate") { // Si es un update general (de todas las películas)

        # Se selecciona la fecha del último update que se hizo
        $checkUpdt = "SELECT last_update FROM general_update WHERE update_id = (SELECT MAX(update_id) FROM general_update)";
        $result = setSql($conex, $checkUpdt);
        $lastUpdate = "";
        while ($row = $result->fetch_object()) {
            $lastUpdate = $row->last_update; // Se obtiene dicha fecha
        }
    
        if (time() - strtotime($lastUpdate) > 2*24*3600) { // Si han pasado 2 días desde el último update

            $todayDate = date("Y-m-d", time()); // Se almacena la fecha de hoy

            $delMovies = "DELETE FROM movies WHERE release_date < '$todayDate'";
            $delResult = setSql($conex, $delMovies);

            # Se obtienen los resultados desde la API para almacenar el total de páginas
            $getGeneralMoviesPages = file_get_contents("https://api.themoviedb.org/3/discover/movie?api_key=10f2acad5e16e03e0578d21f0bcd7dce&language=es-ES&sort_by=primary_release_date.asc&primary_release_date.gte=$todayDate");
            $generalMoviesPages = json_decode($getGeneralMoviesPages, true); // Se decodifica el json en array asociativo
            $totalPages = $generalMoviesPages["total_pages"]; // Se almacena el nº total de páginas del resultado

            for ($page = 1; $page <= $totalPages; $page++) { // Se itera por cada página

                # Se obtienen 20 películas desde la API por cada página
                $getGeneralMovies = file_get_contents("https://api.themoviedb.org/3/discover/movie?api_key=10f2acad5e16e03e0578d21f0bcd7dce&language=es-ES&sort_by=primary_release_date.asc&page=$page&primary_release_date.gte=$todayDate");
                $generalMovies = json_decode($getGeneralMovies, true); // Se decodifica el json en array asociativo

                foreach($generalMovies["results"] as $gMovie) { // Se itera por cada película
                    
                    $infoID = $gMovie["id"]; // Se almacena el ID de TMDB de la película
                    $title = $conex->real_escape_string($gMovie["title"]); // Se almacena el título
                    $releaseDate = date($gMovie["release_date"]); // Se almacena la fecha de estreno
                    $language = $gMovie["original_language"]; // Se almacena el lenguaje
                    $synopsis = $conex->real_escape_string($gMovie["overview"]); // Se almacena la sinopsis
                    $poster = "http://image.tmdb.org/t/p/w185".$gMovie["poster_path"]; // Se almacena el póster
                    $popularity = $gMovie["popularity"]; // Se almacena la popularidad

                    # Comprueba si existe en la bbdd el id de esta película
                    $checkInfoID = "SELECT info_id FROM movies WHERE info_id = '$infoID'";
                    $result = setSql($conex, $checkInfoID);

                    if ($result->num_rows) { // Si la película ya está insertada

                        # Se actualizan las películas con la nueva información
                        $updt = "UPDATE movies SET title = '$title', release_date = '$releaseDate', language = '$language', 
                        synopsis = '$synopsis', poster = '$poster', popularity = '$popularity' WHERE info_id = '$infoID'";
                        
                        $resUpdt = setSql($conex, $updt);
                    } else { // Si no lo está, se inserta la película
                        
                        // Inserta la película en la bbdd
                        $ins = "INSERT INTO movies (info_id, title, release_date, language, synopsis, poster, popularity) 
                        VALUES('$infoID', '$title', '$releaseDate', '$language', '$synopsis', '$poster', '$popularity')";

                        $resIns = setSql($conex, $ins);
                    }
                }
            }

            # Se inserta la fecha de hoy como última actualización
            $updtDate = "INSERT INTO general_update (last_update) VALUES('$todayDate')";
            $resUpdtDate = setSql($conex, $updtDate);
            echo "Update completed on ".date("d-m-Y", strtotime($todayDate));
        }
    } else if ($_GET["type"] == "getMoviesInfo") { // Si es un update específico (una película en concreto)
        
        $movieID = $_GET["movieID"]; // Se almacena el ID de la película general seleccionada
        $idGM = $_GET["infoID"]; // Se almacena el ID de TMDB de la película

        # Se obtienen los resultados de la API, de la película con ID de TMDB
        $getMovie = file_get_contents("https://api.themoviedb.org/3/movie/$idGM?api_key=10f2acad5e16e03e0578d21f0bcd7dce&language=es-ES");
        $movie = json_decode($getMovie, true); // Se decodifica el json en array asociativo

        # Se obtienen los resultados de los créditos de la API con ID de TMDB de la película seleccionada
        $getCastDir = file_get_contents("https://api.themoviedb.org/3/movie/$idGM/credits?api_key=10f2acad5e16e03e0578d21f0bcd7dce");
        $castDir = json_decode($getCastDir, true); // Se decodifica el json en array asociativo

        $duration = $movie["runtime"]; // Se almacena la duración
        $director = ""; // Nulo si no encuentra
        foreach($castDir["crew"] as $isDir) { // Itera en los resultados de la organización
            if ($isDir["job"] == "Director") { // Si la película tiene director lo almacena
                $director = $conex->real_escape_string($isDir["name"]);
                break;
            }
        }
        $castSubstr = ""; // Nulo si no encuentra
        $producerSubstr = ""; // Nulo si no encuentra
        $genreSubstr = ""; // Nulo si no encuentra

        foreach($castDir["cast"] as $c) { // Concatena todo el reparto que encuentre
            $castSubstr .= $c["name"].", ";
        }
        $cast = $conex->real_escape_string(substr($castSubstr, 0, -2)); // Quita la última coma + espacio

        foreach($movie["production_companies"] as $pc) { // Concatena todas las productoras que encuentre
            $producerSubstr .= $pc["name"].", ";
        }
        $producer = $conex->real_escape_string(substr($producerSubstr, 0, -2)); // Quita la última coma + espacio

        foreach($movie["genres"] as $gen) { // Concatena todos los géneros que encuentre
            $genreSubstr .= $gen["name"].", "; 
        }
        $genre = $conex->real_escape_string(substr($genreSubstr, 0, -2)); // Quita la última coma + espacio
        
        # Comprueba si existe el id
        $checkID = "SELECT movie_id FROM info_movies WHERE movie_id = '$movieID'";
        $resCheckID = setSql($conex, $checkID);

        if ($resCheckID->num_rows) { // Si existe

            # Actualiza los nuevos datos
            $updtInfo = "UPDATE info_movies SET duration = '$duration', director = '$director', cast = '$cast', 
            producer = '$producer', genre = '$genre' WHERE movie_id = '$movieID'";

            $res = setSql($conex, $updtInfo);
        } else { // Si no hubo actualización, se inserta la información restante

            # Inserta la información restante
            $insertInfo = "INSERT INTO info_movies (movie_id, duration, director, cast, producer, genre) 
            VALUES('$movieID', '$duration', '$director', '$cast', '$producer', '$genre')";

            $res2 = setSql($conex, $insertInfo);
        }

        # Muestra todos los datos de la película seleccionada
        $showAllInfo = "SELECT m.title, m.release_date, m.language, m.synopsis, m.poster, i.duration, i.director, i.cast, 
        i.producer, i.genre FROM movies m, info_movies i WHERE m.movie_id = i.movie_id AND m.movie_id = '$movieID'";

        $resAllInfo = setSql($conex, $showAllInfo);

        while ($row = $resAllInfo->fetch_object()) {
            
            $defaultSyn = $row->synopsis;
    
            if ($defaultSyn == "") { // Si aún no hay una sinopsis asociada
                $defaultSyn = "No hay sinopsis asociada actualmente...";
            }
    
            # Mostramos los datos en el dialog
            echo "
                <div class='row'>
                    <div class='col s6 m6 l4' data-poster><img src='$row->poster' alt='Póster'></div>
                    <div class='col s6 m6 l8'><span class='orange-text2 info-close'>X</span></div>
                    <div class='col s12 m6 l8' data-content>
                        <div class='row'>
                            <div class='col s12'><h1>$row->title</h1><i data-id='$movieID' class='material-icons alarm'>notifications</i></div>
                            <div class='col s12 m12 l6'><span class='orange-text2'>Fecha de estreno: </span>".date("d-m-Y", strtotime($row->release_date))."<hr/></div>
                            <div class='col s12 m12 l6'><span class='orange-text2'>Idioma: </span>".$row->language."<hr/></div>
                            <div class='col s12 m12 l6'><span class='orange-text2'>Director: </span>".$row->director."<hr/></div>
                            <div class='col s12 m12 l6'><span class='orange-text2'>Duración: </span>".$row->duration."<hr/></div>
                            <div class='row no-margin-bottom'>
                                <div class='col s12 m12 l6'><span class='orange-text2'>Productoras: </span>".$row->producer."<hr/></div>
                                <div class='col s12 m12 l6'><span class='orange-text2'>Género: </span>".$row->genre."<hr/></div>
                            </div>
                            <div class='col s12 info-cast'><span class='orange-text2'>Reparto: </span>".$row->cast."</div>
                        </div>
                    </div>
                    <div class='col s12 info-synopsis'><span class='orange-text2'>Sinopsis: </span><hr/>".$defaultSyn."</div>
                </div>
            ";
        }
    }
    $conex->close();