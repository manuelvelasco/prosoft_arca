//var rutaAplicacion = "http://localhost/git/prosoft_arca/";
var rutaAplicacion = "http://54.196.77.109/arca/";
//var rutaAplicacion = "https://albacar.mx/";

var rutaBackend = rutaAplicacion + "backend/";
var rutaVehiculos = rutaAplicacion + "vehiculos/";
var rutaPosts = rutaAplicacion + "posts/";
var rutaConcesionarios = rutaAplicacion + "concesionarios/";


$(function() {
    registraAnalitica();

    // Modal

    cargaParametros();

    $("#mensaje_exito_agendarCita").hide();
    $("#mensaje_exito_contacto").hide();
});


$(document).on("click", "#enlace_agendarCita_agendar", function(e) {
    $(".errorModalAgendarCita").hide();
    $(".errorModalAgendarCita").html("");
    $("#mensaje_exito_agendarCita").hide();
    $("#enlace_agendarCita_agendar").hide();

    var fechaSolicitada = $("#campo_agendarCita_fechaSolicitada").val();
    var horaSolicitada = $("#campo_agendarCita_horaSolicitada").val();
    var medioSolicitado = $("#campo_agendarCita_medioSolicitado").val();
    var nombre = $("#campo_agendarCita_nombre").val();
    var apellido = $("#campo_agendarCita_apellido").val();
    var telefono = $("#campo_agendarCita_telefono").val();
    var correoElectronico = $("#campo_agendarCita_correoElectronico").val();
    var idVehiculo = $("#campo_agendarCita_idVehiculo").val();

    var ruta = window.location.pathname + window.location.search;
    var pagina = ruta.substring(ruta.lastIndexOf('/') + 1);

    $.ajax({
        url: rutaBackend + "ajax/agendaCita.php",
        type: "post",
        data: {
            fechaSolicitada: fechaSolicitada,
            horaSolicitada: horaSolicitada,
            medioSolicitado: medioSolicitado,
            nombre: nombre,
            apellido: apellido,
            telefono: telefono,
            correoElectronico: correoElectronico,
            idVehiculo: idVehiculo,
            canalIngreso: pagina
        }
    }).done(function (resultado, textStatus, jqXHR) {
        if (resultado == "<resultado>ok</resultado>") {
            $("#mensaje_exito_agendarCita").show();
            $("#enlace_agendarCita_agendar").hide();
        } else {

            $("#enlace_agendarCita_agendar").show();

            // Maneja errores

            $(resultado).find("error").each(function (index) {
                var campo = $(this).find("campo").text();
                var mensaje = $(this).find("mensaje").text();

                $("#error_agendarCita_" + campo).append(mensaje);
                $("#error_agendarCita_" + campo).show();
            });
        }
    });
});


$(document).on("click", "#enlace_cotizarAuto_informacionVehiculo_continuar", function(e) {

    // Valida informacion del vehiculo

    $(".error").hide();
    $(".error").html("");

    var marca = $("#campo_cotizarAuto_marca").val();
    var modelo = $("#campo_cotizarAuto_modelo").val();
    var ano = $("#campo_cotizarAuto_ano").val();
    var version = $("#campo_cotizarAuto_version").val();
    var kilometraje = $("#campo_cotizarAuto_kilometraje").val();
    var color = $("#campo_cotizarAuto_color").val();

    $.ajax({
        url: rutaBackend + "ajax/procesaVentaAuto.php",
        type: "post",
        data: {
            paso: 1,
            marca: marca,
            modelo: modelo,
            ano: ano,
            version: version,
            kilometraje: kilometraje,
            color: color
        }
    }).done(function (resultado, textStatus, jqXHR) {
        if (resultado == "<resultado>ok</resultado>") {

            // Obtiene el precio de InteliMotor

            var precioSugerido = 100000.00;
            
            $("#campo_cotizarAuto_precioSugerido").val(precioSugerido);
            $("#contenedor_cotizarAuto_precioSugerido").html("$" + formatoMoneda(precioSugerido));

            $("#contenedor_cotizarAuto_informacionVehiculo").hide();
            $("#contenedor_cotizarAuto_informacionVehiculo").addClass("fade");
            $("#tab_cotizarAuto_informacionVehiculo").removeClass("active");

            $("#contenedor_cotizarAuto_informacionContacto").show();
            $("#contenedor_cotizarAuto_informacionContacto").removeClass("fade");
            $("#tab_cotizarAuto_informacionContacto").addClass("active");
        } else {

            // Maneja errores

            $(resultado).find("error").each(function (index) {
                var campo = $(this).find("campo").text();
                var mensaje = $(this).find("mensaje").text();

                $("#error_" + campo).append(mensaje);
                $("#error_" + campo).show();
            });
        }
    });
});


$(document).on("click", "#enlace_cotizarAuto_informacionContacto_regresar", function() {
    $("#contenedor_cotizarAuto_informacionVehiculo").show();
    $("#contenedor_cotizarAuto_informacionVehiculo").removeClass("fade");
    $("#tab_cotizarAuto_informacionVehiculo").addClass("active");

    $("#contenedor_cotizarAuto_informacionContacto").hide();
    $("#contenedor_cotizarAuto_informacionContacto").addClass("fade");
    $("#tab_cotizarAuto_informacionContacto").removeClass("active");
});


$(document).on("click", "#enlace_cotizarAuto_informacionContacto_continuar", function(e) {

    // Valida informacion del vehiculo y de contacto

    $(".error").hide();
    $(".error").html("");

    var marca = $("#campo_cotizarAuto_marca").val();
    var modelo = $("#campo_cotizarAuto_modelo").val();
    var ano = $("#campo_cotizarAuto_ano").val();
    var version = $("#campo_cotizarAuto_version").val();
    var kilometraje = $("#campo_cotizarAuto_kilometraje").val();
    var color = $("#campo_cotizarAuto_color").val();

    var nombre = $("#campo_cotizarAuto_nombre").val();
    var apellido = $("#campo_cotizarAuto_apellido").val();
    var correoElectronico = $("#campo_cotizarAuto_correoElectronico").val();
    var telefono = $("#campo_cotizarAuto_telefono").val();

    $.ajax({
        url: rutaBackend + "ajax/procesaVentaAuto.php",
        type: "post",
        data: {
            paso: 2,
            marca: marca,
            modelo: modelo,
            ano: ano,
            version: version,
            kilometraje: kilometraje,
            color: color,
            nombre: nombre,
            apellido: apellido,
            correoElectronico: correoElectronico,
            telefono: telefono
        }
    }).done(function (resultado, textStatus, jqXHR) {
        if (resultado == "<resultado>ok</resultado>") {
            localStorage.setItem("reiniciarCotizador","1");
            $("#contenedor_cotizarAuto_informacionContacto").hide();
            $("#contenedor_cotizarAuto_informacionContacto").addClass("fade");
            $("#tab_cotizarAuto_informacionContacto").removeClass("active");

            $("#contenedor_cotizarAuto_confirmacion").show();
            $("#contenedor_cotizarAuto_confirmacion").removeClass("fade");
            $("#tab_cotizarAuto_confirmacion").addClass("active");
        } else {

            // Maneja errores

            $(resultado).find("error").each(function (index) {
                var campo = $(this).find("campo").text();
                var mensaje = $(this).find("mensaje").text();

                $("#error_" + campo).append(mensaje);
                $("#error_" + campo).show();
            });
        }
    });
});


$(document).on("click", "#enlace_cotizarAuto_confirmacion_regresar", function() {
    $("#contenedor_cotizarAuto_informacionContacto").show();
    $("#contenedor_cotizarAuto_informacionContacto").removeClass("fade");
    $("#tab_cotizarAuto_informacionContacto").addClass("active");

    $("#contenedor_cotizarAuto_confirmacion").hide();
    $("#contenedor_cotizarAuto_confirmacion").addClass("fade");
    $("#tab_cotizarAuto_confirmacion").removeClass("active");
});


$(document).on("click", "#enlace_cotizarAuto_confirmacion_agendarCita", function() {
    $("#campo_agendarCita_nombre").val($("#campo_cotizarAuto_nombre").val());
    $("#campo_agendarCita_apellido").val($("#campo_cotizarAuto_apellido").val());
    $("#campo_agendarCita_correoElectronico").val($("#campo_cotizarAuto_correoElectronico").val());
    $("#campo_agendarCita_telefono").val($("#campo_cotizarAuto_telefono").val());

    $("#enlace_modal_agendarCita").click();
});


function cargaParametros() {
    $.ajax({
        url: rutaBackend + "ajax/cargaParametros.php",
        type: "post",
        data: { nombre: "quierovender_confirmacion_whatsapp" }
    }).done(function (resultado, textStatus, jqXHR) {
        $(resultado).find("parametro").each(function (index) {
            var valor = $(this).find("valor").text();

            $("#enlace_cotizarAuto_confirmacion_contactarPorWhatsApp").attr("href", "https://wa.me/" + valor + "?text=Me%20interesa%20vender%20mi%20auto");
        });
    });
}


function formatoMoneda(monto, decimales) {
    monto += ''; // por si pasan un numero en vez de un string
    monto = parseFloat(monto.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

    decimales = decimales || 0; // por si la variable no fue fue pasada

    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(monto) || monto === 0)
        return parseFloat(0).toFixed(decimales);

    // si es mayor o menor que cero retorno el valor formateado como numero
    monto = '' + monto.toFixed(decimales);

    var amount_parts = monto.split('.'),
            regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

    return amount_parts.join('.');
}


function obtenParametroDeUrl(nombre) {
    if (nombre = (new RegExp('[?&]' + encodeURIComponent(nombre) + '=([^&]*)')).exec(location.search)) {
        return decodeURIComponent(nombre[1]);
    }
}


$(document).on("click", ".enlace_vehiculo", function() {
    window.location = "vehiculo.html?id=" + $(this).data("id");
});


$(document).on("click", "#enlace_contactarWhatsapp_contactar",function(){
    $("#contenedor_contactarWhatsapp_mensaje").html("");

    var nombre = $("#campo_contactarWhatsapp_nombre").val();
    var url = window.location.href;

    if (nombre != "") {
        if (url.indexOf("vehiculo.html") > -1) {
            window.open("https://wa.me/528118168933?text=Hola%20mi%20nombre%20es%20" + nombre + "%20me%20interesa%20información%20del%20vehículo:%20 " + url,"_blank");
        } else {
            window.open("https://wa.me/528118168933?text=Hola%20mi%20nombre%20es%20" + nombre,"_blank");
        }

        $("#boton_contactarWhatsapp_cerrar").click();
    } else {
        $("#contenedor_contactarWhatsapp_mensaje").html("Compártenos tu nombre para darte una atención personalizada");
    }
});


/*
 * Registro de analiticas
 */


function registraAnalitica(selector, evento) {
    $.ajax({
        url: rutaBackend + "ajax/registraAnalitica.php",
        type: "post",
        data: {
            url: window.location + "",
            selector: selector,
            evento: evento
        }
    }).done(function (resultado, textStatus, jqXHR) {
    });
}


$(document).on("click", "a", function() {
    var selector = $(this).attr("id");
    var href = $(this).attr("href");
    var bs_target = $(this).attr("data-bs-target");

    if (bs_target) {
        switch (bs_target) {

            // Modales

            case "#modal_agendarCita":
                registraAnalitica("", "Link para mostrar el modal de Agendar cita.");
                break;
            case "#modal_valuarVehiculo":
                registraAnalitica("", "Link para mostrar el modal de Valuación de vehículo.");
                break;
            case "#modal_contactarWhatsapp":
                registraAnalitica("", "Ícono flotante para abrir modal para registrar información para contactar por Whatsapp.");
                break;
            case "#modal_compararVehiculos":
                registraAnalitica("", "Link para mostrar el modal de Comparación de vehículos.");
                break;
            case "#modal_calcularFinanciamiento":
                registraAnalitica("", "Link para mostrar el modal de Simulación de financiamiento.");
                break;
        }
    } else {
        switch(href) {

            // Canales de contacto

            case "mailto:mercadotecnia@albacar.mx":
                registraAnalitica("", "Botón para enviar correo a mercadotecnia@albacar.mx desde el footer.");
                break;
            case "tel:+528118168933":
                registraAnalitica("", "Botón para llamar por teléfono al 81 1816 8933 desde el footer.");
                break;
            case "https://m.facebook.com/autosalbacar/?mibextid=LQQJ4d":
                registraAnalitica("", "Link en el footer para abrir la página de Facebook de Albacar.");
                break;
            case "https://instagram.com/autosalbacar?igshid=YmMyMTA2M2Y=":
                registraAnalitica("", "Link en el footer para abrir la página de Instagram de Albacar.");
                break;
            case "https://www.tiktok.com/@albacar.mx?_t=8ae3Ue01DaY&_r=1":
                registraAnalitica("", "Link en el footer para abrir la página de Tik Tok de Albacar.");
                break;

            // Varios

            case "javascript:;":
                switch(selector) {
                    case "enlace_agendarCita_agendar":
                        registraAnalitica(selector, "Botón Agendar en modal Agendar cita para enviar el formulario.");
                        break;
                    case "enlace_contactarWhatsapp_contactar":
                        registraAnalitica(selector, "Botón para abrir Whatsapp con la información cargada del modal para registrar información para contactar por Whatsapp.");
                        break;
                    case "boton_contacto_enviar":
                        registraAnalitica(selector, "Contacto - Botón para enviar formulario de contacto.");
                        break;
                }

                break;
        }
    }
});


$(document).on("click", ".enlace_borrarFiltros", function(){
    registraAnalitica(".enlace_borrarFiltros", "Limpiar filtros de listado.");
});


$(document).on("click", ".enlace_pagina", function(){
    var numeroPagina = $(this).attr("data-pagina");
    var archivo = window.location.href.split('/').pop();

    registraAnalitica(".enlace_pagina", "Paginación en " + archivo + " para navegar a la pagina #" + numeroPagina);
});
