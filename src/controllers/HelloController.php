<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HelloController
{
    public function greet(Request $request, Response $response, $args)
    {
        $name = $args['name'];
        $data = ['message' => "Hello, $name"];
        return $response->withJson($data);
    }
}
