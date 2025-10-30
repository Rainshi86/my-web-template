<?php
session_start();
include '../db.php';

// ดึงข้อมูลจังหวัดจากฐานข้อมูล
$stmt = $pdo->query("SELECT id, name_th FROM provinces");
$provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบสถานะการล็อกอิน
$is_logged_in = isset($_SESSION['user']);

if (!$is_logged_in) {
    // หากยังไม่ได้ล็อกอิน
    $errorMessage = "คุณต้องเข้าสู่ระบบก่อน!";
    exit(); // หยุดการทำงาน
} else {
    // หากล็อกอินแล้ว, ดึงข้อมูล user_id จากฐานข้อมูล
    $username = $_SESSION['user'];

    // ดึงข้อมูล user_id จากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT user_id, email FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // เก็บ user_id ลงใน session
        $_SESSION['user_id'] = $user['user_id'];
        $email = $user['email'] ?? '';  // หากไม่มี email ให้เป็นค่าว่าง
    } else {
        // ถ้าไม่พบผู้ใช้ ให้แสดงข้อผิดพลาด
        $errorMessage = "ไม่พบข้อมูลผู้ใช้";
        exit();
    }
}

// // Debug
// echo "<pre>";
// print_r($_POST); // หรือ print_r($_GET); ถ้าใช้ GET
// echo "</pre>";

// รับค่าจาก room_detail.php ผ่าน POST 
$roomId = $_POST['room_id'] ?? 'N/A';
$roomName = $_POST['room_name'] ?? 'N/A';
$checkIn = $_POST['check_in'] ?? '';
$checkOut = $_POST['check_out'] ?? '';
$guests = $_POST['guests'] ?? '';
$pricePerMonth = $_POST['price'] ?? 0; // ใช้ค่าราคาห้องที่ส่งมา (ราคาต่อเดือน)
$months = $_POST['months'] ?? 1; // รับจำนวนเดือนที่ผู้ใช้งานกรอก (จาก input)
// $electricityUnits = $_POST['electricity_units'] ?? 0; // จำนวนหน่วยไฟที่ใช้งาน
// $waterUnits = $_POST['water_units'] ?? 0; // จำนวนหน่วยน้ำที่ใช้งาน
$totalPrice = 0; // ค่ารวม

// ตรวจสอบวันที่เช็คอินและเช็คเอาท์
if ($checkIn && $checkOut && $months > 0) {
    // คำนวณราคาตามจำนวนเดือนที่ลูกค้าจอง
    $totalPrice = $pricePerMonth * $months;

    // คำนวณเงินประกัน (1 เดือน)
    $deposit = $pricePerMonth;

    // คำนวณจ่ายล่วงหน้า (1 เดือน)
    $advancePayment = $pricePerMonth;

    // คำนวณค่าไฟ (ขั้นต่ำ 600 บาท)
    // $electricityCost = ($electricityUnits * 8 >= 600) ? $electricityUnits * 8 : 600;

    // คำนวณค่าน้ำ (ขั้นต่ำ 150 บาท)
    // $waterCost = ($waterUnits * 16 >= 150) ? $waterUnits * 16 : 150;

    // คำนวณราคารวม
    // $totalAmount = $totalPrice + $deposit + $advancePayment + $electricityCost + $waterCost;
    $totalAmount = $deposit + $advancePayment;

    // แสดงผล
    // echo "ราคาค่าเช่า (สำหรับ " . $months . " เดือน): ฿" . number_format($totalPrice, 2) . "<br>";
    // echo "เงินประกัน: ฿" . number_format($deposit, 2) . "<br>";
    // echo "จ่ายล่วงหน้า: ฿" . number_format($advancePayment, 2) . "<br>";
    // // echo "ค่าไฟ: ฿" . number_format($electricityCost, 2) . "<br>";
    // // echo "ค่าน้ำ: ฿" . number_format($waterCost, 2) . "<br>";
    // echo "รวมทั้งหมด: ฿" . number_format($totalAmount, 2);

} else {
    echo "กรุณาระบุวันที่เช็คอิน, เช็คเอาท์ และจำนวนเดือน";
}


?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="booking.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        <!-- Payment Section -->
        <div class="header">
            <h2>Payment</h2>
        </div>

        <!-- Container -->
        <div class="container">
            <!-- Left Section: Form -->
            <div class="left-section">
                <h2>กรอกข้อมูลส่วนตัว</h2>

                <!-- แสดงข้อความแจ้งเตือนหากข้อมูลไม่ครบถ้วน -->
                <!-- <?php if ($errorMessage): ?>
                    <div style="color: red; font-weight: bold;"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?> -->

                <!-- Booking Form -->
                <form action="comfirm.php" method="POST" enctype="multipart/form-data">    
                    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($roomId); ?>">
                    <input type="hidden" name="room_name" value="<?php echo htmlspecialchars($roomName); ?>">
                    <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($checkIn); ?>">
                    <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($checkOut); ?>">
                    <input type="hidden" name="guests" value="<?php echo htmlspecialchars($guests); ?>">
                    <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($totalPrice); ?>">
                    <input type="hidden" name="price_per_month" value="<?php echo htmlspecialchars($pricePerMonth); ?>">
                    <input type="hidden" name="months" value="<?php echo htmlspecialchars($months); ?>">

                    <label for="first-name">ชื่อ</label>
                    <input type="text" id="first-name" name="firstname" value="<?php echo htmlspecialchars($firstName ?? ''); ?>" required>

                    <label for="last-name">นามสกุล</label>
                    <input type="text" id="last-name" name="lastname" value="<?php echo htmlspecialchars($lastName ?? ''); ?>" required>

                    <label for="address">ที่อยู่</label>
                    <textarea id="address" name="address" rows="2" required><?php echo htmlspecialchars($address ?? ''); ?></textarea>

                                   <!-- จังหวัด -->
    <label for="province">จังหวัด</label>
    <div class="select-container">
        <input type="text" id="province_input" name="province_name" readonly placeholder="เลือกจังหวัด">
        <select name="province" id="province">
            <option value="">เลือกจังหวัด</option>
            <?php foreach ($provinces as $province): ?>
                <option value="<?= htmlspecialchars($province['id']) ?>">
                    <?= htmlspecialchars($province['name_th']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- อำเภอ -->
    <label for="amphure">อำเภอ</label>
    <div class="select-container">
        <input type="text" id="amphure_input" name="amphure_name" readonly placeholder="เลือกอำเภอ">
        <select name="amphure" id="amphure">
            <option value="">เลือกอำเภอ</option>
        </select>
    </div>

    <!-- ตำบล -->
    <label for="district">ตำบล</label>
    <div class="select-container">
        <input type="text" id="district_input" name="district_name" readonly placeholder="เลือกตำบล">
        <select name="district" id="district">
            <option value="">เลือกตำบล</option>
        </select>
    </div>


                    <label for="postal-code">รหัสไปรษณีย์</label>
                    <input type="text" id="postal-code" name="postal_code" value="<?php echo htmlspecialchars($postalCode ?? ''); ?>" required>

                    <label for="phone">เบอร์โทร</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>

                    <label for="email">อีเมล์</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>

                    <!-- <label for="details">รายละเอียดเพิ่มเติม</label>
                    <textarea id="details" name="details" rows="4"><?php echo htmlspecialchars($details ?? ''); ?></textarea> -->

                    <label for="upload-slip">อัพโหลดหลักฐานการโอน:</label>
                    <input type="file" id="upload-slip" name="payment_slip" accept="image/*" required>

                    <button type="submit">จอง</button>
                </form>
            </div>

            <!-- Right Section: Booking Summary -->
            <div class="right-section">
                <h2>รายการที่จอง</h2>
                <p><strong>รหัสห้อง:</strong> <?php echo htmlspecialchars($roomId ?? 'N/A'); ?></p>
                <p><strong>ชื่อห้อง:</strong> <?php echo htmlspecialchars($roomName ?? 'N/A'); ?></p>
                <p><strong>วันที่เช็คอิน:</strong> <?php echo htmlspecialchars($checkIn ?? 'N/A'); ?></p>
                <p><strong>วันที่เช็คเอาท์:</strong> <?php echo htmlspecialchars($checkOut ?? 'N/A'); ?></p>
                <p><strong>จำนวนผู้เข้าพัก:</strong> <?php echo htmlspecialchars($guests ?? 'N/A'); ?> คน</p>
                <p><strong>ราคารายเดือน :</strong> ฿<?php echo htmlspecialchars($pricePerMonth ?? 0); ?></p>
                <p><strong>จำนวนเดือน:</strong> <?php echo htmlspecialchars($months ?? 0); ?> เดือน</p>
                <!-- <p><strong>ราคารวม:</strong> ฿<?php echo htmlspecialchars($totalPrice ?? 0); ?> บาท</p> -->
                <p><strong></strong><?php echo "ราคาค่าเช่า (สำหรับ " . $months . " เดือน): ฿" . number_format($totalPrice, 2) . "<br>";?></p>
                <h3>รายการที่ต้องจ่ายชำระก่อน</h3>
                <p><strong></strong><?php  echo "เงินประกัน: ฿" . number_format($deposit, 2) . "<br>";?></p>
                <p><strong></strong><?php  echo "จ่ายล่วงหน้า(1 เดือน): ฿" . number_format($advancePayment, 2) . "<br>";?></p>
                <p><strong></strong><?php  echo "ราคารวมทั้งหมด: ฿" . number_format($totalAmount, 2);?></p> 

                <p><strong>QE CODE:</strong> <br>   
                    <!-- ตรวจสอบพาธให้แน่ใจว่าไฟล์ภาพ qr.png อยู่ในที่ที่ถูกต้อง -->
                    <img src="http://localhost/projectRentroom/img/qrcode.png" alt="QR Code">
                </p>

            </div>
        </div>

    </body>
    <script>
  $(document).ready(function(){
    function setupDropdown(input, select) {
        $(input).on("click", function() {
            $(select).toggle(); // เปิด/ปิด dropdown เมื่อคลิก input
        });

        $(select).on("change", function(){
            let selectedText = $(this).find("option:selected").text();
            $(input).val(selectedText); // ตั้งค่า input เป็นชื่อที่เลือก
            $(select).hide(); // ซ่อน dropdown หลังเลือก
        });

        $(document).on("click", function(event) {
            if (!$(event.target).closest(".select-container").length) {
                $(select).hide(); // ซ่อน dropdown ถ้าคลิกนอกพื้นที่
            }
        });
    }

    setupDropdown("#province_input", "#province");
    setupDropdown("#amphure_input", "#amphure");
    setupDropdown("#district_input", "#district");

    $("#province").on("change", function(){
        let provinceName = $(this).find("option:selected").text();
        $("#province_input").val(provinceName); // บันทึกชื่อจังหวัด
        $("#amphure").html('<option value="">เลือกอำเภอ</option>');
        $("#district").html('<option value="">เลือกตำบล</option>'); // เคลียร์ตำบล

        let provinceId = $(this).val();
        if (provinceId) {
            $.get('get_amphure.php', { province_id: provinceId }, function(data){
                let result = JSON.parse(data);
                result.forEach(item => {
                    // ใช้ id เป็น value และ name_th เป็น text ของ option
                    $("#amphure").append(new Option(item.name_th, item.id)); 
                });
            });
        }
    });

    $("#amphure").on("change", function(){
        let amphureName = $(this).find("option:selected").text();
        $("#amphure_input").val(amphureName); // บันทึกชื่ออำเภอ
        $("#district").html('<option value="">เลือกตำบล</option>'); // เคลียร์ตำบล

        let amphureId = $(this).val();
        if (amphureId) {
            $.get('get_district.php', { amphure_id: amphureId }, function(data){
                let result = JSON.parse(data);
                result.forEach(item => {
                    // ใช้ id เป็น value และ name_th เป็น text ของ option
                    $("#district").append(new Option(item.name_th, item.id)); 
                });
            });
        }
    });

    $("#district").on("change", function(){
        let districtName = $(this).find("option:selected").text();
        $("#district_input").val(districtName); // บันทึกชื่อตำบล
    });

    // ✅ อัปเดตค่า `value` ของ `<select>` ก่อนส่งฟอร์ม
    $("form").on("submit", function() {
        $("#province").val($("#province_input").val());
        $("#amphure").val($("#amphure_input").val());
        $("#district").val($("#district_input").val());
    });
});

</script>

    </html>