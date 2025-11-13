<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['error' => 'Kết nối thất bại:((']);
    exit;
}

// ===== Khi hoàn thiện chức năng đăng nhập, bỏ comment đoạn session bên dưới =====
/*
session_start();
if (!isset($_SESSION['id']) || $_SESSION['vaiTro'] !== 'bacsi') {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập hoặc không phải bác sĩ']);
    exit;
}
$idNguoiDung = $_SESSION['id'];
*/

// ===== Tạm thời gán ID mẫu để test =====
$idNguoiDung = 5; // tương ứng với bác sĩ "Nguyễn Thành C" trong DB của bạn

try {
    $stmt = $conn->prepare("
        SELECT bs.tenBacSi, bs.maBacSi, ck.tenChuyenKhoa, k.tenKhoa
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.maChuyenKhoa = ck.maChuyenKhoa
        LEFT JOIN khoa k ON ck.maKhoa = k.maKhoa
        WHERE bs.nguoiDungId = ?
    ");
    $stmt->bind_param("i", $idNguoiDung);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'data' => $row
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin bác sĩ']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

$conn->close();
?>
