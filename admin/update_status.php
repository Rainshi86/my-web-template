<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    die("คุณไม่มีสิทธิ์เข้าถึงหน้านี้!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $new_status = $_POST['payment_status'] ?? null;

    if (!$booking_id || !$new_status) {
        die("ข้อมูลไม่ครบถ้วน!");
    }

    // ตรวจสอบว่าสถานะที่ส่งมาเป็นค่าที่ถูกต้อง
    $allowed_status = ['pending', 'processed', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed_status)) {
        die("สถานะไม่ถูกต้อง!");
    }

    // อัปเดตสถานะในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE bookings SET payment_status = ?, updated_at = NOW() WHERE booking_id = ?");
    $stmt->execute([$new_status, $booking_id]);

    echo "อัปเดตสถานะสำเร็จ!";
    header("Location: booking_list.php"); // เปลี่ยนเส้นทางไปหน้าผู้ดูแลระบบ
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manage_rooms.css">
    <title>Document</title>
</head>
<body>
    
</body>
</html>