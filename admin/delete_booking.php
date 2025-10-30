<?php
include '../db.php';

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่าได้รับ booking_id หรือไม่
    $bookingId = $_POST['booking_id'] ?? null;
    
    if ($bookingId) {
        try {
            // คำสั่ง SQL สำหรับลบข้อมูลการจอง
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?");
            $stmt->execute([$bookingId]);

            // ถ้าลบสำเร็จ, redirect ไปที่หน้าจัดการการจอง
            header("Location: booking_list.php");
            exit();
        } catch (PDOException $e) {
            die("❌ ข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage());
        }
    } else {
        die("❌ ไม่พบ booking_id ที่ต้องการลบ!");
    }
} else {
    die("❌ วิธีการส่งข้อมูลผิดพลาด!");
}
?>
