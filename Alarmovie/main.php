<?php
    require_once("lib/session/Session.php");

    require_once("lib/conex/Connection.php"); // Se importa la librería Connection.php
    $conex = getConnection("localhost", "alarmovie"); // Se obtiene conexión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alarmovie | La alarma de los estrenos</title>
    <link rel="icon" type="image/png" href="./img/AlarmovieFavicon.png" />
    <link type="text/css" rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/styles.css"/>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
    <script src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="js/push.js/bin/push.min.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>
    <div class="row">
        <nav class="orange-background">
            <div class="nav-wrapper col s12">
                <a href="main.php" class="brand-logo">Alarmovie</a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><?= isset($_SESSION["admin"]) ? "<a href='admin.php'>Administración</a>" : ""; ?></li>
                    <li><a href="main.php?sessionClose">Cerrar sesión</a></li>
                </ul>
                <ul class="sidenav" id="mobile-demo">
                    <li><?= isset($_SESSION["admin"]) ? "<a href='admin.php'>Administración</a>" : ""; ?></li>
                    <li><a href="main.php?sessionClose">Cerrar sesión</a></li>
                </ul>
            </div>
        </nav>
    </div>
    <div id="main" class="container">
        <div class="row">
            <div id="datepicker-section">
                <div class="col s12" id="date"></div>
                <div style="display: none;" id="linked-movies-dialog"></div>
            </div>
            <div class="col s12" id="movies-section"></div>
            <div class="col s12" id="show-more-movies"></div>
            <div class="preloader">
                <div class="preloader-wrapper medium active">
                    <div class="spinner-layer spinner-green-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="info-movie-dialog"></div>
        </div>
    </div>
    <?php
        $user = $_SESSION["username"];
        $name = $_SESSION["name"];
        $sql = "SELECT m.title, m.release_date, m.poster FROM movies m, linked l WHERE m.movie_id = l.movie_id AND l.user_id = '$user'";
        $result = setSql($conex, $sql);

        while ($row = $result->fetch_object()) {
            
            if ((date("Y-m-d", time())) == $row->release_date) {
                ?>
                    <script>
                        Push.create('¡Hola <?= $name ?>!', {
                            body: 'Hoy se estrena <?= $row->title ?> ¡No te la pierdas!',
                            icon: '<?= $row->poster ?>',
                            timeout: 8000,
                            onClick: function () {
                                window.focus();
                                this.close();
                            }
                        });
                    </script>
                <?php
            }
        }
    ?>
</body>
</html>