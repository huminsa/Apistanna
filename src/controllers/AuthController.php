<?php
// src/controllers/AuthController.php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use PDO;

class AuthController
{
    protected $db;

    public function __construct(PDO $db = null)
    {
        $this->db = $db;
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $username = isset($data['username']) ? $data['username'] : '';
        $password = isset($data['password']) ? $data['password'] : '';

        // Jika database tersedia, verifikasi dari database
        if ($this->db) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM usersapi WHERE username = ?");
                $stmt->execute(array($username));
                $user = $stmt->fetch();

                if (!$user || $password !== $user['password']) { // Dalam produksi, gunakan password_verify()
                    $response->getBody()->write(json_encode(array('error' => 'Unauthorized')));
                    return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
                }

                // Login berhasil, buat token
                $payload = array(
                    'iss' => 'localhost',
                    'sub' => $username,
                    'role' => $user['role'],
                    'iat' => time(),
                    'exp' => time() + 3600
                );
            } catch (\Exception $e) {
                $response->getBody()->write(json_encode(array(
                    'status' => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                )));

                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        }
        // Fallback ke hardcoded credentials jika tidak ada database
        else if ($username === 'admin' && $password === '123456') {
            $payload = array(
                'iss' => 'localhost',
                'sub' => $username,
                'role' => 'admin',
                'iat' => time(),
                'exp' => time() + 3600
            );
        } else {
            $response->getBody()->write(json_encode(array('error' => 'Unauthorized')));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Generate JWT token
        $token = JWT::encode($payload, 'secret_key', 'HS256');
        $response->getBody()->write(json_encode(array('token' => $token)));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
