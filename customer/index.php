<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพักออนไลน์</title>
    <link rel="stylesheet" href="cusstyle.css">
    <?php
        // เชื่อมต่อฐานข้อมูล
        require_once '../db.php'; // เชื่อมต่อกับไฟล์ฐานข้อมูล

        session_start();
            if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
                header('Location: ../index.php');
                exit();
            }
        
            $username = $_SESSION['user']; // ดึงชื่อผู้ใช้จาก Session

        // ดึงข้อมูลห้องพักจากฐานข้อมูล
        $stmt = $pdo->query("SELECT * FROM rooms");
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);  
    ?>
</head>
<body>

    <!-- Navbar แรก -->
    <div class="navbar1">
        <div class="navbar-right">
        <h2>ยินดีต้อนรับ, <?php echo htmlspecialchars($username); ?>!</h2>
            <a href="../logout.php">logout</a>
        </div>
    </div>

    <!-- Navbar สอง -->
    <div class="navbar2">
        <div class="navbar-left">
            <img src="logo.png" alt="โลโก้" class="logo">
            <span class="site-name">ชื่อเว็บไซต์</span>
        </div>
        <div class="navbar-right">
            <a href="index.php">หน้าแรก</a>
            <a href="c_rooms.php?username=<?php echo urlencode($_SESSION['user']); ?>">ห้องพัก</a>
            <a href="#">เกี่ยวกับเรา</a>
            <a href="#">ติดต่อเรา</a>
        </div>
    </div>

    <!-- รูปภาพห้องพัก -->
    <div class="room-image">
        <img src="https://blog.canadianloghomes.com/wp-content/uploads/2022/02/modern-farmhouse-bedroom-ideas-3.jpg" alt="ห้องพัก">
    </div>

    <!-- ระบบการจองห้องพัก -->
    <div class="booking">
        <h2>ระบบการจองห้องพัก</h2>
        <form>
            <input type="date" id="check-in" name="check-in">
            <input type="date" id="check-out" name="check-out">
            <button type="submit">Book Now</button>
        </form>
    </div>

    <!-- welcome to my retal rooms -->
    <div class="welcome">
            <h2>ยินดีต้อนรับสู่ห้องพัก</h2>
            <p>ห้องพักของเรา...</p>
            <p>ห้องพักของเรา...</p>
            <p>ห้องพักของเรา...</p>
    </div>

    <!-- เกี่ยวกับเรา -->
    <div class="about_us">
        <div class="grid1">
            <h2>เกี่ยวกับเรา</h2>
            <p>ห้องพักของเรา...</p>
            <p>ห้องพักของเรา...</p>
            <p>ห้องพักของเรา...</p>
        </div>

        <div class="grid2"> 
            <img src="https://cdn-5d4bad43f911c80ef4a324b0.closte.com/wp-content/uploads/2018/05/IMG_0188-Pano-1-860x610.jpg" alt="">
        </div>
    </div>

    <!-- ห้องของเรา -->
    <div class="our-rooms">
        <h2>ห้องของเรา</h2>
        <p>รายละเอียดห้องพัก...</p>
        <div class="room-gallery">
            <img src="room1.jpg" alt="ห้อง 1">
            <img src="room2.jpg" alt="ห้อง 2">
            <img src="room3.jpg" alt="ห้อง 3">
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 ระบบจองห้องพัก. สงวนลิขสิทธิ์.</p>
    </footer>
</body>
</html>
