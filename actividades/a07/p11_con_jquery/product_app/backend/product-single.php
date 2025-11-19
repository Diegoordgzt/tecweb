<?php
header("Content-Type: application/json; charset=UTF-8");

use TECWEB\MYAPI\Products as Products;
require_once __DIR__ . '/myapi/Products.php';

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID no recibido"
    ]);
    exit;
}

$prodObj = new Products('marketzone');
$data = $prodObj->single($id);  // Debe devolver los datos

// Validamos que sí encontró el producto
if ($data) {
    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Producto no encontrado"
    ]);
}
?>
