// Establecer la fecha actual automáticamente y hacer que no se pueda cambiar
window.onload = function () {
    const fechaInput = document.getElementById('fecha');
    const today = new Date().toISOString().split('T')[0];
    fechaInput.value = today;
    fechaInput.setAttribute('readonly', true); // Hacer el campo de fecha de solo lectura
    actualizarTotal();  // Asegurarse de calcular el total y cambio al cargar la página
};

// Función para buscar cliente
function buscarCliente() {
    const documento = document.getElementById('documento').value;
    console.log('Documento ingresado:', documento);

    if (documento) {
        fetch(`../server/buscar_cliente.php?documento=${documento}`)
            .then(response => response.text())  // Cambiar a .text() para depuración
            .then(data => {
                console.log('Respuesta del servidor:', data);  // Ver qué está devolviendo el servidor
                try {
                    const jsonData = JSON.parse(data);  // Convertir a JSON manualmente
                    if (jsonData.nombre) {
                        document.getElementById('cliente').value = jsonData.nombre;
                    } else {
                        alert('Cliente no encontrado.');
                    }
                } catch (e) {
                    console.error('Error al parsear JSON:', e, data);
                    alert('Error al buscar cliente. Ver consola para más detalles.');
                }
            })
            .catch(error => console.error('Error al buscar cliente:', error));
    } else {
        alert('Por favor, ingrese un número de documento.');
    }
}

// Función para calcular el cambio automáticamente y cambiar el color si es negativo
function calcularCambio() {
    const total = parseFloat(document.getElementById('total').value) || 0;
    const pagado = parseFloat(document.getElementById('totalPagado').value) || 0;
    const cambio = pagado - total;
    const cambioElement = document.getElementById('cambio');

    cambioElement.value = cambio.toFixed(0);
    if (cambio < 0) {
        cambioElement.classList.add('text-danger');
    } else {
        cambioElement.classList.remove('text-danger');
    }
}

// Función para actualizar el total automáticamente
function actualizarTotal() {
    let total = 0;
    const subtotales = document.querySelectorAll('.subtotal');
    subtotales.forEach(subtotal => {
        total += parseFloat(subtotal.innerText.replace(/[^0-9.-]+/g, "")) || 0;
    });
    document.getElementById('total').value = total;
    document.getElementById('totalAPagar').innerText = `$${total.toFixed(0)}`;
    calcularCambio();  // Asegura que el cambio se actualice al mismo tiempo
}

// Función para eliminar producto del carrito (simulado)
function eliminarProducto(filaId) {
    document.getElementById(filaId).remove();
    actualizarTotal();
}

$(document).ready(function() {
    // Prevenir la recarga de la página al presionar Enter en el formulario
    $('form').on('submit', function(event) {
        event.preventDefault();
    });

    // Función para agregar producto
    $('#agregarProductoBtn').click(function(e) {
        e.preventDefault();
        agregarProducto();
    });

    // Llamar calcularCambio cada vez que se ingrese un valor en Efectivo Recibido
    $('#totalPagado').on('input', function() {
        calcularCambio();
    });

    // Función para actualizar el total
    function actualizarTotal() {
        let total = 0;
        const subtotales = document.querySelectorAll('.subtotal');
        subtotales.forEach(subtotal => {
            total += parseFloat(subtotal.innerText.replace(/[^0-9.-]+/g, "")) || 0;
        });
        document.getElementById('total').value = total;
        document.getElementById('totalAPagar').innerText = `$${total.toFixed(0)}`;
        calcularCambio();  // Asegura que el cambio se actualice al mismo tiempo
    }

    // Función para calcular el cambio
    function calcularCambio() {
        const total = parseFloat(document.getElementById('total').value) || 0;
        const pagado = parseFloat(document.getElementById('totalPagado').value) || 0;
        const cambio = pagado - total;
        const cambioElement = document.getElementById('cambio');
    
        cambioElement.value = cambio.toFixed(0);
        if (cambio < 0) {
            cambioElement.classList.add('text-danger');
        } else {
            cambioElement.classList.remove('text-danger');
        }
    }

    // Función para eliminar un producto
    function eliminarProducto(filaId) {
        $('#' + filaId).remove();
        actualizarTotal();
    }
});

// Función para agregar producto
function agregarProducto() {
    const skuProducto = $('input[name="codigo_barras"]').val();

    if (skuProducto) {
        $.ajax({
            url: '../server/lista-productos.php',
            method: 'GET',
            data: { sku_producto: skuProducto },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    const producto = data[0];
                    const fila = `
                        <tr id="fila${producto.id}">
                            <td>${producto.sku_producto}</td>
                            <td>${producto.nombre_producto}</td>
                            <td>
                                <input type="number" value="1" class="form-control cantidad" data-precio="${producto.precio_producto}" onchange="actualizarSubtotal(this, ${producto.id})" />
                            </td>
                            <td>${producto.precio_producto}</td>
                            <td class="subtotal" id="subtotal${producto.id}">${producto.precio_producto}</td>
                            <td>
                                <button class="btn btn-success" onclick="actualizarSubtotal($('#fila${producto.id} .cantidad'), ${producto.id})">Actualizar</button>
                            </td>
                            <td>
                                <button class="btn btn-danger" onclick="eliminarProducto('fila${producto.id}')">X</button>
                            </td>
                        </tr>
                    `;
                    $('#productosCarrito').append(fila);

                    // Limpiar el campo de entrada del código de barras
                    $('input[name="codigo_barras"]').val('');

                    // Actualizar el total y el cambio
                    actualizarTotal();
                } else {
                    alert('Producto no encontrado.');
                }
            },
            error: function(error) {
                console.error('Error al cargar productos:', error);
            }
        });
    } else {
        alert('Por favor, ingrese un código de barras.');
    }
}

// Función para actualizar el subtotal cuando cambia la cantidad
function actualizarSubtotal(element, idProducto) {
    const cantidad = parseInt($(element).val());
    const precio = parseFloat($(element).data('precio'));
    const subtotal = cantidad * precio;
    $(`#subtotal${idProducto}`).text(subtotal.toFixed(0));
    actualizarTotal();  // Asegura que el total y el cambio se actualicen
}