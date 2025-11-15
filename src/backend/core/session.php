<?php
session_start();

function requireLogin($role = null) {
    if (!isset($_SESSION['id'])) {
        echo json_encode(["success" => false, "message" => "Chưa đăng nhập"]);
        exit;
    }

    if ($role !== null && $_SESSION['vaiTro'] !== $role) {
        echo json_encode(["success" => false, "message" => "Không có quyền truy cập"]);
        exit;
    }
}
function getDoctorId($conn) {
    $stmt = $conn->prepare("SELECT maBacSi FROM bacsi WHERE nguoiDungId = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ? $row['maBacSi'] : null;
}