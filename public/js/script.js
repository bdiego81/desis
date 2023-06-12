$(document).ready(function () {
    cargarRegiones();
    cargarCandidatos();
    addListenerRegion();
    addListenerSubmitForm();
});

/**
 * Evento listener que recarga las comunas cuando se selecciona una region
 */
function addListenerRegion() {
    $('#region').on('change', function () {
        var regionSeleccionada = $(this).val();
        cargarComunas(regionSeleccionada);
    });
}

/**
 * Carga el listado de regiones en el select region (mediante llamado async ajax)
 */
function cargarRegiones() {
    $.ajax({
        url: '../database/controlador_db.php',
        type: 'GET',
        data: { action: 'getRegiones' },
        dataType: 'json',
        success: function (data) {
            var regionSelect = $('#region');

            // Limpiar opciones anteriores
            regionSelect.empty();

            // Agregar la opcion inicial de SELECCIONE
            var option = $('<option></option>').attr('value', 0).text("SELECCIONE");
            regionSelect.append(option);

            // Agregar regiones
            $.each(data, function (index, region) {
                var option = $('<option></option>').attr('value', region.id).text(region.nombre);
                regionSelect.append(option);
            });

            // Cargar las comunas correspondientes a region seleccionada
            cargarComunas(regionSelect.val());
        },
        error: function () {
            console.log('Error al cargar las regiones');
        }
    });
}

/**
 * Carga el listado de comunas en el select comunas (mediante llamado async ajax)
 */
function cargarComunas(region) {
    var comunaSelect = $('#comuna');

    // Limpiar opciones anteriores
    comunaSelect.empty();

    // Agregar opción inicial "Seleccione"
    var option = $('<option></option>').attr('value', 0).text('SELECCIONE');
    comunaSelect.append(option);

    if (region !== 0) {
        // Realizar una solicitud AJAX para obtener las comunas de la región seleccionada
        $.ajax({
            url: '../database/controlador_db.php',
            type: 'get',
            data: {
                action: 'getComunas',
                region: region
            },
            dataType: 'json',
            success: function (data) {
                // Agregar las comunas de la region seleccionada
                $.each(data, function (index, comuna) {
                    option = $('<option></option>').attr('value', comuna.id).text(comuna.nombre);
                    comunaSelect.append(option);
                });
            },
            error: function () {
                console.log('Error al cargar las comunas');
            }
        });
    }
}

/**
 * Carga el listado de candidatos en el select candidatos (mediante llamado async ajax)
 */
function cargarCandidatos() {
    $.ajax({
        url: '../database/controlador_db.php',
        type: 'GET',
        data: { action: 'getCandidatos' },
        success: function (data) {
            var candidatoSelect = $('#candidato');

            // Limpiar opciones anteriores
            candidatoSelect.empty();

            // Agregar la opcion inicial SELECCIONE
            var option = $('<option></option>').attr('value', 0).text("SELECCIONE");
            candidatoSelect.append(option);

            // Agregar los candidatos
            $.each(data, function (index, candidato) {
                var option = $('<option></option>').attr('value', candidato.id).text(candidato.nombre);
                candidatoSelect.append(option);
            });
        },
        error: function () {
            console.log('Error al cargar las regiones');
        }
    });
}

/**
 * Metodo accionado cuando se presiona el boton VOTAR
 * Llama a procesar_voto.php (donde se realizan las validaciones e inserta en bd posteriomente)
 */
function addListenerSubmitForm() {

    $("#formulario").submit(function (event) {

        // Evita el envío del formulario normal
        event.preventDefault();

        //Obtiene las opciones marcadas de Como se entero de nosotros
        var checkboxes = document.querySelectorAll("input[name='medio[]']:checked");
        var valoresSeleccionados = [];
        checkboxes.forEach(function (checkbox) {
            valoresSeleccionados.push(checkbox.value);
        });


        // Enviar los datos por AJAX para su procesamiento
        $.ajax({
            url: "../logic/procesar_voto.php",
            type: "POST",
            data: {
                nombre: $("#nombre").val(),
                alias: $("#alias").val(),
                rut: $("#rut").val(),
                email: $("#email").val(),
                region: $("#region").val(),
                comuna: $("#comuna").val(),
                candidato: $("#candidato").val(),
                medio: valoresSeleccionados
            },
            success: function (response) {
                //se obtiene respuesta
                obj = JSON.parse(response);

                if (obj.success) {

                    //Despliega mensaje segun respuesta de servidor
                    alert(obj.message);

                    //Si hay exito al insertar voto, se limpiar el formulario
                    limpiaForm();
                } else {
                    //Despliega mensaje segun respuesta de servidor
                    alert(obj.message);
                }

            },
            error: function (xhr, status, error) {
                // Mostrar mensaje de error
                alert("Error " + error);
            }
        });
    });
}
/**
 * Limpia el formulario
 */
function limpiaForm() {
    var formulario = document.getElementById('formulario');
    formulario.reset();
    cargarComunas();
}