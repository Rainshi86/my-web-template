<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";  // ชื่อผู้ใช้ฐานข้อมูล
$password = "";      // รหัสผ่านฐานข้อมูล
$dbname = "rentroomdb";  // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $name = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    // ตรวจสอบให้แน่ใจว่าค่าที่กรอกมาไม่ว่าง
    if (empty($name) || empty($password) || empty($confirmpassword) || empty($email)) {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน.";
    } else {
        // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสตรงกัน
        if ($password !== $confirmpassword) {
            echo "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน.";
        } else {
            // ตรวจสอบว่า username หรือ email ซ้ำ
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
            $stmt->bind_result($exists);
            $stmt->fetch();
            $stmt->close(); // ปิด statement หลังจากดึงข้อมูลเสร็จ

            // ถ้า username หรือ email ซ้ำ
            if ($exists > 0) {
                echo "ชื่อผู้ใช้งาน หรือ อีเมลล์นี้ถูกใช้งานแล้ว. โปรดลองใหม่.";
            } else {
                // แฮชรหัสผ่าน
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // เตรียมคำสั่ง SQL สำหรับการเพิ่มข้อมูล
                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $name, $email, $hashed_password);

                // ดำเนินการและตรวจสอบผลลัพธ์
                if ($stmt->execute()) {
                    echo "สมัครสมาชิกสำเร็จ!";
                    header('Location: index.php'); // ไปยังหน้าหลักหลังจากสมัคร
                    exit();
                } else {
                    echo "เกิดข้อผิดพลาด: " . $stmt->error;
                }
                $stmt->close(); // ปิด statement
            }
        }
    }
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
