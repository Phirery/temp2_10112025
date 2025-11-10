<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$maLichKham = $conn->real_escape_string($data['maLichKham']);
$maBenhNhan = $conn->real_escape_string($data['maBenhNhan']);
$maBacSi = $conn->real_escape_string($data['maBacSi']);
$ngayKham = $conn->real_escape_string($data['ngayKham']);
$maCa = intval($data['maCa']);
$maSuat = intval($data['maSuat']);
$maGoi = !empty($data['maGoi']) ? intval($data['maGoi']) : 'NULL';
$trangThai = $conn->real_escape_string($data['trangThai']);
$ghiChu = !empty($data['ghiChu']) ? "'".$conn->real_escape_string($data['ghiChu'])."'" : 'NULL';

$sql = "UPDATE lichkham 
        SET maBenhNhan = '$maBenhNhan',
            maBacSi = '$maBacSi',
            ngayKham = '$ngayKham',
            maCa = $maCa,
            maSuat = $maSuat,
            maGoi = $maGoi,
            trangThai = '$trangThai',
            ghiChu = $ghiChu
        WHERE maLichKham = '$maLichKham'";

if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật lịch khám thành công!'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy lịch khám hoặc dữ liệu không thay đổi'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>