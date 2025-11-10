<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$sql = "SELECT maCa, tenCa, gioBatDau, gioKetThuc FROM calamviec ORDER BY gioBatDau";
$result = $conn->query($sql);
$shifts = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $shifts[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $shifts], JSON_UNESCAPED_UNICODE);
$conn->close();
?>