<?php

// Perbarui Constructor di dependencies.php
$container[\App\Controllers\AuthController::class] = function ($c) {
    return new \App\Controllers\AuthController($c['db']);
};

// Database connection
$container['db'] = function ($c) {
    $dbSettings = [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',  // Sesuaikan dengan password MySQL Anda
        'dbname' => 'dbase_keuangan',  // Sesuaikan dengan nama database Anda
        'charset' => 'utf8',
    ];

    $dsn = "mysql:host={$dbSettings['host']};dbname={$dbSettings['dbname']};charset={$dbSettings['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $dbSettings['user'], $dbSettings['pass'], $options);
    } catch (PDOException $e) {
        throw new Exception("Database Connection Error: " . $e->getMessage());
    }
};

// Controllers
$container[\App\Controllers\AuthController::class] = function ($c) {
    return new \App\Controllers\AuthController();
};

$container[\App\Controllers\KegiatanController::class] = function ($c) {
    return new \App\Controllers\KegiatanController($c['db']);
};
$container[\App\Controllers\KegiatanByIdController::class] = function ($c) {
    return new \App\Controllers\KegiatanByIdController($c['db']);
};
$container[\App\Controllers\MahasiswaController::class] = function ($c) {
    return new \App\Controllers\MahasiswaController($c['db']);
};
$container[\App\Controllers\DosenController::class] = function ($c) {
    return new \App\Controllers\DosenController($c['db']);
};
$container[\App\Controllers\MataKuliahController::class] = function ($c) {
    return new \App\Controllers\MataKuliahController($c['db']);
};
$container[\App\Controllers\KRSController::class] = function ($c) {
    return new \App\Controllers\KRSController($c['db']);
};
$container[\App\Controllers\ProdiController::class] = function ($c) {
    return new \App\Controllers\ProdiController($c['db']);
};
