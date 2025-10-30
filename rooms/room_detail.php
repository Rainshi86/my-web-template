<!DOCTYPE html>
<html lang="th"> <!-- ใช้ภาษาไทยหรือภาษาอังกฤษ (UK) -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="room_detail.css">

    <?php
    session_start(); // เรียก session_start() ทุกครั้ง

    // ตรวจสอบสถานะการล็อกอิน
    $is_logged_in = isset($_SESSION['user']); // เปลี่ยนจาก 'user_id' เป็น 'user'

    // ถ้าไม่ได้ล็อกอิน ก็จะให้เป็นค่า 'Guest' หรือให้ไปยังหน้าเข้าสู่ระบบ
    if (!$is_logged_in) {
        $username = 'Guest';  // กำหนดชื่อผู้ใช้เป็น Guest
    } else {
        $username = $_SESSION['user'];
    }

    include '../connect.php';

    // ตรวจสอบการส่ง room_id มา
    if (isset($_GET['room_id'])) {
        $room_id = $_GET['room_id'];
    
        // ดึงข้อมูลห้องจากฐานข้อมูล
        $sql = "SELECT * FROM rooms WHERE room_id = $room_id";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            $room = $result->fetch_assoc();
        } else {
            echo "ห้องพักไม่พบ";
            exit;
        }
    } else {
        echo "กรุณาเลือกห้อง";
        exit;
    }

    $conn->close();
    ?>
</head>
<body>
    <!-- Navbar แรก -->
    <div class="navbar1">
        <div class="navbar-right">
            <?php if ($is_logged_in): ?>
                <span>ยินดีต้อนรับ, <?php echo htmlspecialchars($username); ?>!</span>
                <a href="../logout.php">ออกจากระบบ</a>
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

    <!-- แสดงรายละเอียดห้อง -->
    <div class="room-details-container">
        <h1>ชื่อห้อง :<?php echo $room['room_name']; ?></h1>
        <div class="room-image">
            <!-- <img src="<?php echo $room['image_url']; ?>" alt="<?php echo $room['room_name']; ?>"> -->
        </div>
    </div>

    <div class="room-image">
        <!-- <img src="https://blog.canadianloghomes.com/wp-content/uploads/2022/02/modern-farmhouse-bedroom-ideas-3.jpg" alt="ห้องพัก"> -->
        <img src="/projectRentRoom/rooms/img/room/room.jpg" alt="ห้องพัก">
    </div>

    <!-- ระบบการจองห้องพัก -->
    <div class="booking">
        <h2>ระบบการจองห้องพัก</h2>
        <?php if ($is_logged_in): ?>
            <form id="bookingForm" action="booking.php" method="POST">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
            <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($room['room_name']); ?>">
            
            <label>วันที่เช็คอิน</label>
            <input type="datetime-local" id="check_in" name="check_in" step="60" required>

            <label>วันที่เช็คเอาต์</label>
            <input type="datetime-local" id="check_out" name="check_out" step="60" required>

            <label>จำนวนเดือน</label>
            <input type="number" id="months" name="months" min="1" value="1" required>

            <label>จำนวนผู้เข้าพัก</label>
            <select id="guests" name="guests" required> 
                <option value="1">1 คน</option>
                <option value="2">2 คน</option>
            </select>

            <input type="hidden" name="price" value="<?php echo $room['price']; ?>"> <!-- ราคา -->
            <input type="hidden" name="total_price" id="total_price" value=""> <!-- ราคารวม -->

            <button type="submit">Continue</button>
            </form>
        <?php else: ?>
            <p>กรุณา <a href="../index.php">เข้าสู่ระบบ</a> ก่อนจองห้องพัก</p>
        <?php endif; ?>
    </div>

    <div class="room-info"> 
        <h2>ข้อกำหนดในการเช่า Apartment</h2>
        <p><strong>คำอธิบาย:</strong> <?php echo htmlspecialchars($room['description']); ?></p>
        
        <h3>รายละเอียดห้อง</h3>
            <p><strong>ห้องชั้นที่:</strong> <?php echo htmlspecialchars($room['floor']); ?></p>
            <p><strong>ห้องเลขที่:</strong> <?php echo htmlspecialchars($room['room_name']); ?></p>
            <p><strong>ขนาดห้อง:</strong> <?php echo htmlspecialchars($room['room_size']); ?> ตร.ม.</p>
            <p><strong>เตียงนอน:</strong> <?php echo htmlspecialchars($room['bed_count']); ?> เตียง</p>
            <p><strong>ห้องน้ำ:</strong> <?php echo htmlspecialchars($room['restroom_count']); ?> ห้อง</p>

        <h3>สิ่งอำนวยความสะดวก</h3>
            <p>
                <?php echo htmlspecialchars($room['facilities']); ?>
            </p>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkinInput = document.getElementById('check_in');
        const checkoutInput = document.getElementById('check_out');
        const monthsInput = document.getElementById('months');

        // ห้ามเลือกวันย้อนหลัง
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const hours = String(today.getHours()).padStart(2, '0');
        const minutes = String(today.getMinutes()).padStart(2, '0');

        // สร้างค่าวันที่ในรูปแบบ 'YYYY-MM-DDTHH:MM'
        const currentDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

        // กำหนดวันที่และเวลาเริ่มต้น
        checkinInput.setAttribute('min', currentDateTime);

        // ฟังก์ชันคำนวณวันที่เช็คเอาต์โดยเพิ่มจำนวนเดือน
        function calculateCheckoutDate() {
            if (checkinInput.value) {
                const checkinDate = new Date(checkinInput.value);
                const months = parseInt(monthsInput.value); // อ่านจำนวนเดือนที่กรอก

                // เพิ่มจำนวนเดือนที่กรอกให้กับวันที่เช็คอิน
                checkinDate.setMonth(checkinDate.getMonth() + months);

                // ใช้ toISOString เพื่อให้ได้รูปแบบ 24 ชั่วโมง
                const checkoutDateTime = checkinDate.toISOString().slice(0, 16); // แปลงเวลาเป็นรูปแบบ 24 ชั่วโมง
                // ตั้งค่าวันที่เช็คเอาต์
                checkoutInput.setAttribute('min', checkoutDateTime);
                checkoutInput.value = checkoutDateTime;
            }
        }

        // คำนวณวันที่เช็คเอาต์เมื่อเลือกวันที่เช็คอิน
        checkinInput.addEventListener('change', function() {
            calculateCheckoutDate();
        });

        // คำนวณวันที่เช็คเอาต์เมื่อจำนวนเดือนเปลี่ยน
        monthsInput.addEventListener('change', function() {
            calculateCheckoutDate();
        });
    });
</script>




</body>
</html>
