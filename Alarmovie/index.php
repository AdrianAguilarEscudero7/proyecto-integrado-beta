<?php
    # Se abre sesión
    session_start();
    
    /*if (isset($_SESSION["id"])) {
        header("Location: main.php");
    }*/
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
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script src="js/scripts.js"></script>
</head>
<body>
    <form id="sign-in-form" method="post" autocomplete="on">
        <p><input type="text" name="userId" id="user-id" maxlength="16" autocomplete="on" placeholder="Usuario"></p>
        <p><input type="password" name="pass" id="password" maxlength="16" autocomplete="on" placeholder="Contraseña"></p>
        <p>
            <span id="sign-in-validate"></span>
            <span style="display: none;" id="forgot-pass"><a href="#">¿Es posible que los haya olvidado?</a></span>
        </p>
        <button type="submit" id="send">Entrar</button>
    </form>
    <p><a id="reg-modal-dialog" href="#">¿Aún no tiene una cuenta?</a></p>
    <span id="reg-success"></span>
    <span id="session-expired"><?= isset($_GET["expired"]) ? "La sesión expiró. Inicie sesión nuevamente." : ""?></span>
    <div style="display: none;" id="registry-form">
        <form id="sign-up-form" method="post" autocomplete="on">
            <p>*Todos los campos son requeridos.</p>
            <p>
                <input type="text" name="regUserId" id="reg-user-id" maxlength="16" autocomplete="on" placeholder="Usuario">
                <span id="reg-user-valid"></span>
            </p>
            <p>
                <input type="password" name="regPass" id="reg-password" maxlength="16" autocomplete="on" placeholder="Contraseña">
                <span id="reg-pass-valid"></span>
            </p>
            <p>
                <input type="password" name="regPass2" id="reg-password2" maxlength="16" autocomplete="on" placeholder="Confirmar contraseña">
                <span id="reg-pass2-valid"></span>
            </p>
            <p>
                <input type="text" name="regName" id="reg-name" maxlength="40" autocomplete="on" placeholder="Nombre">
                <span id="reg-name-valid"></span>
            </p>
            <p>
                <input type="text" name="regSurname" id="reg-surname" maxlength="40" autocomplete="on" placeholder="Apellidos">
                <span id="reg-surname-valid"></span>
            </p>
            <p>
                <input type="text" name="regMail" id="reg-mail" maxlength="40" autocomplete="on" placeholder="Correo electrónico">
                <span id="reg-mail-valid"></span>
            </p>
            <span id="reg-form-valid"></span>
        </form>
    </div>
</body>
</html>