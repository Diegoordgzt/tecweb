<?php
require_once __DIR__ . '/myapi/Products.php';

use TECWEB\MYAPI\Products;

$products = new Products('marketzone');
$products->list(); 
echo $products->getData();
?>
