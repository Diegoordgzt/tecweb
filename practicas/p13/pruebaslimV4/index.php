<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
// /myapp/api is the api folder http://domain/myapp/api/
$app->setBasePath("/tecweb/practicas/p13/pruebaslimV4");
//Ejemplo 1
$app->get('/', function (Request $request, Response $response, array $args): Response {
    $response->getBody()->write("Hola Mundo Slim!!!");
    return $response;
});

//Ejemplo 2
$app->get("/hola[/{nombre}]", function (Request $request, Response $response, array $args): Response {
    $nombre = $args["nombre"] ?? "invitado";
    $response->getBody()->write("Hola, " . $nombre);
    return $response;
});

//Ejemplo 3
$app->post("/pruebapost", function (Request $request, Response $response, array $args): Response {
    $reqPost = $request->getParsedBody();
    $val1 = $reqPost["val1"] ?? '';
    $val2 = $reqPost["val2"] ?? '';

    $response->getBody()->write("Valores: " . $val1 . " " . $val2);
    return $response;
});

//Ejemplo 4
$app->get("/testjson", function (Request $request, Response $response, array $args): Response {
    $data = [];

    $data[0]["nombre"]    = "Sergio";
    $data[0]["apellidos"] = "Rojas Espino";
    $data[1]["nombre"]    = "Pedro";
    $data[1]["apellidos"] = "Perez Lopez";

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write($json);

    return $response;
});

$app->run();