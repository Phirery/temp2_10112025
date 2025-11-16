<?php
require_once '../../config/cors.php';
require_once '../../core/dp.php';

session_start();

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['username'], $input['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin đăng nhập'
    ]);
    exit;
}

$username = trim($input['username']);
$password = $input['password'];

try {
    // Tìm người dùng theo tên đăng nhập hoặc số điện thoại
    $stmt = $conn->prepare("
        SELECT id, tenDangNhap, matKhau, vaiTro, trangThai 
        FROM nguoidung 
        WHERE (tenDangNhap = ? OR soDienThoai = ?)
    ");
    
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản không tồn tại'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $user = $result->fetch_assoc();

    // Kiểm tra tài khoản có bị khóa không
    if ($user['trangThai'] === 'Khóa') {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản đã bị khóa. Không thể đăng nhập'
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }

   // KIỂM TRA MẬT KHẨU AN TOÀN (CHỐNG TIMING ATTACK)

    $dbPassword = $user['matKhau'];

    // Luôn ưu tiên thử xác thực HASH trước, vì đây là trường hợp an toàn nhất
    // và phổ biến nhất sau khi hệ thống đã chạy ổn định.
    $isHashVerified = password_verify($password, $dbPassword);

    if ($isHashVerified) {
        // --- TRƯỜNG HỢP 1: MẬT KHẨU LÀ HASH VÀ KHỚP ---
        // Đây là luồng đăng nhập chuẩn, không cần làm gì thêm.
        
    } else {
        // --- TRƯỜNG HỢP 2: XÁC THỰC HASH THẤT BẠI ---
        // Lý do:
        // 1. Mật khẩu là HASH nhưng người dùng nhập sai.
        // 2. Mật khẩu là PLAINTEXT (password_verify luôn trả về false).

        // Bây giờ, chúng ta kiểm tra xem có phải là PLAINTEXT khớp không.
        // Phải dùng hash_equals() để so sánh an toàn (constant time)
        // nhằm chống lại Timing Attack.
        $isPlaintextVerified = hash_equals($dbPassword, $password);

        if ($isPlaintextVerified) {
            // --- TRƯỜNG HỢP 2A: MẬT KHẨU LÀ PLAINTEXT VÀ KHỚP ---
            
            // Tự động chuyển sang HASH mới
            // (Đây chính là logic nâng cấp của bạn)
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            $update = $conn->prepare("
                UPDATE nguoidung SET matKhau = ? WHERE id = ?
            ");
            $update->bind_param("si", $newHash, $user['id']);
            $update->execute();
            $update->close();

        } else {
            // --- TRƯỜNG HỢP 2B: MẬT KHẨU SAI ---
            // Đã thử HASH (thất bại) VÀ thử PLAINTEXT (cũng thất bại).
            // Mật khẩu chắc chắn sai.
            echo json_encode([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng',
            ]);
            $stmt->close();
            $conn->close();
            exit;
        }
    }

    $_SESSION['id'] = $user['id'];
    $_SESSION['vaiTro'] = $user['vaiTro'];
    $_SESSION['tenDangNhap'] = $user['tenDangNhap'];

    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'role' => $user['vaiTro']
    ]);

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();