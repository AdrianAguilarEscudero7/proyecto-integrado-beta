<?php
    require_once("lib/session/Session.php");
    if (!isset($_SESSION["admin"])) header("Location: main.php");
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
    <div class="container">
        <div class="row">
            <div class="col s12"><a style="padding-right: 30px;" href="admin.php?sessionClose">Cerrar sesión</a><a href="main.php">Página principal</a></div>
        </div>
        <div id="admin-panel" class="col s12">
            <table id="admin-table" class="centered">
                <tr id="table-header">
                    <th>Usuario</th>
                    <th>Contraseña</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Correo electrónico</th>
                    <th><button class="waves-effect waves-light btn orange-background" id="add-user">Añadir</button></th>
                    <th colspan="2"><input id="search-user" type="text" placeholder="Búsqueda de usuarios" autofocus></th>
                </tr>
            </table>
            <div id="show-more-users"></div>
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
            <div style="display: none;" id="user-admin-modal">
                <div class="row">
                    <form id="user-admin-form" method="post" class="col s12">
                        <div class="row no-margin-bottom">
                            <div class="input-field col s2">
                                <input type="text" name="userId" id="admin-user" maxlength="16" placeholder="Usuario">
                            </div>
                            <div class="input-field col s2">
                                <input type="text" name="pass" id="admin-pass" maxlength="16" placeholder="Contraseña">
                            </div>
                            <div class="input-field col s2">
                                <input type="text" name="name" id="admin-name" maxlength="40" placeholder="Nombre">
                            </div>
                            <div class="input-field col s2">
                                <input type="text" name="surname" id="admin-surname" maxlength="40" placeholder="Apellidos">
                            </div>
                            <div class="input-field col s2">
                                <input type="text" name="email" id="admin-email" maxlength="40" placeholder="Correo electrónico">
                            </div>
                            <div class="input-field col s2">
                                <button class="waves-effect waves-light btn orange-background" id="admin-modal-submit"></button>
                            </div>
                        </div>
                        <div class="row no-margin-bottom">
                            <div class="col s12">
                                <p id="admin-response"></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="confirm-delete-user"></div>
        </div>
    </div>
</body>
</html>