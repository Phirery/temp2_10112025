<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$maCa = isset($_GET['maCa']) ? intval($_GET['maCa']) : 0;

if (!$maCa) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã ca']);
    exit;
}

$sql = "SELECT maSuat, maCa, gioBatDau, gioKetThuc 
        FROM suatkham 
        WHERE maCa = $maCa 
        ORDER BY gioBatDau";
$result = $conn->query($sql);
$slots = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
}

echo json_encode(['success' => true, 'data' => $slots], JSON_UNESCAPED_UNICODE);
$conn->close();
?>