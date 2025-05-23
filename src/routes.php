<?php
// src/routes.php
require __DIR__ . '/../src/middleware.php';

$app->post('/login', \App\Controllers\AuthController::class . ':login');

// Lindungi group dengan middleware
$app->group('', function () use ($app) {
    $app->get('/mahasiswa', \App\Controllers\MahasiswaController::class . ':index');
    $app->get('/kegiatan', \App\Controllers\KegiatanController::class . ':index');
    $app->get('/kegiatan/{id}', \App\Controllers\KegiatanByIdController::class . ':getById');

    $app->get('/dosen', \App\Controllers\DosenController::class . ':index');
    $app->get('/prodi', \App\Controllers\ProdiController::class . ':index');
    $app->get('/matakuliah', \App\Controllers\MataKuliahController::class . ':index');
    $app->get('/krs', \App\Controllers\KRSController::class . ':index');
    $app->get('/mahasiswa/{nim}', \App\Controllers\MahasiswaController::class . ':getByNim');
})->add($jwtMiddleware);
