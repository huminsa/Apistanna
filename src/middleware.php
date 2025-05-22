<?php
// src/middleware.php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// Buat middleware tanpa menerapkannya secara global
$jwtMiddleware = function ($request, $response, $next) {
    $authHeader = $request->getHeaderLine('Authorization');

    if (empty($authHeader)) {
        $response->getBody()->write(json_encode(array('error' => 'Token tidak ada')));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $token = $matches[1];
        try {
            // Untuk versi lama Firebase JWT
            $decoded = JWT::decode($token, new Key('secret_key', 'HS256'));

            // Tambahkan data user ke request
            $request = $request->withAttribute('user', $decoded);
            return $next($request, $response);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(array('error' => 'Token tidak valid: ' . $e->getMessage())));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }

    $response->getBody()->write(json_encode(array('error' => 'Format token salah')));
    return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
};

// JANGAN menerapkan middleware secara global
// $app->add($jwtMiddleware);