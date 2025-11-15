<?php
header("Content-Type: application/json; charset=UTF-8");

use TECWEB\MYAPI\Products as Products; 
require_once __DIR__ . '/myapi/Products.php'; 

$prodObj = new Products('marketzone'); 

$id = $_POST['id'] ?? $_GET['id'] ?? null;

$data = $prodObj->single($id); // <-- Debe devolver datos, NO imprimir

echo json_encode($data);
?>
