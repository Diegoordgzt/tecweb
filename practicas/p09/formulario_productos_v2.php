<?php
    /** Conexión a la base de datos */
    @$link = new mysqli('localhost', 'root', 'diegord17', 'marketzone');

    /** Comprobar la conexión */
    if ($link->connect_errno) {
        die('<p>Falló la conexión: ' . htmlspecialchars($link->connect_error) . '</p>');
    }

    /** Inicializar variables */
    $id = $nombre = $marca = $modelo = $precio = $unidades = $detalles = $imagen = '';

    /** Verificar si se recibió un ID de producto */
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();
            $nombre = htmlspecialchars($producto['nombre']);
            $marca = htmlspecialchars($producto['marca']);
            $modelo = htmlspecialchars($producto['modelo']);
            $precio = htmlspecialchars($producto['precio']);
            $unidades = htmlspecialchars($producto['unidades']);
            $detalles = htmlspecialchars($producto['detalles']);
            $imagen = htmlspecialchars($producto['imagen']);
        } else {
            die('<p>Producto no encontrado...</p>');
        }
        $stmt->close();
    } else {
        die('<p>No se recibió un ID de producto...</p>');
    }

    /** Procesar formulario de actualización */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $precio = $_POST['precio'];
        $unidades = $_POST['unidades'];
        $detalles = $_POST['detalles'];

        if (!empty($_FILES['imagen']['name'])) {
            $imagen = 'imagenes/' . basename($_FILES['imagen']['name']);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
        }

        $sql = "UPDATE productos SET nombre=?, marca=?, modelo=?, precio=?, unidades=?, detalles=?, imagen=? WHERE id=?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ssssissi", $nombre, $marca, $modelo, $precio, $unidades, $detalles, $imagen, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Producto actualizado correctamente.'); window.location.href='get_productos_vigentes_v2.php?tope=10';</script>";
        } else {
            echo "<p>Error al actualizar el producto.</p>";
        }

        $stmt->close();
    }
    $link->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <style>
body {
    background-image: url("https://static.vecteezy.com/system/resources/previews/003/710/788/non_2x/abstract-web-background-many-hexagons-on-a-dark-gray-background-vector.jpg");
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #E0E0E0;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

h2 {
    text-align: center;
    margin-top: 30px;
    color: #ffffff;
    text-shadow: 2px 2px 10px #000;
}

form {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: rgba(0, 0, 0, 0.8);
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(223, 4, 4, 0.5); /* sombra roja */
    backdrop-filter: blur(5px);
}

fieldset {
    border: none;
    margin-bottom: 20px;
}

legend {
    font-weight: bold;
    font-size: 1.5rem;
    color: #ffffff;
    text-shadow: 1px 1px 5px #000;
}

label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    color: #ADD8E6;
}

input[type="text"],
input[type="number"],
textarea,
select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 10px;
    border: 2px solid #f41f1f; /* borde rojo */
    border-radius: 8px;
    background-color: #1A1A1A;
    color: #E0E0E0;
    font-size: 1rem;
    outline: none;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.7);
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus,
select:focus {
    border-color: #f41f1f;
    box-shadow: 0 0 10px #f41f1f, inset 0 0 10px rgba(0, 0, 0, 0.7);
}

input[type="submit"],
input[type="reset"] {
    padding: 10px 20px;
    background-color: #5b0202; /* rojo */
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    margin-right: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(255, 0, 0, 0.4); /* sombra roja */
}

input[type="submit"]:hover,
input[type="reset"]:hover {
    background-color: #f41f1f;
    box-shadow: 0 5px 20px rgba(240, 25, 25, 0.6);
    transform: translateY(-3px);
}

input[type="submit"]:active,
input[type="reset"]:active {
    transform: translateY(1px);
    box-shadow: 0 3px 10px rgba(196, 7, 7, 0.4);
}

    </style>
</head>
<body>
    <h2>Modificar Producto</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?php echo $nombre; ?>" required>

        <label>Marca</label>
        <input type="text" name="marca" value="<?php echo $marca; ?>" required>

        <label>Modelo</label>
        <input type="text" name="modelo" value="<?php echo $modelo; ?>" required>

        <label>Precio</label>
        <input type="number" name="precio" step="0.01" value="<?php echo $precio; ?>" required>

        <label>Unidades</label>
        <input type="number" name="unidades" value="<?php echo $unidades; ?>" required>

        <label>Detalles</label>
        <textarea name="detalles" required><?php echo $detalles; ?></textarea>

        <label>Imagen Actual</label><br>
        <img src="<?php echo $imagen; ?>" alt="Imagen del producto" width="100"><br>
        <label>Nueva Imagen (opcional)</label>
        <input type="file" name="imagen">

        <input type="submit" value="Guardar Cambios">
        <a href="get_productos_vigentes_v2.php?tope=30" class="btn-secondary">Cancelar</a>
    </form>
</body>
</html>