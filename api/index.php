<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'logErrors' => true,
        'logErrorDetails' => true
    ]
]);

$app->get('/', function ($request, $response, $args) {
    $data = ['message' => 'Welcome to the API stanna'];
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200)->write(json_encode($data));
});


// Tambahkan middleware untuk parsing JSON jika menggunakan Slim 3
$app->add(function ($request, $response, $next) {
    if ($request->getContentType() === 'application/json') {
        $body = $request->getBody();
        $parsedBody = json_decode($body, true);
        if ($parsedBody) {
            $request = $request->withParsedBody($parsedBody);
        }
    }
    return $next($request, $response);
});

$container = $app->getContainer();

require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/middleware.php';
require __DIR__ . '/../src/routes.php';

$app->run();