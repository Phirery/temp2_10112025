<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
date_default_timezone_set('Asia/Ho_Chi_Minh');

$conn = new mysqli("localhost", "root", "", "datlichkham");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$maBenhNhan = $conn->real_escape_string($data['maBenhNhan']);
$maBacSi = $conn->real_escape_string($data['maBacSi']);
$ngayKham = $conn->real_escape_string($data['ngayKham']);
$maCa = intval($data['maCa']);
$maSuat = intval($data['maSuat']);
$maGoi = !empty($data['maGoi']) ? intval($data['maGoi']) : 'NULL';
$trangThai = $conn->real_escape_string($data['trangThai']);
$ghiChu = !empty($data['ghiChu']) ? "'".$conn->real_escape_string($data['ghiChu'])."'" : 'NULL';

$maLichKham = 'LK' . date('YmdHis') . rand(100, 999);

if(empty($maBenhNhan) || empty($maBacSi) || empty($ngayKham) || empty($trangThai)) {
    echo json_encode(['success'=>false,'message'=>'Thiếu thông tin bắt buộc']);
    exit;
}

$sql = "INSERT INTO lichkham (maLichKham, maBenhNhan, maBacSi, ngayKham, maCa, maSuat, maGoi, trangThai, ghiChu) 
        VALUES ('$maLichKham', '$maBenhNhan', '$maBacSi', '$ngayKham', $maCa, $maSuat, $maGoi, '$trangThai', $ghiChu)";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Thêm lịch khám thành công!',
        'maLichKham' => $maLichKham
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>