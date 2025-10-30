<?php
session_start();
include '../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$total_price = $_POST['total_price'] ?? 0;
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

// แสดงค่าของ check_in และ check_out ก่อนการบันทึก
echo "Check-in: " . $checkIn . "<br>";
echo "Check-out: " . $checkOut . "<br>";

function formatDateTime($dateString) {
    $date = DateTime::createFromFormat('Y-m-d', $dateString); // ตรวจสอบรูปแบบวันที่
    if (!$date) {
        echo "Error parsing date: " . $dateString . "<br>";
    }
    return $date ? $date->format('Y-m-d') : null;
}


// ตรวจสอบค่าข้อมูลที่จำเป็น
if (!$room_id) {    
    die("❌ ไม่พบ room_id ในข้อมูลที่ส่งมา!");
}

if (empty($firstName) || empty($lastName) || empty($address) || empty($phone) || empty($email)) {
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
    if (!empty($_FILES["payment_slip"]["name"])) {
        $uploadDir = "uploads/";
        $fileName = uniqid() . "_" . basename($_FILES["payment_slip"]["name"]);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png"];

        if (!in_array($fileType, $allowedTypes)) {
            die("❌ อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น!");
        }

        if (!move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $targetFilePath)) {
            $targetFilePath = null; // ตั้งค่า NULL หากอัปโหลดไม่สำเร็จ
        }
    }

    // ✅ บันทึกข้อมูลลงฐานข้อมูล
    try {
        // บันทึกข้อมูลลงในฐานข้อมูล
        $stmt = $pdo->prepare("INSERT INTO bookings (
            room_id, room_name, check_in, check_out, guests, firstname, lastname, address,
            sub_district, district, province, postal_code, phone, email, details, total_price, 
            payment_status, user_id, payment_slip, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW(), NOW())");
        
        $stmt->execute([
            $room_id, $room_name, $checkIn, $checkOut,
            $guests, $firstName, $lastName, $address, $district, $amphure, $province, 
            $postalCode, $phone, $email, $details, $total_price, $user_id, $targetFilePath
        ]);
        
        header("Location: confirm.php");
        exit();
    } catch (PDOException $e) {
        die("❌ ข้อผิดพลาด: " . $e->getMessage());
    }
}
?>
