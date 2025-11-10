<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$stats = [
    'total' => 0,
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham");
$stats['total'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Chờ'");
$stats['pending'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Đã đặt'");
$stats['confirmed'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Hoàn thành'");
$stats['completed'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM lichkham WHERE trangThai = 'Hủy'");
$stats['cancelled'] = $result->fetch_assoc()['count'];

echo json_encode($stats);
$conn->close();
?>
