<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="c_rooms_styles.css">
    <?php

        // เชื่อมต่อฐานข้อมูล
        require_once '../db.php'; // เชื่อมต่อกับไฟล์ฐานข้อมูล

        session_start();

        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'customer') {
            header('Location: ../index.php');
            exit();
        }

        $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : $_SESSION['user'];

        $roomsPerPage = 6; // จำนวนห้องที่จะแสดงต่อหน้า
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // ถ้าผู้ใช้ไม่ได้ระบุหน้าจะเป็นหน้าที่ 1

        // คำนวณขอบเขตการดึงข้อมูล
        $startLimit = ($currentPage - 1) * $roomsPerPage;

        // เชื่อมต่อกับฐานข้อมูล
        include '../connect.php';

        // ดึงข้อมูลห้องพักจากฐานข้อมูลพร้อมการแบ่งหน้า
        $sql = "SELECT * FROM rooms LIMIT $startLimit, $roomsPerPage";
        $result = $conn->query($sql);

        // สร้างอาเรย์เก็บข้อมูลห้องพัก
        $rooms = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rooms[] = [
                    'id' => $row["room_id"],
                    'room_name' => $row["room_name"],
                    // 'description' => $row["description"],
                    // 'image_url' => $row["image_url"],
                    'price' => $row["price"],
                    'type' => $row["room_type"]
                ];
            }
        }

        // การส่งข้อมูลไปยัง JavaScript
        echo "<script>var roomsData = " . json_encode($rooms) . ";</script>";

        // การสร้างปุ่มแบ่งหน้า
        $sqlTotal = "SELECT COUNT(*) as total FROM rooms";
        $totalResult = $conn->query($sqlTotal);
        $totalRooms = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRooms / $roomsPerPage);

    
        $conn->close();
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
            <a href="../index.php">หน้าแรก</a>
            <a href="./rooms.php">ห้องพัก</a>
            <a href="#">เกี่ยวกับเรา</a>
            <a href="#">ติดต่อเรา</a>
        </div>
    </div>

    <!-- รูปภาพห้องพัก -->
    <div class="room-image">
        <img src="https://blog.canadianloghomes.com/wp-content/uploads/2022/02/modern-farmhouse-bedroom-ideas-3.jpg" alt="ห้องพัก">
    </div>

    <div class="container">
        <h1>Room List with Filter</h1>

        <!-- ส่วนของฟิลเตอร์ -->
        <div class="filters">
            <select id="room-type-filter">
                <option value="">ทั้งหมด</option>
                <option value="Deluxe Room">Deluxe Room</option>
                <option value="Standard Room">Standard Room</option>
                <option value="Luxury Suite">Luxury Suite</option>
                <option value="Family Room">Family Room</option>
            </select>

            <select id="price-sort">
                <option value="">การเรียงลำดับเริ่มต้น</option>
                <option value="asc">จากต่ำไปสูง</option>
                <option value="desc">จากสูงไปต่ำ</option>
            </select>
        </div>

        <!-- ส่วนแสดงห้องพัก -->
        <div class="room-grid" id="room-grid">
            <!-- ห้องพักจะแสดงที่นี่ตามการกรอง -->        
        </div>
    </div>

    <!-- ส่วนของการแบ่งหน้า -->
    <div class="pagination" id="pagination">
        <!-- ปุ่มแบ่งหน้าจะแสดงที่นี่ -->
    </div>

    <footer class="footer">
        <p>&copy; 2024 ระบบจองห้องพัก. สงวนลิขสิทธิ์.</p>
    </footer>

    <script>
        let currentPage = 1;
        const roomsPerPage = 6;

        // ฟังก์ชันสำหรับแสดงห้องพัก
        function displayRooms(rooms) {
                const roomGrid = document.getElementById('room-grid');
                roomGrid.innerHTML = rooms.map(room => `
                    <div class="room-card">
                        <img src="${room.image_url}" alt="${room.room_name}"> 
                        <img src="https://hotel-booking.ninenic.com/wp-content/uploads/2022/08/room-6.jpg" alt=""> 
                        <div class="room-details">
                            <h3>${room.room_name}</h3> 
                            <p>${room.type}</p> 
                            <p>${room.price}</p>

                            <div class="actions">
                                <!-- ลิงก์ไปยังหน้ารายละเอียดห้อง -->
                                <a href="../rooms/room_detail.php?room_id=${room.id}">
                                    <button>Book Now</button>
                                </a>

                                <a href="../rooms/room_detail.php?room_id=${room.id}">
                                    <button>ดูข้อมูลเพิ่มเติม &gt;</button>
                                </a>
                            </div>

                        </div>
                    </div>
                `).join('');
            }


        // ฟังก์ชันสำหรับสร้างปุ่มแบ่งหน้า
        function createPagination(totalRooms) {
            const pagination = document.getElementById('pagination');
            const totalPages = Math.ceil(totalRooms / roomsPerPage);
            pagination.innerHTML = '';

            // ปุ่มไปหน้าก่อนสุดท้าย
            const firstButton = document.createElement('button');
            firstButton.innerText = '<<';
            firstButton.addEventListener('click', () => goToPage(1));
            pagination.appendChild(firstButton);

            // ปุ่มสำหรับแต่ละหน้า
            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.innerText = i;
                button.classList.toggle('active', i === currentPage);
                button.addEventListener('click', () => goToPage(i));
                pagination.appendChild(button);
            }

            // ปุ่มไปหน้าสุดท้าย
            const lastButton = document.createElement('button');
            lastButton.innerText = '>>';
            lastButton.addEventListener('click', () => goToPage(totalPages));
            pagination.appendChild(lastButton);

            // แสดงข้อมูลหน้าปัจจุบันและหน้าทั้งหมด
            const pageInfo = document.createElement('span');
            pageInfo.innerText = `หน้า ${currentPage} จาก ${totalPages}`;
            pagination.appendChild(pageInfo);
        }

        // ฟังก์ชันสำหรับเปลี่ยนหน้า
        function goToPage(pageNumber) {
            currentPage = pageNumber;
            const startIndex = (currentPage - 1) * roomsPerPage;
            const endIndex = startIndex + roomsPerPage;
            displayRooms(roomsData.slice(startIndex, endIndex));
            createPagination(roomsData.length);
        }

        // ฟังก์ชันสำหรับกรองห้องพัก
        function filterRooms() {
            const typeFilter = document.getElementById('room-type-filter').value;
            const priceSort = document.getElementById('price-sort').value;

            let filteredRooms = roomsData;

            if (typeFilter) {
                filteredRooms = filteredRooms.filter(room => room.type === typeFilter);
            }

            if (priceSort === 'asc') {
                filteredRooms.sort((a, b) => a.price - b.price);
            } else if (priceSort === 'desc') {
                filteredRooms.sort((a, b) => b.price - a.price);
            }

            displayRooms(filteredRooms.slice((currentPage - 1) * roomsPerPage, currentPage * roomsPerPage));
            createPagination(filteredRooms.length);
        }

        // เพิ่ม event listeners สำหรับฟิลเตอร์
        document.getElementById('room-type-filter').addEventListener('change', filterRooms);
        document.getElementById('price-sort').addEventListener('change', filterRooms);

        // แสดงห้องพักและแบ่งหน้าเริ่มต้น
        filterRooms();
    </script>
</body>
</html>
