<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../database/db_connect.php';

session_start();

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['username'], $input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

$username = $input['username'];
$password = $input['password'];

$stmt = $conn->prepare("SELECT * FROM nguoidung WHERE taiKhoan = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
    exit;
}

// Nếu bạn dùng hash thì thay bằng password_verify()
if ($password !== $user['matKhau']) {
    echo json_encode(['success' => false, 'message' => 'Sai mật khẩu']);
    exit;
}

$_SESSION['id'] = $user['nguoiDungId'];
$_SESSION['vaiTro'] = $user['vaiTro'];

echo json_encode([
    'success' => true,
    'role' => $user['vaiTro']
]);

$stmt->close();
$conn->close();