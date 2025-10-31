<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K.K.Apartment</title>
    <link rel="stylesheet" href="index_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <?php
session_start();  // เริ่มต้น session

// เชื่อมต่อฐานข้อมูล
require_once 'db.php'; // เชื่อมต่อกับไฟล์ฐานข้อมูล
include 'db.php';

// ตรวจสอบว่า user ถูกตั้งค่าหรือไม่ใน session
$is_logged_in = isset($_SESSION['user']); // ถ้าล็อกอินจะเป็น true

// ตั้งค่าชื่อผู้ใช้จาก session หรือให้เป็นค่าพื้นฐาน
$username = isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'ผู้ใช้งาน';  // กรณีไม่ได้ล็อกอินให้เป็น 'ผู้ใช้งาน'

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role']; switch ($role) {
    case 'admin':
        // ถ้าเป็น admin ให้ทำสิ่งนี้
        break;
    case 'employee':
        // ถ้าเป็น employee ให้ทำสิ่งนี้
        break;
    case 'customer':
        // ถ้าเป็น customer ให้ทำสิ่งนี้
        break;
    default:
        // ถ้าไม่ตรงกับบทบาทใดๆ ให้ทำสิ่งนี้
        break;
}
    $role = 'user'; // ถ้าไม่ได้ล็อกอินให้เป็น 'user'
}

// ดึงข้อมูลห้องพักจากฐานข้อมูล
$stmt = $pdo->query("SELECT * FROM rooms");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);  

// ตรวจสอบว่ามีข้อความแจ้งเตือนใน session หรือไม่
if (isset($_SESSION['booking_success_message'])) {
    $message = $_SESSION['booking_success_message'];
    unset($_SESSION['booking_success_message']);  // ลบข้อความแจ้งเตือนหลังจากแสดงผล
}

?>


</head>
<body>
    <!-- Navbar แรก -->
<div class="navbar1">
    <div class="navbar-right">
        <?php if ($is_logged_in): ?>
            <span>ยินดีต้อนรับ, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="logout.php">ออกจากระบบ</a>
            <div class="user-menu">
                <?php 
                // แสดงเมนูสำหรับแต่ละบทบาท
                if ($_SESSION['role'] === 'admin') {
                    echo '<a href="admin/admin.php">แดชบอร์ดแอดมิน</a>';
                    echo '<a href="admin/manage_rooms.php">จัดการห้องพัก</a>';
                    echo '<a href="admin/manage_users.php">จัดการผู้ใช้</a>';
                    echo '<a href="rooms/my_bookings.php">จัดการจอง</a>';
                } elseif ($_SESSION['role'] === 'employee') {
                    echo '<a href="employee_dashboard.php">แดชบอร์ดพนักงาน</a>';
                    echo '<a href="manage_bookings.php">จัดการการจอง</a>';
                    echo '<a href="view_rooms.php">ดูห้องพัก</a>';
                } elseif ($_SESSION['role'] === 'customer') {
                    echo '<a href="customer_dashboard.php">แดชบอร์ดลูกค้า</a>';
                    echo '<a href="rooms/my_bookings.php">การจองของฉัน</a>';
                }
                ?>
            </div>
        <?php else: ?>
            <a href="register.html">สมัครสมาชิก</a>
            <a href="login.html">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </div>
</div>

<!-- Navbar สอง -->
<div class="navbar2">
    <div class="navbar-left">
        <img src="img/logo.jpg" alt="โลโก้" class="logo">
        <span class="site-name">K.K.Apartment</span>
    </div>
    <div class="navbar-right">
        <a href="index.php">หน้าแรก</a>
        <a href="rooms/rooms.php">ห้องพัก</a>
        <a href="#">เกี่ยวกับเรา</a>
        <a href="#">ติดต่อเรา</a>
    </div>
</div>


    <!-- รูปภาพห้องพัก -->
    <div class="room-image">
        <img src="img/main.jpg" alt="ห้องพัก">
    </div>

    <!-- ระบบการจองห้องพัก
    <div class="booking">
        <h2>ระบบการจองห้องพัก</h2>
        <form>
            <input type="date" id="check-in" name="check-in">
            <input type="date" id="check-out" name="check-out">
            <button type="submit">Book Now</button>
        </form>
    </div> -->

    <!-- welcome to my retal rooms -->
    <div class="welcome">
            <h2>ยินดีต้อนรับสู่ K.K.Apartment</h2>
            <p>ห้องเช่ารายเดือน
ห้องแอร์ พร้อมเฟอร์นิเจอร์
ฟรี wifi
เข้า ออก ใช้ระบบสแกนนิ้ว
มีลิฟท์</p>
            
    </div>

    <!-- เกี่ยวกับเรา -->
    <div class="about_us">
        <div class="grid1">
            <h2>เกี่ยวกับเรา</h2>
            <p>เค.เค.อพาร์ทเม้นท์
            ซ.วัฒนะภูติ ถ.บางกรวย-ไทรน้อย บางบัวทอง บางบัวทอง นนทบุรี</p>
        </div>

        <div class="grid2"> 
            <img src="img/kk.jpg" alt="">
        </div>
    </div>

    <!-- ห้องของเรา
    <div class="our-rooms">
        <h2>ห้องของเรา</h2>
        <p>รายละเอียดห้องพัก...</p>
        <div class="room-gallery">
            <img src="room1.jpg" alt="ห้อง 1">
            <img src="room2.jpg" alt="ห้อง 2">
            <img src="room3.jpg" alt="ห้อง 3">
        </div>
    </div> -->

    <footer class="footer">
        <p>&copy; 2024 ระบบจองห้องพัก. สงวนลิขสิทธิ์.</p>
    </footer>

            <!-- JavaScript สำหรับการแสดง alert() -->
    <script>
        <?php if (isset($message)): ?>
            // แสดงข้อความแจ้งเตือนแบบ popup
            alert("<?php echo $message; ?>");

            // หลังจากแสดงข้อความแล้ว เปลี่ยนเส้นทางไปยังหน้า index
            window.location.href = "index.php";
        <?php endif; ?>
    </script>

    <!-- JavaScript และ jQuery สำหรับการใช้ Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
