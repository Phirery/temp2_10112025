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

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $maBenhNhan = $conn->real_escape_string($data['maBenhNhan'] ?? '');
    $tenBenhNhan = trim($conn->real_escape_string($data['tenBenhNhan'] ?? ''));
    $soDienThoai = trim($conn->real_escape_string($data['soDienThoai'] ?? ''));
    $ngaySinh = $conn->real_escape_string($data['ngaySinh'] ?? '');
    $gioiTinh = $conn->real_escape_string($data['gioiTinh'] ?? '');
    $soTheBHYT = $conn->real_escape_string($data['soTheBHYT'] ?? null);
    $tenDangNhap = trim($conn->real_escape_string($data['tenDangNhap'] ?? ''));
    $matKhau = $data['matKhau'] ?? '';  // Nếu có, update password

    if (empty($maBenhNhan) || empty($tenBenhNhan) || empty($tenDangNhap) || empty($ngaySinh) || empty($gioiTinh)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc!');
    }

    // Lấy nguoiDungId từ benhnhan
    $getIdSql = "SELECT nguoiDungId FROM benhnhan WHERE maBenhNhan = '$maBenhNhan'";
    $result = $conn->query($getIdSql);
    if ($result->num_rows === 0) {
        throw new Exception('Không tìm thấy bệnh nhân!');
    }
    $nguoiDungId = $result->fetch_assoc()['nguoiDungId'];
    
    if (strtotime($ngaySinh) > time()) {
        throw new Exception('Ngày sinh không được vượt quá ngày hiện tại!');
    }
    // Update nguoidung
    $updateUserSql = "UPDATE nguoidung SET 
                      tenDangNhap = '$tenDangNhap',
                      soDienThoai = '$soDienThoai'" .
                      (!empty($matKhau) ? ", matKhau = '" . password_hash($matKhau, PASSWORD_DEFAULT) . "'" : "") .
                      " WHERE id = $nguoiDungId";
    if (!$conn->query($updateUserSql)) {
        throw new Exception('Lỗi cập nhật tài khoản: ' . $conn->error);
    }

    // Update benhnhan
    $updatePatientSql = "UPDATE benhnhan SET 
                         tenBenhNhan = '$tenBenhNhan',
                         ngaySinh = '$ngaySinh',
                         gioiTinh = '$gioiTinh',
                         soTheBHYT = " . ($soTheBHYT ? "'$soTheBHYT'" : "NULL") . "
                         WHERE maBenhNhan = '$maBenhNhan'";
    if (!$conn->query($updatePatientSql)) {
        throw new Exception('Lỗi cập nhật bệnh nhân: ' . $conn->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật bệnh nhân thành công!'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>