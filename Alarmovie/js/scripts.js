$(document).ready(function() {

    //------------------- Sign-in validaciones -------------------//

    let forgotPass = 0;

    // Envía el formulario de inicio de sesión al servidor y comprueba los datos
    $("#send").click(function() {
        let userId = $("#user-id").val();
        let pass = $("#password").val();
        $.post("lib/sign-in-up/Sign-in-up.php",
        {
            form: "signIn",
            user: userId,
            password: pass
        },
        function(response) {
            if (!response) {
                forgotPass++;
                $("#reg-success").text("");
                $("#session-expired").text("");
                $("#sign-in-validate").text("Usuario o contraseña incorrectos.");
                if (forgotPass >= 3) {
                    $("#forgot-pass").show();
                }
            } else if (response == true) {
                forgotPass = 0;
                if (userId === "administrador") {
                    location.href = "admin.php";
                } else {
                    location.href = "main.php";
                }
            } else {
                $("#reg-success").text("");
                $("#session-expired").text("");
                $("#sign-in-validate").text(response);
                $("#forgot-pass").hide();
                forgotPass = 0;
            }
        });
        return false;
    });

    //------------------- Sign-up funciones -------------------//

    let valid = new Array(); // Se inicializa un valid en falso por cada campo por defecto
    for (let i = 0; i < 7; i++) {
        valid[i] = false;
    }
    let checkUser = false; // Da permiso para realizar la consulta de comprobación de usuario
    let regExpUserPass = /^[a-zA-Z][a-zA-Z0-9]{5,16}$/; // Expresión regular letras y números
    let regExpNameSurname = /^([A-ZÁÉÍÓÚ][a-zñáéíóú]+[\s]*){1,40}$/; // Expresión regular solo letras con la primera mayúscula
    let regExpMail = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i; // Expresión regular formato correo
    
    // Valida el usuario
    function userValidate() {
        let regUserId = $("#reg-user-id").val();
        if (!regExpUserPass.test(regUserId)) {
            $("#reg-user-valid").text("Se permiten letras y números de 6 a 16 caracteres.");
            valid[0] = false;
            checkUser = false;
        } else {
            $("#reg-user-valid").text("");
            valid[0] = true;
            checkUser = true;
        }
    }
    
    // Valida la contraseña
    function passValidate() {
        let regPass = $("#reg-password").val();
        if (!regExpUserPass.test(regPass)) {
            $("#reg-pass-valid").text("Se permiten letras y números de 6 a 16 caracteres.");
            valid[1] = false;
        } else {
            $("#reg-pass-valid").text("");
            valid[1] = true;
        }
    }

    // Comprueba que las contraseñas sean iguales
    function passValidate2() {
        let regPass = $("#reg-password").val();
        let regPass2 = $("#reg-password2").val();
        if (regPass2 != regPass) {
            $("#reg-pass2-valid").text("Las contraseñas deben coincidir.");
            valid[2] = false;
        } else {
            $("#reg-pass2-valid").text("");
            valid[2] = true;
        }
    }

    // Valida el nombre
    function nameValidate() {
        let regName = $("#reg-name").val();
        if (!regExpNameSurname.test(regName)) {
            $("#reg-name-valid").text("Se permiten letras con la primera mayúscula.");
            valid[3] = false;
        } else {
            $("#reg-name-valid").text("");
            valid[3] = true;
        }
    }

    // Valida el/los apellidos
    function surnameValidate() {
        let regSurname = $("#reg-surname").val();
        if (!regExpNameSurname.test(regSurname)) {
            $("#reg-surname-valid").text("Se permiten letras con la primera mayúscula.");
            valid[4] = false;
        } else {
            $("#reg-surname-valid").text("");
            valid[4] = true;
        }
    }

    // Valida el correo
    function emailValidate() {
        let email = $("#reg-mail").val();
        if (!regExpMail.test(email)) {
            $("#reg-mail-valid").text("Introduzca un formato de correo válido.");
            valid[5] = false;
        } else {
            $("#reg-mail-valid").text("");
            valid[5] = true;
        }
    }

    // Limpia los campos de mensajes de validación
    function resetMsg() {
        $("#reg-user-valid").text("");
        $("#reg-pass-valid").text("");
        $("#reg-pass2-valid").text("");
        $("#reg-name-valid").text("");
        $("#reg-surname-valid").text("");
        $("#reg-mail-valid").text("");
        $("#reg-form-valid").text("");
        checkUser = false;
    } 
    
    //------------------- Sign-up validaciones ------------------//

    $("#reg-user-id").on("input", function() {
        userValidate(); 
    });

    // Comprueba si existe el usuario cuando el campo pierde el focus
    $("#reg-user-id").blur(function() {
        let regUserId = $("#reg-user-id").val();
        if (checkUser) {
            $.post("lib/sign-in-up/Sign-in-up.php",
            {
                form: "checkUser",
                regUserId: regUserId
            },
            function(response) {
                if (!response) {
                    $("#reg-user-valid").text("El usuario no está disponible.");
                    valid[6] = false;
                } else {
                    $("#reg-user-valid").text("Usuario disponible.");
                    valid[6] = true;
                }
            });
        }
    });

    $("#reg-password").on("input", function() {
        passValidate();
        passValidate2();
    });

    $("#reg-password2").on("input", function() {
        passValidate2();
    });

    $("#reg-name").on("input", function() {
        nameValidate();
    });

    $("#reg-surname").on("input", function() {
        surnameValidate();
    });

    $("#reg-mail").on("input", function() {
        emailValidate();
    });

    //------------------- Formulario modal de registro -------------------//

    $("#registry-form").dialog({
        title: "Registro",
        autoOpen: false,
        width: 650,
        maxWidth: 650,
        height: "auto",
        resizable: false,
        draggable: false,
        modal: true,
        fluid: true,
        close: function() {$("#sign-up-form")[0].reset(); resetMsg();},
        buttons: {
            Borrar: function() {
                $("#sign-up-form")[0].reset();
                resetMsg();
            },
            "Crear cuenta": function() { // Cuando se crea la cuenta, comprueba la validación y manda los datos al servidor
                let campos = $("#sign-up-form input");
                if (!valid[0] || !valid[1] || !valid[2] || !valid[3] || !valid[4] || !valid[5] || !valid[6] || campos.val() === "") {
                    $("#reg-form-valid").text("Complete correctamente todos los campos.");
                } else {
                    let regUserId = $("#reg-user-id").val();
                    let regPass = $("#reg-password").val();
                    let regName = $("#reg-name").val();
                    let regSurname = $("#reg-surname").val();
                    let email = $("#reg-mail").val();
                    $.post("lib/sign-in-up/Sign-in-up.php",
                    {
                        form: "signUp",
                        regUserId: regUserId,
                        regPass: regPass,
                        regName: regName,
                        regSurname: regSurname,
                        email: email
                    },
                    function(response) {
                        if (!response) {
                            $("#reg-form-valid").text("Hubo algún error en la consulta. Inténtelo de nuevo más tarde.");
                        } else if (response == true) {
                            $("#registry-form").dialog("close");
                            $("#reg-success").text("Su cuenta ha sido creada con éxito. Inicie sesión si lo desea.");
                        } else {
                            $("#reg-form-valid").text(response);
                        }
                    });
                }
            }
        }
    });
        
    // Cuando se redimensiona la ventana, ejecuta la función
    $(window).resize(function () {
        fluidDialog();
    });

    // Captura el dialog si es abierto dentro de un viewport mas pequeño que la anchura del dialog
    $(document).on("dialogopen", ".ui-dialog", function (event, ui) {
        fluidDialog();
    });

    // Hace el dialog responsive
    function fluidDialog() {
        var $visible = $(".ui-dialog:visible");
        // Cada dialog abierto
        $visible.each(function () {
            var $this = $(this);
            var dialog = $this.find(".ui-dialog-content").data("ui-dialog");
            // Si la opción fluid es true
            if (dialog.options.fluid) {
                var wWidth = $(window).width();
                // Comprueba la anchura de la ventana con la anchura del dialog
                if (wWidth < (parseInt(dialog.options.maxWidth) + 50))  {
                    // Evita que el dialog llene toda la pantalla
                    $this.css("max-width", "90%");
                } else {
                    // Arregla el bug del maxWidth
                    $this.css("max-width", dialog.options.maxWidth + "px");
                }
                // Reposiciona el dialog
                dialog.option("position", dialog.options.position);
            }
        });
    }

    // Abre la ventana modal de registro
    $("#reg-modal-dialog").click(function() {
        $("#reg-success").text("");
        $("#session-expired").text("");
        $("#registry-form").dialog("open");
    });

    //------------------- Panel de administración -------------------//

    $("#show-more-users").html($(".preloader").html()); // Inserta el preloader

    // Función para mostrar los usuarios
    function displayUsers(limit, offset, lastRowId) {
        $("#show-more-users").show();
        $.get("lib/admin-panel/Admin-panel.php", 
        {
            limit: limit,
            offset: offset,
            iterator: lastRowId
        }, function(response) {
            $("#admin-table").append(response);
            if (response == "") { // Si no devuelve más registros, limpia el preloader
                $("#show-more-users").html("");
            }
            busy1 = false;
        });
    }
    
    // Carga 10 usuarios de inicio
    let limit = 10;
    let newLimit = 10;
    let offset1 = 0;
    let busy1 = false;
    displayUsers();
    $("#show-more-users").hide();

    // Muestra más registros cuando se hace scroll down
    $(window).scroll(function() {
        if ($(window).scrollTop() == $(document).height() - $(window).height() && !busy1) {
            busy1 = true;
            let lastRowId = parseInt($("#admin-table tr:last").attr("id"))+1;
            offset1 = limit + offset1;
            newLimit = newLimit + 10;
            displayUsers(limit, offset1, lastRowId);
        }
    });

    // Filtra por usuario, nombre o apellidos
    $("#search-user").on("input", function() {
        let char = $("#search-user").val();
        char = $("#search-user").val();
        let smc = $("#show-more-users").hide();
        if ($("#search-user").val() == "") {
            offset1 = 0;
            smc.html($(".preloader").html());
        }
        $.get("lib/admin-panel/Admin-panel.php", {char:char}, function(response) {
            $("#table-header ~ tr").remove();
            $("#table-header").after(response);
        });
    });

    // Ventana dialog para confirmar el borrado
    $("#confirm-delete-user").dialog({
        title: "Confirmación de borrado",
        autoOpen: false,
        width: 500,
        maxWidth: 500,
        resizable: false,
        draggable: false,
        modal: true,
        fluid: true,
        close: function() {
            $("#confirm-delete-user").text("");
        },
        buttons: {
            Cancelar: function() {
                $("#confirm-delete-user").dialog("close");
            },
            "Aceptar": function() {
                deleteUser(userName);
                $("#confirm-delete-user").dialog("close");
            }
        }
    });

    // Elimina al usuario de la fila seleccionada
    function deleteUser(userName) {
        $.get("lib/admin-panel/Admin-panel.php", 
        {
            delete: true, 
            userId: userName, 
            limit: newLimit, 
        }, function(response) {
            $("#table-header ~ tr").remove();
            $("#table-header").after(response);
        });
    }

    // Abre la ventana de confirmación para el borrado
    let userName = "";
    $(document).on("click", "button[data-user-delete]", function() {
        let rowId = $(this).closest("tr").attr("id");
        userName = $("tr#"+rowId+" td[data-user]").text();
        $("#confirm-delete-user").text("¿Seguro que desea eliminar al usuario con ID, "+userName+"?");
        $("#confirm-delete-user").dialog("open");
    });
    
    // Ventana modal de administración, para añadir/editar usuarios
    $("#user-admin-modal").dialog({
        title: "Panel de administración",
        autoOpen: false,
        width: 1200,
        maxWidth: 1200,
        height: 200,
        resizable: false,
        draggable: false,
        modal: true,
        fluid: true,
        close: function() { // Al cerrar la ventana, se limpian los campos y se refresca la tabla
            $("#user-admin-form")[0].reset();
            $("#admin-response").text("");
            $.get("lib/admin-panel/Admin-panel.php", {limit: newLimit}, function(response) {
                $("#table-header ~ tr").remove();
                $("#table-header").after(response);
            });
        }
    });

    // Abre la ventana modal para realizar la modificación
    let userId = pass = name = surname = email = "";
    $(document).on("click", "button[data-user-edit]", function() {
        let rowId = $(this).closest("tr").attr("id"); // Se obtiene el id de la fila y el valor de los campos de dicha fila
        userId = $("tr#"+rowId+" td[data-user]").text();
        pass = $("tr#"+rowId+" td[data-pass]").text();
        name = $("tr#"+rowId+" td[data-name]").text();
        surname = $("tr#"+rowId+" td[data-surname]").text();
        email = $("tr#"+rowId+" td[data-email]").text();
        $("#admin-modal-submit").text("Modificar"); // Se cambia el texto del botón de envío a modificar

        // Ya que es una modificación, se dejan los campos tal como estaban
        $("#admin-user").val(userId);
        $("#admin-pass").val(pass);
        $("#admin-name").val(name);
        $("#admin-surname").val(surname);
        $("#admin-email").val(email);
        $("#user-admin-modal").dialog("open");
    });

    // Abre la ventana modal para realizar la inserción
    $("#add-user").click(function() {
        $("#admin-modal-submit").text("Insertar"); // Se cambia el texto del botón de envío a insertar
        $("#user-admin-modal").dialog("open");
    });

    // Envía al servidor una modificación o adición según la opción pulsada
    $("#admin-modal-submit").click(function() {
        let userEdit = $("#admin-user").val();
        let passEdit = $("#admin-pass").val();
        let nameEdit = $("#admin-name").val();
        let surnameEdit = $("#admin-surname").val();
        let emailEdit = $("#admin-email").val();
        let type = "";
        if ($("#admin-modal-submit").text() === "Modificar") { // Si el botón es modificar envia datos a edit
            type = "edit";
        } else { // Si no, envia a add
            type = "add";
        }
        $.post("lib/admin-panel/Admin-panel.php",
        {
            type: type,
            userId: userId,
            userEdit: userEdit,
            passEdit: passEdit,
            nameEdit: nameEdit,
            surnameEdit: surnameEdit,
            emailEdit: emailEdit
        }, function(response) {
            $("#admin-response").text(response);
        });
        return false;
    });

    //------------------- Calendario -------------------//

    // Dialog que muestra los estrenos de la fecha seleccionada
    $("#linked-movies-dialog").dialog({
        title: "Alarmas para esta fecha:",
        autoOpen: false,
        width: 300,
        maxWidth: 300,
        resizable: false,
        fluid: true,
        dialogClass: "linked-dialog",
        close: function() {
            $("#linked-movies-dialog").text("");
        }
    });

    // Opciones del calendario para ponerlo en español
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '< Ant',
        nextText: 'Sig >',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: '',
        onSelect: function(e) { // Al seleccionar un día consulta en la bbdd, y trae un array con las películas que se estrenan ese día
            let dateSelected = $.datepicker.formatDate("yy-mm-dd",($("#date").datepicker("getDate")));
            $("#linked-movies-dialog").dialog("close");
            $.post("lib/main-datepicker/Main-datepicker.php", {type: "getMovies", date: dateSelected}, function(response) {
                response.forEach(function(movies) {
                    $("#linked-movies-dialog").append("<p>"+movies["title"]+"</p>"); // Añade los estrenos al dialog
                    $("#linked-movies-dialog").dialog("open");
                });
            }, "json");
            getEachDate();
        }
    }
    $.datepicker.setDefaults($.datepicker.regional['es']); // Se establecen las opciones
    $("#date").datepicker(); // Se asocia el calendario al contenedor

    // Obtiene todas las fechas del mes seleccionado y consulta en la bbdd para traerse las que coincidan
    function getEachDate() {
        let arrayDatesUI = [];
        $("#date td:has(a)").datepicker().each(function() {
            // Crea un string tipo fecha, con los atributos de cada celda
            let dt = new Date(($(this).attr("data-year")+"-"+(parseInt($(this).attr("data-month"))+1)+"-"+$(this).text()));
            let dateFormat = $.datepicker.formatDate("yy-mm-dd",dt); // Transforma a tipo date el string anterior
            arrayDatesUI.push(dateFormat); // Lo almacena en un array
        });
        $.post("lib/main-datepicker/Main-datepicker.php", {type: "getDates", datesUI:arrayDatesUI}, function(response) {
            response.forEach(function(dateResponse) {
                $("#date td:has(a)").datepicker().each(function() {
                    let dt = new Date(($(this).attr("data-year")+"-"+(parseInt($(this).attr("data-month"))+1)+"-"+$(this).text()));
                    let dateFormat = $.datepicker.formatDate("yy-mm-dd",dt);
                    if (dateResponse["dateTrue"] === dateFormat) { // Si las fechas coinciden, pinta la celda
                        $(this).children("a").css({
                            "background-color": "#ff6000",
                            "color": "#fff",
                            "border": "1px solid #fff"
                        });
                    }
                });
            });
        }, "json");
    }

    // Se inicializa al cargar la página
    getEachDate();

    // Cuando se clickea en el mes siguiente
    $(document).on('click', '.ui-datepicker-next', function () {
        $("#linked-movies-dialog").dialog("close");
        getEachDate();
    });

    // Cuando se clickea en el mes anterior
    $(document).on('click', '.ui-datepicker-prev', function () {
        $("#linked-movies-dialog").dialog("close");
        getEachDate();
    });

    //------------------- Películas -------------------//

    // Llama a php y hace una actualización general cada 5 días
    function generalUpdate() {
        $.get("lib/main-movies/Main-movies.php", {type: "generalUpdate"}, function(response) {
            console.log(response);
        });
    }

    $("#show-more-movies").html($(".preloader").html()); // Inserta el preloader

    // Función para mostrar las películas
    function displayMovies(limit, offset) {
        $("#show-more-movies").show();
        $.get("lib/main-movies/Show-movies.php", 
        {
            limit: limit,
            offset: offset
        }, function(response) {
            $("#movies-section").append(response);
            if (response == "") { // Si no devuelve más registros, limpia el preloader
                $("#show-more-movies").html("");
            }
            busy2 = false;
        });
    }

    generalUpdate(); // Ejecuta la actualización

    let offset2 = 0;
    let busy2 = false;
    displayMovies(); // Muestra las 10 primeras películas
    $("#show-more-movies").hide();

    // Muestra más registros cuando se hace scroll down
    $(window).scroll(function() {
        if ($(window).scrollTop() == $(document).height() - $(window).height() && !busy2) {
            busy2 = true;
            offset2 = limit + offset1;
            displayMovies(limit, offset2);
        }
    });

    // Dialog para el contenido al completo de la película seleccionada
    $("#info-movie-dialog").dialog({
        autoOpen: false,
        width: 900,
        maxWidth: 900,
        resizable: false,
        draggable: false,
        modal: true,
        dialogClass: 'info-movie no-titlebar',
        fluid: true,
        close: function() {
            getEachDate();
            $("#date").datepicker("refresh");
        }
    });

    // Al hacer click en una carta de película general, actualiza o inserta los datos y muestra el dialog con todo el contenido
    $(document).on("click", ".movie-container", function() {
        let movieID = $(this).attr("data-id");
        let infoID = $(this).attr("data-info-id");

        $(".preloader-movie[data-id='"+movieID+"']").show();
        $.get("lib/main-movies/Main-movies.php", {
            type: "getMoviesInfo",
            movieID: movieID,
            infoID: infoID
        }, function(response) {
            $(".preloader-movie[data-id='"+movieID+"']").hide();
            $("#info-movie-dialog").html(response);
            $("#info-movie-dialog").dialog("open");

            // Consulta la vinculación del usuario con la película seleccionada
            $.post("lib/main-datepicker/Main-datepicker.php", {type: "checkUserMovie", movieID: movieID}, function(response) {
                if (response == "off") { // Si existe cambia el icono
                    $("i.alarm").text("notifications_off");
                }
            });
        });
    });
    
    // Cierra el dialog de información de la película
    $(document).on("click", "span.info-close", function() {
        $("#info-movie-dialog").dialog("close");
    });

    // Vincula o desvincula al usuario con la película
    $(document).on("click", "i.alarm", function() {
        let movieID = $(this).attr("data-id");
        $.post("lib/main-datepicker/Main-datepicker.php", {type: "linkUserMovie", movieID: movieID}, function(response) {
            if (response == "on") {
                $("i.alarm").text("notifications");
            } else {
                $("i.alarm").text("notifications_off");
            }
        });
    });

    //------------------- Barra de navegación -------------------//
    $(".button-collapse").sideNav(); // Abre el menú desplegable para móviles
});