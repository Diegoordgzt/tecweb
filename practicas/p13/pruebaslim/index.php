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


$app->run();
