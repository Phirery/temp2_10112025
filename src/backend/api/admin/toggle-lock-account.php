<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datlichkham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối thất bại']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['id'])) {
    $id = intval($data['id']);
    $trangThai = !empty($data['lock']) ? 'Khóa' : 'Hoạt Động';

    // Lấy thông tin tài khoản
    $checkQuery = "SELECT id, vaiTro FROM nguoidung WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại']);
        exit;
    }

    $user = $result->fetch_assoc();

    // Chặn khóa tài khoản quản trị
    if (strtolower($user['vaiTro']) === 'quantri' && $trangThai === 'Khóa') {
        echo json_encode(['success' => false, 'message' => 'Không thể khóa tài khoản quản trị viên']);
        exit;
    }

    // Bắt đầu transaction để an toàn
    $conn->begin_transaction();

    try {
        $updateQuery = "UPDATE nguoidung SET trangThai = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $trangThai, $id);

        if ($stmt->execute()) {
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => ($trangThai === 'Khóa' ? 'Khóa' : 'Mở khóa') . ' tài khoản thành công'
            ]);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái tài khoản']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu ID tài khoản'
    ]);
}
?>
