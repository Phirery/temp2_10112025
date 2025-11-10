<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

try {
    // Tổng bệnh nhân
    $totalSql = "SELECT COUNT(*) as total FROM benhnhan";
    $total = $conn->query($totalSql)->fetch_assoc()['total'];

    // Nam
    $maleSql = "SELECT COUNT(*) as male FROM benhnhan WHERE gioiTinh = 'nam'";
    $male = $conn->query($maleSql)->fetch_assoc()['male'];

    // Nữ
    $femaleSql = "SELECT COUNT(*) as female FROM benhnhan WHERE gioiTinh = 'nu'";
    $female = $conn->query($femaleSql)->fetch_assoc()['female'];

    // Có BHYT
    $bhytSql = "SELECT COUNT(*) as bhyt FROM benhnhan WHERE soTheBHYT IS NOT NULL AND soTheBHYT != ''";
    $bhyt = $conn->query($bhytSql)->fetch_assoc()['bhyt'];

    echo json_encode([
        'success' => true,
        'total' => (int)$total,
        'male' => (int)$male,
        'female' => (int)$female,
        'bhyt' => (int)$bhyt
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>