<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class KRSController
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function index(Request $request, Response $response)
    {
        try {
            $user = $request->getAttribute('user');
            $params = $request->getQueryParams();
            $prodi = isset($params['prodi']) ? $params['prodi'] : null;
            $nim = isset($params['nim']) ? $params['nim'] : null;

            // Base query
            $sql = "SELECT id_krs AS nim, kd_matkul, thn_ajaran, kelas, dosen, hari, kd_jam, ruang FROM viewjadwaldetailbayangankrs";
            $conditions = ["statuskrs = 1"];
            $queryParams = [];

            // Tambahkan filter berdasarkan prodi
            if ($prodi) {
                $conditions[] = "prodi = ?";
                $queryParams[] = $prodi;
            }

            // Tambahkan filter berdasarkan nim
            if ($nim) {
                $conditions[] = "id_krs = ?";
                $queryParams[] = $nim;
            }

            // Gabungkan WHERE jika ada kondisi
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            // Tambahkan GROUP BY dan ORDER BY jika filter berdasarkan NIM
            if ($nim) {
                $sql .= " GROUP BY id ORDER BY semester, kd_matkul";
            } else {
                $sql .= " ORDER BY prodi, semester";
            }

            // Eksekusi query
            $stmt = $this->db->prepare($sql);
            $stmt->execute($queryParams);
            $krs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Response
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $krs,
                'user' => $user->sub,
                'total' => count($krs)
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]));

            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
