

// Obtener el campo de entrada del RUT
var rutInput = document.getElementById('rut');

// Función para aplicar la máscara al campo de entrada del RUT
function aplicarMascaraRut() {
    // Obtener el valor actual del campo de entrada del RUT
    var rut = rutInput.value;

    // Remover caracteres no válidos del RUT
    rut = rut.replace(/[^0-9kK]/g, '');

    // Aplicar la máscara al RUT
    var rutFormateado = '';
    var rutSinDV = rut.slice(0, -1);
    var dv = rut.slice(-1);

    // Aplicar puntos y guion al RUT
    for (var i = rutSinDV.length - 1, j = 0; i >= 0; i--, j++) {
        rutFormateado = rutSinDV.charAt(i) + rutFormateado;
        if (j === 2 && i !== 0) {
            rutFormateado = '.' + rutFormateado;
            j = -1;
        }
    }

    // Agregar el dígito verificador al RUT formateado
    rutFormateado += '-' + dv;

    // Actualizar el valor del campo de entrada con el RUT formateado
    rutInput.value = rutFormateado;
}

// Agregar un evento de escucha para aplicar la máscara al RUT mientras se escribe
rutInput.addEventListener('input', aplicarMascaraRut);
