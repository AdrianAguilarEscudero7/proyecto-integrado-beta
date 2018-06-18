<?php
    # Se abre sesión
    session_start();
    
    # Redirige en caso de existir ya una sesión
    if (isset($_SESSION["id"])) header("Location: main.php");

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
    <link rel="icon" type="image/png" href="./img/AlarmovieFavicon.png"/>
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
    <img style="position: absolute; top: 0; z-index: -1;" src="img/cinema2.jpeg" alt="" width="100%" height="100%">
    <div class="row">
        <div class="index-info">
            <form id="sign-in-form" method="post">
                <div class="input-field col s12">
                    <input id="user-id" type="text" maxlength="16" name="userId">
                    <label for="password">Usuario</label>
                </div>
                <div class="input-field col s12">
                    <input id="password" type="password" maxlength="16" name="pass">
                    <label for="password">Contraseña</label>
                </div>
                <div class="sub-form">
                    <p>
                        <span id="sign-in-validate"></span>
                        <span style="display: none;" id="forgot-pass"><a href="#">¿Es posible que los haya olvidado?</a></span>
                    </p>
                    <button class="waves-effect waves-light btn" type="submit" id="send">Entrar</button>
                </div>
            </form>
            <p><a id="reg-modal-dialog" href="#">¿Aún no tiene una cuenta?</a></p>
            <span id="reg-success"></span>
            <span id="session-expired"><?= isset($_GET["expired"]) ? "La sesión expiró. Inicie sesión nuevamente." : ""?></span>
        </div>
    </div>
    <div style="display: none;" id="pass-recovery">
        <form id="pass-recovery-form" method="post">
            <p>
                No se preocupe, introduzca su dirección de correo electrónico y 
                antes de que se estrene su película favorita le mandaremos un correo para recuperar su contraseña.
            </p>
            <div class="input-field">
                <input id="mail-pass-recovery" type="text" maxlength="40" name="mailPassRecovery">
                <label for="confirm-new-password">Correo electrónico</label>
            </div>
            <span id="mail-pass-recovery-valid"></span>
        </form>
    </div>
    <div style="display: none;" id="change-pass-request">
        <form id="change-pass-form" method="post">
            <?php
                if (isset($_GET["passRecovery"])) {
                    $email = $_GET["email"] ?? "";
                    $token = $_GET["token"] ?? "";
                    $sql = "SELECT user_id FROM users WHERE email = '$email'"; // Se obtiene el id del usuario
                    $result = setSql($conex, $sql);
                    $row = $result->fetch_object();
                    $userId = $row->user_id ?? "";
                
                    // Se obtiene el tiempo de la solicitud y si es válido o no
                    $sql2 = "SELECT last_recovery, valid FROM pass_recovery WHERE user_id = '$userId' AND token = '$token'";
                    $result2 = setSql($conex, $sql2);
                    $row2 = $result2->fetch_object();
                    if ($result2->num_rows) { // Si coinciden usuario y token
                        $requestTime = $row2->last_recovery;
                        $valid = $row2->valid;
                    
                        if ((time()-strtotime($requestTime)) >= 7200) { // Si han pasado 2 horas o más
                            echo "<p>Lo sentimos, su enlace ha expirado. Recuerde que el tiempo de expiración es de 2 horas. Por favor solicite otro cambio de contraseña, gracias.</p>";
                        } else {
                            if ($valid == 0) { // Si ya no es válido
                                ?>
                                    <p>El cambio de contraseña solicitado para este enlace ya ha sido efectuado. Por favor, espere 2 horas para solicitar otro.</p>
                                    <script>
                                        $(document).ready(function() {
                                            $('#change-pass-request').dialog('option', 'buttons', {});
                                        });
                                    </script>
                                <?php
                            } else { // Si es válido, muestra los campos para cambiar la contraseña
                                ?>
                                    <div class="input-field">
                                        <input id="new-password" type="password" maxlength="16">
                                        <label for="new-password">Nueva contraseña</label>
                                    </div>
                                    <div class="input-field">
                                        <input id="confirm-new-password" type="password" maxlength="16">
                                        <label for="confirm-new-password">Confirmar nueva contraseña</label>
                                    </div>
                                    <input type="hidden" id="change-pass-id" name="changePassId" value="<?= $userId ?>">
                                    <span id="change-pass-valid"></span>
                                <?php
                            }
                        }
                        ?>  <!-- Abre el modal para cambiar la contraseña -->
                            <script>
                                $(document).ready(function() {
                                    $("#change-pass-request").dialog("open");
                                });
                            </script>
                        <?php
                    }
                }
            ?>
        </form>
    </div>
    <div style="display: none;" id="registry-form">
        <form id="sign-up-form" method="post">
            <p>*Todos los campos son requeridos.</p>
            <p>
                <input type="text" name="regUserId" id="reg-user-id" maxlength="16" placeholder="Usuario">
                <span id="reg-user-valid"></span>
            </p>
            <p>
                <input type="password" name="regPass" id="reg-password" maxlength="16" placeholder="Contraseña">
                <span id="reg-pass-valid"></span>
            </p>
            <p>
                <input type="password" name="regPass2" id="reg-password2" maxlength="16" placeholder="Confirmar contraseña">
                <span id="reg-pass2-valid"></span>
            </p>
            <p>
                <input type="text" name="regName" id="reg-name" maxlength="40" placeholder="Nombre">
                <span id="reg-name-valid"></span>
            </p>
            <p>
                <input type="text" name="regSurname" id="reg-surname" maxlength="40" placeholder="Apellidos">
                <span id="reg-surname-valid"></span>
            </p>
            <p>
                <input type="text" name="regMail" id="reg-mail" maxlength="40" placeholder="Correo electrónico">
                <span id="reg-mail-valid"></span>
            </p>
            <span id="reg-form-valid"></span>
        </form>
    </div>
</body>
</html>