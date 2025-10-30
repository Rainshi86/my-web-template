<?php
session_start(); // เริ่มต้นการใช้งาน Session

// ลบข้อมูลทั้งหมดใน Session
session_unset();  // ลบตัวแปร Session ทั้งหมด
session_destroy(); // ทำลาย Session

// เปลี่ยนเส้นทางกลับไปที่หน้า Login
header('Location: index.php');
exit();
?>