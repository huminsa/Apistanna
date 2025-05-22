<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
$app = new \Slim\App();

$app->get('/hello/{name}', function ($request, $response, $args) {
    $name = $args['name'];
    return $response->withJson(["message" => "Hello, $name!"]);
});

$app->run();
