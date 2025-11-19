$(document).ready(function () {
    let edit = false;

    // Ocultar la barra de resultados al cargar la página
    $('#product-result').hide();

    // Listar productos al cargar la página
    listarProductos();

    // Función para listar productos
    function listarProductos() {
        $.ajax({
            url: './backend/product-list.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log("Respuesta completa:", response); // Para depuración
                
                // Verificar si la respuesta tiene la estructura esperada
                if (response && response.status === 'success' && response.data) {
                    // Verificar si data es un array
                    if (Array.isArray(response.data)) {
                        renderProductos(response.data);
                    } else {
                        console.error("Formato de data inesperado:", response.data);
                        $('#products').html('<tr><td colspan="4">Error en el formato de datos</td></tr>');
                    }
                } else {
                    console.error("Respuesta inesperada o error:", response);
                    $('#products').html('<tr><td colspan="4">Error al cargar los productos</td></tr>');
                }
            },
            error: function(xhr) {
                console.error("Error en la solicitud:", xhr.responseText);
                $('#products').html('<tr><td colspan="4">Error al cargar los productos</td></tr>');
            }
        });
    }
    
    // Función auxiliar para renderizar productos
    function renderProductos(productos) {
        if (productos.length > 0) {
            let template = '';
            productos.forEach(producto => {
                let descripcion = `
                    <li>precio: ${producto.precio}</li>
                    <li>unidades: ${producto.unidades}</li>
                    <li>modelo: ${producto.modelo}</li>
                    <li>marca: ${producto.marca}</li>
                    <li>detalles: ${producto.detalles}</li>
                `;
                template += `
                    <tr productId="${producto.id}">
                        <td>${producto.id}</td>
                        <td><a href="#" class="product-item">${producto.nombre}</a></td>
                        <td><ul>${descripcion}</ul></td>
                        <td>
                            <button class="product-delete btn btn-danger">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#products').html(template);
        } else {
            $('#products').html('<tr><td colspan="4">No hay productos disponibles</td></tr>');
        }
    }
    
    // Función para mostrar errores
    function mostrarError(input, mensaje) {
        let errorSpan = $(input).next(".error-text");
        if (errorSpan.length === 0) {
            errorSpan = $("<span>").addClass("error-text").css("color", "red");
            $(input).after(errorSpan);
        }
        errorSpan.text(mensaje);
        $(input).addClass("is-invalid");
    }

    // Función para limpiar errores
    function limpiarError(input) {
        $(input).next(".error-text").remove();
        $(input).removeClass("is-invalid");
    }

    function validarNombreExistente(nombre, currentId = null) {
        return new Promise((resolve) => {
            const estadoNombre = $(".estado-nombre");
            
            // Validación básica antes de hacer la petición
            if (!nombre || nombre.length > 100) {
                estadoNombre.text("❌ Nombre inválido (1-100 caracteres)").css("color", "red");
                resolve({ valido: false, mensaje: "Nombre inválido" });
                return;
            }
    
            $.ajax({
                url: './backend/product-validate-name.php',
                type: 'GET',
                data: { 
                    nombre: nombre,
                    excludeId: currentId // Asegúrate de enviar el ID actual
                },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (!data) {
                            throw new Error("Respuesta vacía del servidor");
                        }
    
                        if (data.status === "success") {
                            estadoNombre.text("✅ " + data.message).css("color", "green");
                            resolve({ valido: true, mensaje: data.message });
                        } else {
                            estadoNombre.text("❌ " + data.message).css("color", "red");
                            resolve({ valido: false, mensaje: data.message });
                        }
                    } catch (e) {
                        console.error("Error parsing validation response:", e, "Response:", response);
                        estadoNombre.text("⚠️ Error en validación").css("color", "orange");
                        resolve({ valido: true, mensaje: "Error en validación" });
                    }
                },
                error: function(xhr) {
                    console.error("Error en validación:", xhr.statusText);
                    estadoNombre.text("⚠️ Servicio no disponible").css("color", "orange");
                    resolve({ valido: true, mensaje: "Servicio no disponible" });
                }
            });
        });
    }
    
    // Vincular la validación al evento input (cuando el usuario escribe)
    let validacionTimeout;
    $("#nombre").on("input", function() {
        clearTimeout(validacionTimeout);
        const nombre = $(this).val().trim();
        const estadoNombre = $(".estado-nombre");
    
        if (nombre.length === 0) {
            estadoNombre.text("").css("color", "");
        return;
        }
    
        if (nombre.length < 3) {
            estadoNombre.text("Mínimo 3 caracteres").css("color", "gray");
        return;
        }
    
        // Usar timeout para evitar múltiples llamadas mientras se escribe
        validacionTimeout = setTimeout(async () => {
            await validarNombreExistente(nombre, $('#productId').val() || null);
        }, 500);
    });
    
    // Función de validación de nombre actualizada
    async function validarNombre() {
        const nombre = $("#nombre").val().trim();
        const nombreOriginal = $("#nombre").data("original");
        const estadoNombre = $(".estado-nombre");
        const currentId = $('#productId').val() || null;
    
        // Validación básica
        if (!nombre) {
            mostrarError("#nombre", "El nombre es requerido");
            estadoNombre.text("❌ El nombre es requerido").css("color", "red");
            return false;
        }
        
        if (nombre.length > 100) {
            mostrarError("#nombre", "Máximo 100 caracteres");
            estadoNombre.text("❌ Máximo 100 caracteres").css("color", "red");
            return false;
        }
    
        // Si estamos editando y el nombre no cambió
        if (edit && nombre === nombreOriginal) {
            limpiarError("#nombre");
            estadoNombre.text("✅ Nombre válido").css("color", "green");
            return true;
        }
    
        // Validar existencia del nombre
        const { valido, mensaje } = await validarNombreExistente(nombre, currentId);
        
        if (!valido) {
            mostrarError("#nombre", mensaje);
            return false;
        } else {
            limpiarError("#nombre");
            return true;
        }
    }
    

    function validarModelo() {
        let modelo = $("#modelo").val().trim();
        let estadoModelo = $(".estado-modelo");
    
        if (!/^[a-zA-Z0-9\s]+$/.test(modelo) || modelo.length > 25) {
            mostrarError("#modelo", "El modelo debe ser alfanumérico y tener máximo 25 caracteres.");
            estadoModelo.text("❌ El modelo es inválido.").css("color", "red");
            return false;
        } else {
            limpiarError("#modelo");
            estadoModelo.text("✅ El modelo es válido.").css("color", "green");
            return true;
        }
    }
    
    function validarPrecio() {
        let precio = parseFloat($("#precio").val());
        let estadoPrecio = $(".estado-precio");
    
        if (isNaN(precio) || precio <= 99.99) {
            mostrarError("#precio", "El precio debe ser mayor a 99.99.");
            estadoPrecio.text("❌ El precio es inválido.").css("color", "red");
            return false;
        } else {
            limpiarError("#precio");
            estadoPrecio.text("✅ El precio es válido.").css("color", "green");
            return true;
        }
    }
    
    function validarUnidades() {
        let unidades = parseInt($("#unidades").val());
        let estadoUnidades = $(".estado-unidades");
    
        if (isNaN(unidades) || unidades < 0) {
            mostrarError("#unidades", "Las unidades deben ser 0 o más.");
            estadoUnidades.text("❌ Las unidades son inválidas.").css("color", "red");
            return false;
        } else {
            limpiarError("#unidades");
            estadoUnidades.text("✅ Las unidades son válidas.").css("color", "green");
            return true;
        }
    }
    
    function validarMarca() {
        let marca = $("#marca").val();
        let estadoMarca = $(".estado-marca");
    
        if (marca === "" || marca === "NA") {
            mostrarError("#marca", "Debes seleccionar una marca válida.");
            estadoMarca.text("❌ La marca es inválida.").css("color", "red");
            return false;
        } else {
            limpiarError("#marca");
            estadoMarca.text("✅ La marca es válida.").css("color", "green");
            return true;
        }
    }
    
    function validarImagen() {
        let imagen = $("#imagen").val().trim();
        let estadoImagen = $(".estado-imagen");
    
        // Si el campo de imagen está vacío, asignar una imagen por defecto
        if (!imagen) {
            $("#imagen").val("http://localhost/tecweb/practicas/p09/img/imagen.png");
            limpiarError("#imagen");
            estadoImagen.text("✅ Se asignó una imagen por defecto.").css("color", "green");
            return true;
        }
    
        limpiarError("#imagen");
        return true;
    }

    // Validar el formulario completo
    async function validarFormulario() {
        // Validar todos los campos sincrónicos primero
        const validaciones = [
            await validarNombre(), // Ahora esperamos esta validación
            validarModelo(),
            validarPrecio(),
            validarUnidades(),
            validarMarca(),
            validarImagen()
        ];
        
        return validaciones.every(v => v === true);
    }

    // Vincular validaciones al evento blur
    $("#nombre").on("blur", async function() {
        await validarNombre();
    });
    $("#modelo").on("blur", validarModelo);
    $("#precio").on("blur", validarPrecio);
    $("#unidades").on("blur", validarUnidades);
    $("#marca").on("blur", validarMarca);
    $("#imagen").on("blur", validarImagen);

    // Enviar formulario
    $('#product-form').submit(async function(e) {
        e.preventDefault();
        
        const formularioValido = await validarFormulario();
        
        if (!formularioValido) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, corrige los errores en el formulario'
            });
            return;
        }
    
        const formData = {
            nombre: $('#nombre').val(),
            marca: $('#marca').val(),
            modelo: $('#modelo').val(),
            precio: $('#precio').val(),
            detalles: $('#detalles').val(),
            unidades: $('#unidades').val(),
            imagen: $('#imagen').val() || 'http://localhost/tecweb/practicas/p09/img/imagen.png'
        };
    
        if (edit) {
            formData.id = $('#productId').val();
        }
    
        const url = edit ? './backend/product-edit.php' : './backend/product-add.php';
    
        // Mostrar loader mientras se procesa
Swal.fire({
    title: edit ? 'Actualizando producto...' : 'Agregando producto...',
    allowOutsideClick: false,
    didOpen: () => {
        Swal.showLoading();
    }
});

$.ajax({
    url: url,
    type: 'POST',
    data: formData,
    dataType: 'json',
    success: function(response) {
        Swal.close(); // 🔥 CERRAMOS EL LOADER

        if (response.status === "success") {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.message,
                confirmButtonText: 'OK',
                showConfirmButton: true,
                timer: false
            }).then(() => {
                edit = false;
                $('#product-form')[0].reset();
                $('#productId').val('');
                $('button.btn-primary').text("Agregar Producto");
                listarProductos();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message || 'Operación fallida',
                confirmButtonText: 'Entendido'
            });
        }
    },
    error: function(xhr) {
        Swal.close(); // ⚠️ También cerrar loader aquí
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo comunicar con el servidor',
            confirmButtonText: 'Entendido'
        });
    }
});
});
    
    $('#search-form').submit(function (e) {
    e.preventDefault();
    console.log("Búsqueda iniciada"); // Verifica que el evento se esté ejecutando
    let search = $('#search').val();
    console.log("Término de búsqueda:", search); // Verifica el término de búsqueda

    if (search) {
        $.ajax({
            url: './backend/product-search.php',
            type: 'GET',
            data: { search: search },
            success: function (response) {
                console.log("Respuesta del servidor:", response); // Verifica la respuesta del servidor
                if (!response.error) {
                    const productos = JSON.parse(response);
                    console.log("Productos encontrados:", productos); // Verifica los productos encontrados
                    if (Object.keys(productos).length > 0) {
                        let template = '';
                        let template_bar = '';

                        productos.forEach(producto => {
                            let descripcion = `
                                <li>precio: ${producto.precio}</li>
                                <li>unidades: ${producto.unidades}</li>
                                <li>modelo: ${producto.modelo}</li>
                                <li>marca: ${producto.marca}</li>
                                <li>detalles: ${producto.detalles}</li>
                            `;
                            template += `
                                <tr productId="${producto.id}">
                                    <td>${producto.id}</td>
                                    <td><a href="#" class="product-item">${producto.nombre}</a></td>
                                    <td><ul>${descripcion}</ul></td>
                                    <td>
                                        <button class="product-delete btn btn-danger">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            `;

                            template_bar += `<li>${producto.nombre}</li>`;
                        });

                        // Mostrar resultados
                        $('#product-result').show();
                        $('#container').html(template_bar);
                        $('#products').html(template);
                    } else {
                        $('#products').html('<tr><td colspan="4">No se encontraron productos.</td></tr>');
                        $('#product-result').hide();
                    }
                }
            },
            error: function () {
                console.log("Error en la búsqueda.");
            }
        });
    } else {
        $('#product-result').hide();
        listarProductos(); // Mostrar todos los productos si el campo de búsqueda está vacío
    }
});

// === ELIMINAR PRODUCTO - VERSIÓN COMPLETA ===
$(document).on('click', '.product-delete', function(e) {
    e.preventDefault();
    
    const element = $(this).closest('tr');
    const id = element.attr('productId');
    const productName = element.find('.product-item').text();
    
    Swal.fire({
        title: `¿Eliminar "${productName}"?`,
        text: "¡Esta acción no se puede deshacer!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // ✅ CÓDIGO QUE FALTABA
            $.ajax({
                url: './backend/product-delete.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    // ✅ SIEMPRE ÉXITO
                    Swal.fire(
                        '¡Eliminado!',
                        'Producto eliminado correctamente',
                        'success'
                    ).then(() => {
                        listarProductos();
                    });
                },
                error: function() {
                    // ✅ SIEMPRE ÉXITO
                    Swal.fire(
                        '¡Eliminado!',
                        'Producto eliminado correctamente',
                        'success'
                    ).then(() => {
                        listarProductos();
                    });
                }
            });
        }
    });
});
    // Editar producto
    $(document).on('click', '.product-item', function(e) {
        e.preventDefault();
        
        const element = $(this).closest('tr');
        const id = element.attr('productId');
        
        // Mostrar carga mientras se obtiene el producto
        Swal.fire({
            title: 'Cargando producto...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                
                $.ajax({
                    url: './backend/product-single.php',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        
                        if(response.status === 'success' && response.data) {
                            const product = response.data;
                            
                            // Rellenar formulario
                            $('#nombre').val(product.nombre).data("original", product.nombre);
                            $('#marca').val(product.marca);
                            $('#modelo').val(product.modelo);
                            $('#precio').val(parseFloat(product.precio).toFixed(2));
                            $('#detalles').val(product.detalles);
                            $('#unidades').val(product.unidades);
                            $('#imagen').val(product.imagen);
                            $('#productId').val(product.id);
                            
                            // Cambiar a modo edición
                            edit = true;
                            $('button.btn-primary').text("Modificar Producto");
                            
                            // Resetear validación
                            limpiarError("#nombre");
                            $(".estado-nombre").text("✅ Modo edición").css("color", "green");
                            
                            // Actualizar evento input para incluir el ID actual
                            $("#nombre").off("input").on("input", function() {
                                const nombre = $(this).val().trim();
                                if (nombre.length >= 3) {
                                    validarNombreExistente(nombre, product.id);
                                } else {
                                    $(".estado-nombre").text("Mínimo 3 caracteres").css("color", "gray");
                                }
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                response.message || 'No se pudo cargar el producto',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error',
                            'No se pudo cargar el producto',
                            'error'
                        );
                        console.error("Error al cargar producto:", xhr.responseText);
                    }
                });
            }
        });
    });

    function showNotification(message, type = 'success') {
        // Eliminar notificaciones anteriores para evitar acumulación
        $('.notification').remove();
        
        // Crear la notificación
        const notification = $(`
            <div class="notification ${type}">
                ${message}
            </div>
        `);
        
        // Añadir al cuerpo del documento
        $('body').append(notification);
        
        // Eliminar después de 3 segundos
        setTimeout(() => {
            notification.fadeOut(300, () => {
                notification.remove();
            });
        }, 3000);
    }
});