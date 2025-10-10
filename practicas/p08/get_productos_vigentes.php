<?php
header("Content-Type: text/html; charset=utf-8");

if (!isset($_GET['tope'])) {
    die('<p>Parámetro "tope" no detectado. Usa: archivo.php?tope=5</p>');
}

$tope = intval($_GET['tope']);
$data = array();

/** SE CREA EL OBJETO DE CONEXION */
$link = new mysqli('localhost', 'root', 'diegord17', 'marketzone');

/** Comprobar la conexión */
if ($link->connect_errno) {
    die('<p>Falló la conexión: ' . $link->connect_error . '</p>');
}

/** Consultar productos con unidades menores o iguales al tope */
$sql = "SELECT * FROM productos WHERE unidades <= ? AND eliminado = 0";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $tope);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

$stmt->close();
$link->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h3>PRODUCTOS</h3>
        <br/>
        <?php if (!empty($data)): ?>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Unidades</th>
                        <th scope="col">Detalles</th>
                        <th scope="col">Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $producto): ?>
                        <tr>
                            <th scope="row"><?= htmlspecialchars($producto['id']) ?></th>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td><?= htmlspecialchars($producto['marca']) ?></td>
                            <td><?= htmlspecialchars($producto['modelo']) ?></td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td><?= htmlspecialchars($producto['unidades']) ?></td>
                            <td><?= htmlspecialchars($producto['detalles']) ?></td>
                            <td><img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen" width="80"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
<script>
            alert('No hay productos que cumplan con el criterio.');
        </script>
        <?php endif; ?>
    </div>
</body>
</html>