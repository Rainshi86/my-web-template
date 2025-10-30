<?php
session_start();
require_once '../db.php';

// ตรวจสอบสถานะการล็อกอิน
$is_logged_in = isset($_SESSION['user']);
$username = $is_logged_in ? $_SESSION['user'] : 'Guest';

// ตั้งค่าจำนวนห้องต่อหน้า
$roomsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$floor = isset($_GET['floor']) ? (int)$_GET['floor'] : 1; // ถ้าไม่ได้เลือกจะเป็นชั้น 1

$startLimit = ($currentPage - 1) * $roomsPerPage;

// เชื่อมต่อกับฐานข้อมูล
include '../connect.php';

// ดึงข้อมูลห้องของชั้นที่เลือก
$sql = "SELECT * FROM rooms WHERE status = 'available' AND floor = ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $floor, $startLimit, $roomsPerPage);
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = [
        'id' => $row["room_id"],
        'room_name' => $row["room_name"],
        'image_url' => $row["image_url"],
        'price' => $row["price"],
        'floor' => $row["floor"],
    ];
}

// ดึงจำนวนห้องทั้งหมดของชั้นที่เลือกเพื่อคำนวณจำนวนหน้า
$sqlTotal = "SELECT COUNT(*) as total FROM rooms WHERE status = 'available' AND floor = ?";
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bind_param("i", $floor);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalRooms = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRooms / $roomsPerPage);

$conn->close();

// ส่งข้อมูลไปยัง JavaScript
echo "<script>var roomsData = " . json_encode($rooms) . "; var totalPages = $totalPages; var currentPage = $currentPage; var selectedFloor = $floor;</script>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="rooms_styles.css">
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

    <!-- รูปภาพห้องพัก -->
    <div class="room-image">
        <!-- <img src="https://blog.canadianloghomes.com/wp-content/uploads/2022/02/modern-farmhouse-bedroom-ideas-3.jpg" alt="ห้องพัก"> -->
        <img src="/projectRentRoom/img/main.jpg" alt="ห้องพัก">
    </div>

    <div class="container">
        <h1>Room List with Filter</h1>

        <!-- ฟิลเตอร์สำหรับเลือกชั้นห้อง -->
<div class="filters">
    <select id="floor-filter">
        <option value="">เลือกชั้น</option>
        <option value="1">ชั้น 1</option>
        <option value="2">ชั้น 2</option>
        <option value="3">ชั้น 3</option>
        <option value="4">ชั้น 4</option>
        <option value="5">ชั้น 5</option>
    </select>
</div>

<!-- แสดงรายการห้องพัก -->
<div id="room-grid" class="room-grid"></div>

<!-- ปุ่มแบ่งหน้า -->
<!-- <div class="pagination" id="pagination"></div>  -->

    <footer class="footer">
        <p>&copy; 2024 ระบบจองห้องพัก. สงวนลิขสิทธิ์.</p>
    </footer>

    <script>
       function displayRooms(rooms) {
    const roomGrid = document.getElementById('room-grid');
    roomGrid.innerHTML = rooms.map(room => {
        // เติม URL ของเว็บให้รูปภาพ
        const imageUrl = `http://localhost/projectRentRoom/${room.image_url}`;

        return `
            <div class="room-card">
                <img src="${imageUrl}" alt="${room.room_name}">
                <div class="room-details">
                    <h3>${room.room_name}</h3>
                    <p>${room.price} บาท</p>
                    <p>ชั้น: ${room.floor}</p>
                    <div class="actions">
                        <a href="room_detail.php?room_id=${room.id}">
                            <button>Book Now</button>
                        </a>
                        <a href="room_detail.php?room_id=${room.id}">
                            <button>ดูข้อมูลเพิ่มเติม &gt;</button>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}


        function createPagination(totalPages) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            const prevButton = document.createElement('button');
            prevButton.innerText = '<<';
            prevButton.disabled = currentPage === 1;
            prevButton.addEventListener('click', () => changePage(currentPage - 1));
            pagination.appendChild(prevButton);

            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.innerText = i;
                button.classList.toggle('active', i === currentPage);
                button.addEventListener('click', () => changePage(i));
                pagination.appendChild(button);
            }

            const nextButton = document.createElement('button');
            nextButton.innerText = '>>';
            nextButton.disabled = currentPage === totalPages;
            nextButton.addEventListener('click', () => changePage(currentPage + 1));
            pagination.appendChild(nextButton);
        }

        function changePage(pageNumber) {
            const floor = document.getElementById("floor-filter").value;
            window.location.href = `rooms.php?floor=${floor}&page=${pageNumber}`;
        }

        document.getElementById("floor-filter").addEventListener("change", function() {
            const selectedFloor = this.value;
            window.location.href = `rooms.php?floor=${selectedFloor}&page=1`; // รีโหลดหน้าเพื่อโหลดข้อมูลชั้นใหม่
        });

        // โหลดข้อมูลห้องเมื่อเปิดหน้า
        displayRooms(roomsData);
        createPagination(totalPages);
    </script>
</body>
</html>
