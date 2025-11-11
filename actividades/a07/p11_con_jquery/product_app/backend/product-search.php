<?php
use TECWEB\MYAPI\Products as Products; 
require_once __DIR__ . '/myapi/Products.php'; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$prodObj = new Products('marketzone');

if (!empty($search)) {
    $prodObj->search($search); 
} else {
    $prodObj->list(); 
}

header('Content-Type: application/json; charset=utf-8');
echo $prodObj->getData();
?>
