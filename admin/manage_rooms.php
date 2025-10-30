<?php
require_once '../db.php';

session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// ตั้งค่าจำนวนห้องต่อหน้า
$roomsPerPage = 5;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$floor = isset($_GET['floor']) ? (int)$_GET['floor'] : 1;
$startLimit = max(0, ($currentPage - 1) * $roomsPerPage);

// ดึงข้อมูลห้องตามชั้น
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE floor = ? LIMIT $startLimit, $roomsPerPage");
$stmt->execute([$floor]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // ดึงจำนวนห้องทั้งหมดของชั้น
$sqlTotal = "SELECT COUNT(*) as total FROM rooms WHERE floor = ?";
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute([$floor]);
$totalRooms = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRooms / $roomsPerPage);
echo "<script>var roomsData = " . json_encode($rooms) . "; var totalPages = $totalPages; var currentPage = $currentPage; var selectedFloor = $floor;</script>";

// ดึงรายการชั้นที่มีอยู่ในฐานข้อมูล
$stmt = $pdo->query("SELECT DISTINCT floor FROM rooms ORDER BY floor ASC");
$floorList = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ตรวจสอบค่าชั้นที่ถูกเลือก
$selected_floor = isset($_GET['floor']) && $_GET['floor'] !== '' ? $_GET['floor'] : null;

// ดึงรายการห้องพักตามชั้นที่เลือก (หรือทั้งหมดถ้าไม่ได้เลือก)
if ($selected_floor) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE floor = ?");
    $stmt->execute([$selected_floor]);
} else {
    $stmt = $pdo->query("SELECT * FROM rooms");
}
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $room_name = $_POST['room_name'] ?? '';
        $price = $_POST['price'] ?? '';
        $status = $_POST['status'] ?? '';
        $room_size = $_POST['room_size'] ?? '';
        $bed_count = $_POST['bed_count'] ?? '';
        $restroom_count = $_POST['restroom_count'] ?? '';
        $floor = $_POST['floor'] ?? '';
        $facilities = $_POST['facilities'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($room_name) || empty($price) || empty($status)) {
            echo "กรุณากรอกข้อมูลให้ครบถ้วน";
            exit();
        }

        // อัปโหลดภาพ
        $image_url = '';
        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
            $targetDir = "../rooms/img/room/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $imageName = basename($_FILES['room_image']['name']);
            $imagePath = $targetDir . $imageName;
            $imageType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['room_image']['tmp_name'], $imagePath)) {
                    $image_url = "/rooms/img/room/" . $imageName;
                } else {
                    echo "เกิดข้อผิดพลาดในการอัปโหลดภาพ.";
                    exit();
                }
            } else {
                echo "ประเภทไฟล์ไม่ถูกต้อง อนุญาตให้เฉพาะ JPG, PNG และ GIF เท่านั้น.";
                exit();
            }
        } else {
            echo "กรุณาเลือกภาพสำหรับห้อง";
            exit();
        }

        // เพิ่มข้อมูลห้อง
        $stmt = $pdo->prepare("INSERT INTO rooms (room_name, price, status, image_url, room_size, bed_count, restroom_count, floor, facilities, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$room_name, $price, $status, $image_url, $room_size, $bed_count, $restroom_count, $floor, $facilities, $description]);

        echo "การเพิ่มห้องสำเร็จ!";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ✅ เพิ่มการแก้ไขห้อง (UPDATE)
        if (isset($_POST['edit'])) {
            $id = $_POST['id']; // รับค่า ID ของห้อง
            $room_name = $_POST['room_name'];
            $price = $_POST['price'];
            $status = $_POST['status'];
            $room_size = $_POST['room_size'];
            $bed_count = $_POST['bed_count'];
            $restroom_count = $_POST['restroom_count'];
            $floor = $_POST['floor'];
            $facilities = $_POST['facilities'];
            $description = $_POST['description'];
    
            // ✅ ตรวจสอบว่ามีการอัปโหลดรูปใหม่หรือไม่
            if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == 0) {
                $targetDir = "../rooms/img/room/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
    
                $imageName = basename($_FILES['room_image']['name']);
                $imagePath = $targetDir . $imageName;
                $imageType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['room_image']['tmp_name'], $imagePath)) {
                        $image_url = "/rooms/img/room/" . $imageName;
    
                        // ✅ อัปเดตข้อมูลรวมถึงรูปภาพ
                        $stmt = $pdo->prepare("UPDATE rooms SET room_name=?, price=?, status=?, image_url=?, room_size=?, bed_count=?, restroom_count=?, floor=?, facilities=?, description=?, updated_at=NOW() WHERE room_id=?");
                        $stmt->execute([$room_name, $price, $status, $image_url, $room_size, $bed_count, $restroom_count, $floor, $facilities, $description, $id]);
                    }
                }
            } else {
                // ✅ อัปเดตข้อมูลโดยไม่เปลี่ยนรูปภาพ
                $stmt = $pdo->prepare("UPDATE rooms SET room_name=?, price=?, status=?, room_size=?, bed_count=?, restroom_count=?, floor=?, facilities=?, description=?, updated_at=NOW() WHERE room_id=?");
                $stmt->execute([$room_name, $price, $status, $room_size, $bed_count, $restroom_count, $floor, $facilities, $description, $id]);
            }
    
            // echo "✅ อัปเดตข้อมูลสำเร็จ!";
        }
    }    
    elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->execute([$id]);

        echo "ลบห้องสำเร็จ!";
    }
}

?>

    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>การจัดการห้อง</title>
        <link rel="stylesheet" href="manage_rooms.css"> 
    </head>
    <body>
        <div class="container">
            <h1>การจัดการห้อง</h1>

        <!-- Add Room Form -->
        <form method="POST" class="form-container" enctype="multipart/form-data">
                <!-- Input for floor on a new line -->
                <label for="floor">ชั้น</label>   
        <select name="floor" id="floor" required onchange="updateRoomOptions()">
            <option value="">-- เลือกชั้น --</option>
            <option value="1">ชั้น 1</option>
            <option value="2">ชั้น 2</option>
            <option value="3">ชั้น 3</option>
            <option value="4">ชั้น 4</option>
            <option value="5">ชั้น 5</option>
        </select>

        <label for="room_name">หมายเลขห้อง</label>
        <select name="room_name" id="room_name" required>
            <option value="">-- เลือกหมายเลขห้อง --</option>
        </select>


        <input type="number" name="price" placeholder="ราคา" required>
        <input type="number" name="room_size" placeholder="ขนาดห้อง (ตร.ม.)" required>
        <input type="number" name="bed_count" placeholder="จำนวนเตียง" required>
        <input type="number" name="restroom_count" placeholder="จำนวนห้องน้ำ" required>

        <input type="text" name="facilities" placeholder="สิ่งอำนวยความสะดวก" required>
        <textarea name="description" placeholder="คำอธิบาย" required></textarea>

        <!-- Room Image Upload on a new line -->
        <input type="file" name="room_image" accept="image/*" required>

        <!-- Add Room Button on a new line -->
        <button type="submit" name="add">เพิ่มห้อง</button>

        <!-- Home Link on a new line -->
        <a class="home" href="../index.php">หน้าหลัก</a>
    </form>

            <!-- ฟอร์มเลือกชั้น -->
    <form method="GET">
        <label for="floor">เลือกชั้น:</label>
        <select name="floor" id="floor" onchange="this.form.submit()">
            <option value="">-- ทั้งหมด --</option>
            <?php foreach ($floorList as $floor): ?>
                <option value="<?= $floor ?>" <?= ($selected_floor == $floor) ? 'selected' : '' ?>>
                    ชั้น <?= $floor ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

        <!-- ตารางแสดงห้อง -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อห้อง</th>
                    <th>ราคา</th>
                    <th>สถานะ</th>
                    <th>ขนาดห้อง</th>
                    <th>ชั้น</th>
                    <th>สิ่งอำนวยความสะดวก</th>
                    <th>ภาพ</th>
                    <th>วันที่สร้าง</th>
                    <th>วันที่แก้ไข</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_id']) ?></td>
                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                    <td><?= htmlspecialchars($room['price']) ?> บาท</td>    
                    <td><?= htmlspecialchars($room['status']) ?></td>
                    <td><?= htmlspecialchars($room['room_size']) ?> ตร.ม.</td>
                    <td><?= htmlspecialchars($room['floor']) ?></td>
                    <td><?= htmlspecialchars($room['facilities']) ?></td>
                    <td>
                    <?php if (!empty($room['image_url'])): ?>
                        <img src="http://localhost/projectRentRoom/<?= htmlspecialchars($room['image_url']) ?>" alt="Room Image" width="100">
                    <?php else: ?>
                        ไม่มีภาพ
                    <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($room['created_at']) ?></td>
                    <td><?= htmlspecialchars($room['updated_at']) ?></td>
                        <td>
                            <!-- Edit Form -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $room['room_id'] ?>">
                                <input type="text" name="room_name" value="<?= htmlspecialchars($room['room_name']) ?>" required>
                                <input type="number" name="price" value="<?= htmlspecialchars($room['price']) ?>" required>
                                <select name="status" required>
                                    <option value="available" <?= $room['status'] == 'available' ? 'selected' : '' ?>>ว่าง</option>
                                    <option value="booked" <?= $room['status'] == 'booked' ? 'selected' : '' ?>>จองแล้ว</option>
                                    <option value="maintenance" <?= $room['status'] == 'maintenance' ? 'selected' : '' ?>>ซ่อมแซม</option>
                                </select>
                                <input type="number" name="room_size" value="<?= htmlspecialchars($room['room_size']) ?>" required>
                                <input type="number" name="bed_count" value="<?= htmlspecialchars($room['bed_count']) ?>" required>
                                <input type="number" name="restroom_count" value="<?= htmlspecialchars($room['restroom_count']) ?>" required>
                                
                                <!-- Input for floor -->
                                <input type="number" name="floor" value="<?= htmlspecialchars($room['floor']) ?>" required>

                                <input type="text" name="facilities" value="<?= htmlspecialchars($room['facilities']) ?>" required>
                                <textarea name="description" required><?= htmlspecialchars($room['description']) ?></textarea>
                                
                                <!-- Room Image Upload -->
                                <input type="file" name="room_image" accept="image/*">
                                <button type="submit" name="edit">แก้ไข</button>
                            </form>

                            <!-- Delete Form -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $room['room_id'] ?>">
                                <button type="submit" name="delete" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบห้องนี้?')">ลบ</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>        
                </tbody>
            </table>
           
        </div>        
    </body>
    <script>
        function updateRoomOptions() {
    let floor = document.getElementById("floor").value;
    let roomSelect = document.getElementById("room_name");

    // ล้างค่าเก่า
    roomSelect.innerHTML = '<option value="">-- เลือกหมายเลขห้อง --</option>';

    if (floor) {
        fetch(`get_rooms.php?floor=${floor}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(room => {
                    let option = document.createElement("option");
                    option.value = room.room_id; // ใช้ room_id เป็นค่า
                    option.textContent = room.room_name; // แสดงชื่อห้อง
                    roomSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching rooms:", error));
    }
}
    </script>
    </html> 

