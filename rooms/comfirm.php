<?php
session_start();
include '../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ฟังก์ชันแปลงวันที่และเวลา
function formatDateTime($dateString) {
    $date = DateTime::createFromFormat('Y-m-d\TH:i', $dateString); // ใช้ 'Y-m-d\TH:i' เพื่อรองรับวันที่และเวลา
    return $date ? $date->format('Y-m-d H:i:s') : null; // แปลงเป็นรูปแบบ 'Y-m-d H:i:s'
}



// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user'])) {
    die("คุณต้องเข้าสู่ระบบก่อน!");
}

// ตรวจสอบว่ามี user_id หรือไม่
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("❌ ไม่พบ user_id ในระบบ!");
}

// ตรวจสอบว่ามีการส่งข้อมูลมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ DEBUG: แสดงค่าที่ได้รับจากฟอร์ม
    echo "<h3>🔹 ค่าที่รับจากฟอร์ม:</h3><pre>";
    print_r($_POST);
    echo "</pre>";

    // รับค่าจากฟอร์ม
$room_id = $_POST['room_id'] ?? null;
$room_name = $_POST['room_name'] ?? '';
$checkIn = isset($_POST['check_in']) ? formatDateTime($_POST['check_in']) : null;
$checkOut = isset($_POST['check_out']) && !empty($_POST['check_out']) ? formatDateTime($_POST['check_out']) : null;
$guests = $_POST['guests'] ?? '';
$firstName = trim($_POST['firstname'] ?? '');
$lastName = trim($_POST['lastname'] ?? '');
$address = trim($_POST['address'] ?? '');
$province = trim($_POST['province_name'] ?? ''); // ใช้ชื่อจังหวัดที่ส่งมา
$amphure = trim($_POST['amphure_name'] ?? '');   // ใช้ชื่ออำเภอที่ส่งมา
$district = trim($_POST['district_name'] ?? '');  // ใช้ชื่อตำบลที่ส่งมา
$postalCode = trim($_POST['postal_code'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$details = trim($_POST['details'] ?? '');


// รับค่าที่เกี่ยวข้องกับการจองห้องพัก
$pricePerMonth = $_POST['price_per_month'] ?? 0; // ราคาห้องต่อเดือน
$months = $_POST['months'] ?? 1; // รับจำนวนเดือนที่ผู้ใช้งานกรอก
$deposit = $_POST['deposit'] ?? 0; // เงินประกัน
$advancePayment = $_POST['advance_payment'] ?? 0; // การจ่ายล่วงหน้า

// คำนวณเงินมัดจำ (1 เดือน)
$deposit = $pricePerMonth * 1;  // ค่าเงินมัดจำคำนวณเสมอ
// $advancePayment = 0;  // กำหนดค่าจ่ายล่วงหน้าเริ่มต้นเป็น 0

// // คำนวณจ่ายล่วงหน้า (1 เดือน) ถ้าจองมากกว่า 1 เดือน
// if ($months > 1) {
//     $advancePayment = $pricePerMonth; // ค่าจ่ายล่วงหน้า = 1 เดือน
// } else {
//     $advancePayment = 0; // ถ้าจองแค่ 1 เดือนไม่คิดค่าจ่ายล่วงหน้า
// }

$advancePayment = $pricePerMonth;

// คำนวณราคารวม
if ($months == 1) {
    // ถ้าจองแค่ 1 เดือน คำนวณจากเงินประกันและค่าจ่ายล่วงหน้า
    $total_price = $deposit + $advancePayment;
} else {
    // ถ้าจองมากกว่า 1 เดือน คำนวณจากราคาและเงินประกัน
    $calculatedTotalPrice = $pricePerMonth * $months; // ราคารวม = ราคาห้อง * จำนวนเดือน
    $total_price = $calculatedTotalPrice + $deposit + $advancePayment; // ยอดรวมทั้งหมด
}

// แสดงค่าต่างๆ เพื่อการดีบัก
echo "Price per month: " . $pricePerMonth . "<br>";
echo "Months: " . $months . "<br>";
echo "Deposit: " . $deposit . "<br>";
echo "Advance Payment: " . $advancePayment . "<br>";
echo "Calculated Total Price: " . $calculatedTotalPrice . "<br>";
echo "Total Price: " . $total_price . "<br>";

// ตรวจสอบค่าข้อมูลที่จำเป็น
if (empty($room_id) || empty($firstName) || empty($lastName) || empty($address) || empty($phone) || empty($email)) {
    die("❌ ข้อมูลผู้ใช้ไม่ครบถ้วน กรุณากรอกให้ครบ!");
}

// ตรวจสอบค่าข้อมูลที่จำเป็น
if (empty($province) || empty($amphure) || empty($district)) {
    die("❌ กรุณาเลือกจังหวัด, อำเภอ, และตำบล!");
}

if (!$checkIn || !$checkOut) {
    die("❌ กรุณากรอกวันที่เช็คอินและเช็คเอาท์ให้ถูกต้อง!");
}



    // ✅ DEBUG: แสดงค่าหลังจากแปลงวันที่
    echo "<h3>✅ แปลงวันที่สำเร็จ:</h3>";
    var_dump($checkIn, $checkOut);

    // ตรวจสอบและอัปโหลดไฟล์ (ถ้ามี)
    $targetFilePath = null;
    // ตรวจสอบการอัปโหลดไฟล์
if (!empty($_FILES["payment_slip"]["name"])) {
    // กำหนดโฟลเดอร์สำหรับเก็บไฟล์
    $uploadDir = "uploads/"; // โฟลเดอร์ที่เก็บไฟล์
    $fileName = uniqid() . "_" . basename($_FILES["payment_slip"]["name"]); // สร้างชื่อไฟล์ใหม่
    $targetFilePath = $uploadDir . $fileName; // ตั้งค่า path ที่จะเก็บไฟล์
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION)); // ตรวจสอบประเภทไฟล์

    // ตรวจสอบประเภทไฟล์ที่อนุญาต
    $allowedTypes = ["jpg", "jpeg", "png"];
    if (in_array($fileType, $allowedTypes)) {
        // อัปโหลดไฟล์
        if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $targetFilePath)) {
            // บันทึก path ของไฟล์ลงในฐานข้อมูล
            $filePath = $targetFilePath; // path ของไฟล์ที่จะเก็บใน DB

            // เรียกใช้คำสั่ง SQL เพื่อบันทึก path ของไฟล์ในฐานข้อมูล
            $stmt = $pdo->prepare("UPDATE bookings SET payment_slip = ? WHERE booking_id = ?");
            $stmt->execute([$filePath, $bookingId]); // $bookingId คือ booking_id ที่คุณต้องการอัปเดต
        } else {
            echo "❌ การอัปโหลดไฟล์ล้มเหลว!";
        }
    } else {
        echo "❌ อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น!";
    }
}


    // ✅ บันทึกข้อมูลลงฐานข้อมูล
    try {
        // บันทึกข้อมูลลงในฐานข้อมูล
        $stmt = $pdo->prepare("INSERT INTO bookings (
            room_id, room_name, check_in, check_out, guests, firstname, lastname, address,
            sub_district, district, province, postal_code, phone, email, details, total_price, 
            payment_status, user_id, payment_slip, created_at, updated_at, months, price_per_month, deposit, advance_payment
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW(), NOW(), ?, ?, ?, ?)");
    
        $stmt->execute([
            $room_id, $room_name, $checkIn, $checkOut,
            $guests, $firstName, $lastName, $address, $district, $amphure, $province,   
            $postalCode, $phone, $email, $details, $total_price, $user_id, $targetFilePath,
            $months, $pricePerMonth, $deposit, $advancePayment
        ]);
        
            // ตั้งค่าข้อความแจ้งเตือนใน session
            $_SESSION['booking_success_message'] = "ทำการจองเสร็จแล้ว รอดำเนินการต่อ";

            // เปลี่ยนเส้นทางไปยังหน้า index.php
            header("Location: ../index.php");
            exit();
    } catch (PDOException $e) {
        die("❌ ข้อผิดพลาด: " . $e->getMessage());
    }
}
?>
