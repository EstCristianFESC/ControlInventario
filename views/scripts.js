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

// Función para eliminar producto del carrito
function eliminarProducto(filaId) {
    document.getElementById(filaId).remove();
    actualizarTotal();
}

// Función para agregar productos al carrito de la factura
function agregarProducto() {
    const skuProducto = $('input[name="codigo_barras"]').val();

    if (skuProducto) {
        $.ajax({
            url: '../server/lista-productos.php',
            method: 'GET',
            data: { sku_producto: skuProducto },
            dataType: 'json', // Cambiar a 'json' si el servidor devuelve un JSON
            success: function(producto) {
                if (producto.length > 0) {
                    const p = producto[0]; // Asumir que solo hay un producto por cada SKU
                    const fila = `
                        <tr id="fila${p.id}">
                            <td data-codigo-barras="${p.sku_producto}">${p.sku_producto}</td>
                            <td>${p.nombre_producto}</td>
                            <td>
                                <input type="number" value="1" class="form-control cantidad" name="cantidad" data-precio="${p.precio_producto}" onchange="actualizarSubtotal(this, ${p.id})" />
                            </td>
                            <td data-precio="${p.precio_producto}">${p.precio_producto}</td>
                            <td class="subtotal" id="subtotal${p.id}">${p.precio_producto}</td>
                            <td>
                                <button class="btn btn-success" onclick="actualizarSubtotal($('#fila${p.id} .cantidad'), ${p.id})">Actualizar</button>
                            </td>
                            <td>
                                <button class="btn btn-danger" onclick="eliminarProducto('fila${p.id}')">X</button>
                            </td>
                        </tr>
                    `;
                    $('#productosCarrito').append(fila);
                } else {
                    alert('Producto no encontrado.');
                }

                // Limpiar el campo de entrada del código de barras
                $('input[name="codigo_barras"]').val('');

                // Actualizar el total y el cambio
                actualizarTotal();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error al cargar productos:', textStatus, errorThrown);
                alert('Error al cargar productos. Ver consola para más detalles.');
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

$(document).ready(function() {
    // Prevenir la recarga de la página al presionar Enter en el formulario
    $('form').on('submit', function(event) {
        event.preventDefault();
    });

    // Llamar agregarProducto cuando se presiona el botón
    $('#agregarProductoBtn').off('click').on('click', function(e) {
        e.preventDefault();
        agregarProducto();
    });

    // Llamar calcularCambio cada vez que se ingrese un valor en Efectivo Recibido
    $('#totalPagado').on('input', function() {
        calcularCambio();
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const guardarVentaForm = document.getElementById('ventaForm');
    if (guardarVentaForm) {
        let isSubmitting = false; // Bandera para evitar doble envío

        guardarVentaForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Evitar doble envío
            if (isSubmitting) return;
            isSubmitting = true;

            const productos = obtenerProductosDelCarrito();
            const fecha = document.getElementById('fecha').value;
            const documento = document.getElementById('documento').value;
            const cliente = document.getElementById('cliente').value;
            const totalPagado = parseFloat(document.getElementById('totalPagado').value);
            const total = parseFloat(document.getElementById('total').value);
            const cambio = parseFloat(document.getElementById('cambio').value);

            // Validar valores antes de enviarlos
            if (isNaN(totalPagado) || isNaN(total) || isNaN(cambio)) {
                alert("Error: Por favor, ingresa valores válidos para los totales y el cambio.");
                isSubmitting = false;
                return;
            }

            // Preparar el objeto JSON para enviar al servidor
            const ventaData = {
                fecha: fecha,
                documento: documento,
                cliente: cliente,
                totalPagado: totalPagado,
                total: total,
                cambio: cambio,
                productos: productos // Obtenemos los productos del carrito
            };

            // Enviar la solicitud al servidor
            fetch('../server/guardar_ventas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(ventaData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor completa:', data);
                if (data.success) {
                    alert('Venta guardada exitosamente.');
                    // Redirigir a la lista de ventas
                    window.location.href = '../views/lista-ventas.html';
                } else {
                    alert(`Error: ${data.message}`);
                }
                isSubmitting = false; // Restablecer la bandera
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                alert('Error en la conexión con el servidor.');
                isSubmitting = false; // Restablecer la bandera
            });
        });
    }
});

function obtenerProductosDelCarrito() {
    const productos = [];
    document.querySelectorAll('#productosCarrito tr').forEach(tr => {
        const codigoBarrasElement = tr.querySelector('td[data-codigo-barras]');
        const cantidadElement = tr.querySelector('input[name="cantidad"]');
        const precioElement = tr.querySelector('td[data-precio]');

        if (codigoBarrasElement && cantidadElement && precioElement) {
            const codigoBarras = codigoBarrasElement.dataset.codigoBarras;
            const cantidad = parseInt(cantidadElement.value);
            const precio = parseFloat(precioElement.dataset.precio);

            if (!isNaN(codigoBarras) && !isNaN(cantidad) && !isNaN(precio)) {
                productos.push({ producto_id: codigoBarras, cantidad, precio });
            } else {
                console.error('Error: Valores inválidos en el carrito', { codigoBarras, cantidad, precio });
            }
        }
    });
    return productos;
}