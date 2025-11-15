<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

try {
    $sql = "SELECT t.maThongBao, t.maNghi, t.loai, t.tieuDe, t.noiDung, t.thoiGian, t.daXem,
            bs.tenBacSi, n.ngayNghi, c.tenCa
            FROM thongbaoadmin t
            JOIN bacsi bs ON t.maBacSi = bs.maBacSi
            LEFT JOIN ngaynghi n ON t.maNghi = n.maNghi
            LEFT JOIN calamviec c ON n.maCa = c.maCa
            ORDER BY t.thoiGian DESC";
    
    $result = $conn->query($sql);
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['daXem'] = (bool)$row['daXem'];
        $row['ngayNghi'] = $row['ngayNghi'] ? date('d/m/Y', strtotime($row['ngayNghi'])) : null;
        $notifications[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>