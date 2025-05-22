<?php
// src/controllers/ProdiController.php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class ProdiController
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(Request $request, Response $response)
    {
        try {
            // Dapatkan informasi user dari token JWT
            $user = $request->getAttribute('user');

            // Periksa apakah $user adalah objek dan memiliki property sub
            $username = ($user && is_object($user) && isset($user->sub)) ? $user->sub : 'anonymous';

            // Siapkan query SQL
            $sql = "SELECT kd_prodi, nm_prodi, status, jenjang_studi FROM tbprodi";
            // Siapkan parameter untuk prepared statement
            $params = array();

            // Eksekusi query
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $prodi = $stmt->fetchAll();

            // Response
            $response->getBody()->write(json_encode(array(
                'status' => 'success',
                'data' => $prodi,
                'user' => $username,
                'total' => count($prodi)
            )));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(array(
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            )));

            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // Tambahkan method untuk mendapatkan detail prodi berdasarkan kode
    public function getByKode(Request $request, Response $response, $args)
    {
        try {
            // Ambil kode prodi dari parameter URL
            $kd_prodi = $args['kd_prodi'];

            // Query database
            $stmt = $this->db->prepare("SELECT * FROM tbprodi WHERE kd_prodi = ?");
            $stmt->execute(array($kd_prodi));
            $prodi = $stmt->fetch();

            // Cek apakah prodi ditemukan
            if (!$prodi) {
                $response->getBody()->write(json_encode(array(
                    'status' => 'error',
                    'message' => 'Data prodi tidak ditemukan'
                )));

                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Response
            $response->getBody()->write(json_encode(array(
                'status' => 'success',
                'data' => $prodi
            )));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(array(
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            )));

            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
