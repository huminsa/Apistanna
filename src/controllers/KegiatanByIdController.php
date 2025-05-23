<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class KegiatanByIdController
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getById(Request $request, Response $response, array $args)
    {
        try {
            // Ambil ID dari parameter URL
            $id = $args['id'];

            // Query SQL untuk mengambil data kegiatan berdasarkan ID
            $sql = "SELECT `id`, `kode_akun`, `kode_no_akun`, `nama_kegiatan`, `anggaran_diajukan`, 
                           `anggaran_disetujui`, `anggaran_dicairkan`, `anggaran_digunakan`, `diajukan_oleh`, 
                           `tgl_pengajuan`, `disetujui_oleh`, `tgl_disetujui`, `dibuat_oleh`, `tgl_dibuat` 
                    FROM `tbl_kegiatan` 
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $kegiatan = $stmt->fetch();

            if (!$kegiatan) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Data kegiatan tidak ditemukan'
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $kegiatan
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
