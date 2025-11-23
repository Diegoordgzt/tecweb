<?php

require 'vendor/autoload.php';

$app = new \Slim\App();

$app->get('/', function ($request, $response, $args) {
    $response->write("Hola Mundo Slim!!!");
    return $response;
});

//Ejemplo 2
$app->get("/hola[/{nombre}]", function( $request, $response, $args ){
    $nombre = $args["nombre"] ?? "invitado";
    $response->getBody()->write("Hola, " . $nombre);
    return $response;
});

//Ejemplo 3
$app->post("/pruebapost", function( $request, $response, $args ){
    $reqPost = $request->getParsedBody();
    $val1 = $reqPost["val1"] ?? '';
    $val2 = $reqPost["val2"] ?? '';

    $response->getBody()->write("Valores: " . $val1 . " " . $val2);
    return $response;
});
$app->run();
