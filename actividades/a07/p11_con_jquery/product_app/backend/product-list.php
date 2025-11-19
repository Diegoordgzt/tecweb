<?php
use TECWEB\MYAPI\Products as Products;
require_once __DIR__.'/myapi/Products.php';

header('Content-Type: application/json');

$prodObj = new Products('marketzone');
$prodObj->list();

$data = $prodObj->getData();

// FORMATO CORRECTO QUE ESPERA app.js:
echo json_encode([
    "status" => "success",
    "data" => $data
]);
