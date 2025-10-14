<?php
$servername = "localhost";
$username = "root";
$password = "diegord17";
$database = "marketzone";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$producto = [];
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
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

h1, h2 {
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
    box-shadow: 0 0 15px rgba(223, 4, 4, 0.5); 
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
    border: 2px solid #f41f1f; 
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
input[type="reset"],
button[type="reset"] {
    padding: 10px 20px;
    background-color: #5b0202; 
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    margin-right: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(255, 0, 0, 0.4);
}

input[type="submit"]:hover,
input[type="reset"]:hover,
button[type="reset"]:hover {
    background-color: #f41f1f;
    box-shadow: 0 5px 20px rgba(240, 25, 25, 0.6);
    transform: translateY(-3px);
}

input[type="submit"]:active,
input[type="reset"]:active,
button[type="reset"]:active {
    transform: translateY(1px);
    box-shadow: 0 3px 10px rgba(196, 7, 7, 0.4);
}

.imagen-preview {
    text-align: center;
    margin-top: 20px;
}

.imagen-preview img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(223, 4, 4, 0.5);
}

    </style>
</head>
<body>
    <h2>Editar Producto</h2>
    <form action="update_producto.php" method="post">
        <input type="hidden" name="id" value="<?php echo $producto['id'] ?? ''; ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo $producto['nombre'] ?? ''; ?>" required>

        <label for="marca">Marca:</label>
<select id="marca" name="marca" required>
    <option value="">Seleccione una marca</option>
    <option value="Samsung" <?php if (($producto['marca'] ?? '') == 'Samsung') echo 'selected'; ?>>Samsung</option>
    <option value="Apple" <?php if (($producto['marca'] ?? '') == 'Apple') echo 'selected'; ?>>Apple</option>
    <option value="Sony" <?php if (($producto['marca'] ?? '') == 'Sony') echo 'selected'; ?>>Sony</option>
    <option value="LG" <?php if (($producto['marca'] ?? '') == 'LG') echo 'selected'; ?>>LG</option>
    <option value="Lenovo" <?php if (($producto['marca'] ?? '') == 'Lenovo') echo 'selected'; ?>>Lenovo</option>
    <option value="HP" <?php if (($producto['marca'] ?? '') == 'HP') echo 'selected'; ?>>HP</option>
</select>

        <label for="modelo">Modelo:</label>
        <input type="text" id="modelo" name="modelo" value="<?php echo $producto['modelo'] ?? ''; ?>" required>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" step="0.01" value="<?php echo $producto['precio'] ?? ''; ?>" required>

        <label for="detalles">Detalles:</label>
        <textarea id="detalles" name="detalles" rows="4"><?php echo $producto['detalles'] ?? ''; ?></textarea>

        <label for="unidades">Unidades:</label>
        <input type="number" id="unidades" name="unidades" value="<?php echo $producto['unidades'] ?? ''; ?>" required>

        <label for="imagen">Imagen (URL):</label>
        <input type="text" id="imagen" name="imagen" value="<?php echo $producto['imagen'] ?? ''; ?>">

        <?php if (!empty($producto['imagen'])): ?>
        <div class="imagen-preview">
            <img src="<?php echo $producto['imagen']; ?>" alt="Imagen del producto">
        </div>
        <?php endif; ?>

        <input type="submit" value="Actualizar Producto">
    </form>

    <p style="text-align:center; margin-top:20px;">
        <a href="get_productos_xhtml_v2.php?tope=1000" style="color:#00BFFF; text-decoration:none; font-weight:bold;">Ver todos los productos</a> |
        <a href="get_productos_vigentes_v2.php?tope=1000" style="color:#00BFFF; text-decoration:none; font-weight:bold;">Ver productos vigentes</a>
    </p>
</body>
</html>