<?php
    require_once("lib/session/Session.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alarmovie | La alarma de los estrenos</title>
    <link type="text/css" rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.css"/>
    <link type="text/css" rel="stylesheet" href="css/materialize/css/materialize.min.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/styles.css"/>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>
    <div id="main" class="container">
        <div class="row">
            <div class="col s12"><a style="padding-right: 30px;" href="main.php?sessionClose">Cerrar sesión</a>
            <?= isset($_SESSION["admin"]) ? "<a href='admin.php'>Administración</a>" : ""; ?>
        </div>
        </div>
        <div id="datepicker-section">
            <div class="col s12" id="date"></div>
            <div style="display: none;" id="linked-movies-dialog"></div>
        </div>
        <div class="col s12" id="movies-section"></div>
        <div id="show-more-movies"></div>
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
</body>
</html>