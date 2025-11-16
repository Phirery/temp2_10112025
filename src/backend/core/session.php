<?php
// Bắt đầu session một cách an toàn (chỉ bắt đầu nếu chưa có)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Hàm kiểm tra vai trò người dùng.
 * Nếu không hợp lệ, sẽ tự động trả về lỗi JSON và dừng script.
 *
 * @param string|array $roles Vai trò yêu cầu (vd: 'quantri')
 * hoặc một mảng các vai trò (vd: ['quantri', 'bacsi'])
 */
function require_role($roles) {
    
    // 1. Kiểm tra xem đã đăng nhập chưa
    if (!isset($_SESSION['id']) || !isset($_SESSION['vaiTro'])) {
        http_response_code(401); // 401 - Unauthorized (Chưa xác thực)
        echo json_encode([
            'success' => false,
            'message' => 'Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.'
        ]);
        exit;
    }

    // 2. Lấy vai trò của người dùng từ session
    $userRole = $_SESSION['vaiTro'];

    // 3. Kiểm tra vai trò
    $isAllowed = false;
    
    if (is_array($roles)) {
        // Nếu $roles là một mảng (cho phép nhiều vai trò)
        if (in_array($userRole, $roles)) {
            $isAllowed = true;
        }
    } else {
        // Nếu $roles là một chuỗi (chỉ 1 vai trò)
        if ($userRole === $roles) {
            $isAllowed = true;
        }
    }

    // 4. Nếu không được phép, trả về lỗi
    if (!$isAllowed) {
        http_response_code(403); // 403 - Forbidden (Bị cấm)
        echo json_encode([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện hành động này.'
        ]);
        exit;
    }
    
    // Nếu đến được đây, người dùng hợp lệ -> không làm gì cả,
    // để script gốc (API) tiếp tục chạy
}

?>