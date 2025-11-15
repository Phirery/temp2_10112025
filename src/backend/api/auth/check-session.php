<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');

session_start();

if (isset($_SESSION['id'])) {
    echo json_encode([
        'logged_in' => true,
        'role' => $_SESSION['vaiTro']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}