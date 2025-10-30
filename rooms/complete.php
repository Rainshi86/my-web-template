<?php
session_start();
include '../db.php'; // Ensure the connection is being established

// ตรวจสอบสถานะการล็อกอิน
$is_logged_in = isset($_SESSION['user']); // เปลี่ยนจาก 'user_id' เป็น 'user'

if (!$is_logged_in) {
    $username = 'Guest';  // กำหนดชื่อผู้ใช้เป็น Guest
} else {
    $username = $_SESSION['user'];
}

$errorMessage = ''; // ตัวแปรสำหรับเก็บข้อความแจ้งเตือน

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $roomId = $_POST['room_id'] ?? '';
    $roomName = $_POST['room_name'] ?? '';
    $checkIn = $_POST['check_in'] ?? '';
    $checkOut = $_POST['check_out'] ?? '';
    $guests = $_POST['guests'] ?? '';
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $subDistrict = $_POST['sub_district'] ?? '';
    $district = $_POST['district'] ?? '';
    $province = $_POST['province'] ?? '';
    $postalCode = $_POST['postal_code'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $details = $_POST['details'] ?? '';

    // ตรวจสอบว่าข้อมูลที่จำเป็นทั้งหมดถูกกรอก
    if (empty($roomId) || empty($roomName) || empty($checkIn) || empty($checkOut) || empty($guests) ||
        empty($firstName) || empty($lastName) || empty($address) || empty($subDistrict) || empty($district) ||
        empty($province) || empty($postalCode) || empty($phone) || empty($email)) {
        $errorMessage = "";
    } else {
        // ดำเนินการต่อไป เช่น คำนวณราคา และอัปโหลดไฟล์
    }

    // ดึงราคาห้องจากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT price FROM rooms WHERE room_id = :room_id");
    $stmt->bindParam(':room_id', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room) {
        $pricePerNight = $room['price']; // ราคาต่อคืน
    } else {
        $pricePerNight = 0; // หากไม่มีข้อมูล ให้ราคาต่อคืนเป็น 0
    }

    // คำนวณจำนวนคืนที่พัก
    if (!empty($checkIn) && !empty($checkOut)) {
        $startDate = new DateTime($checkIn);
        $endDate = new DateTime($checkOut);
        $interval = $startDate->diff($endDate);
        $totalDays = $interval->days; // จำนวนคืนที่พัก
    } else {
        $totalDays = 0;
    }

    // คำนวณราคารวม
    $totalPrice = $pricePerNight * $totalDays;

    // Handle file upload if needed
    if (isset($_FILES['payment_slip'])) {
        // Process file upload (e.g., save the file)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>
    <!-- Navbar แรก -->
    <div class="navbar1">
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <span>ยินดีต้อนรับ, <?php echo htmlspecialchars($username); ?>!</span>
                <a href="logout.php">ออกจากระบบ</a>
            <?php else: ?>
                <a href="../register.html">สมัครสมาชิก</a>
                <a href="../login.html">เข้าสู่ระบบ</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navbar สอง -->
    <div class="navbar2">
        <div class="navbar-left">
            <img src="../img/logo.jpg" alt="โลโก้" class="logo">
            <span class="site-name">K.K.Apartment</span>
        </div>
        <div class="navbar-right">
            <a href="../index.php">หน้าแรก</a>
            <a href="./rooms.php">ห้องพัก</a>
            <a href="#">เกี่ยวกับเรา</a>
            <a href="#">ติดต่อเรา</a>
        </div>
    </div>