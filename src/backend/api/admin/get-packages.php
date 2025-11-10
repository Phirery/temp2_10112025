<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$sql = "SELECT maGoi, tenGoi, moTa, thoiLuong, gia FROM goikham ORDER BY gia";
$result = $conn->query($sql);
$packages = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $packages], JSON_UNESCAPED_UNICODE);
$conn->close();
?>