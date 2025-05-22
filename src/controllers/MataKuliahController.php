<?php
// src/controllers/MataKuliahController.php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class MataKuliahController
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

            // Ambil query parameters (jika ada)
            $params = $request->getQueryParams();
            $prodi = isset($params['kd_prodi']) ? $params['kd_prodi'] : null;

            // Siapkan query SQL
            $sql = "SELECT kd_matkul, nm_matkul, sks, semester, kd_kurikulum, kd_prodi FROM tbmatakuliah ";
            // Siapkan parameter untuk prepared statement
            $params = array();

            // Filter berdasarkan prodi jika ada
            if ($prodi) {
                $sql .= " WHERE kd_prodi = ?";
                $params[] = $prodi;
            }

            // Eksekusi query
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $matakuliah = $stmt->fetchAll();

            // Response
            $response->getBody()->write(json_encode(array(
                'status' => 'success',
                'data' => $matakuliah,
                'user' => $user->sub,
                'total' => count($matakuliah)
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

    // Tambahkan method untuk mendapatkan detail mahasiswa berdasarkan nim
    public function getByNim(Request $request, Response $response, $args)
    {
        try {
            // Ambil nim dari parameter URL
            $nim = $args['npm'];

            // Query database
            $stmt = $this->db->prepare("SELECT * FROM tbmahasiswa WHERE npm = ?");
            $stmt->execute(array($nim));
            $mahasiswa = $stmt->fetch();

            // Cek apakah mahasiswa ditemukan
            if (!$mahasiswa) {
                $response->getBody()->write(json_encode(array(
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan'
                )));

                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            // Response
            $response->getBody()->write(json_encode(array(
                'status' => 'success',
                'data' => $mahasiswa
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
