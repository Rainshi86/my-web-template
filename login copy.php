<?php
include 'connect.php';  // การเชื่อมต่อฐานข้อมูล
session_start(); // เริ่มต้นเซสชัน

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มล็อกอิน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าข้อมูลไม่ว่าง
    if (empty($username) || empty($password)) {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน.";
    } else {
        // ค้นหาผู้ใช้ในฐานข้อมูล
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username); // ผูกค่าชื่อผู้ใช้
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // ตรวจสอบรหัสผ่าน (กรณีที่รหัสผ่านถูกเก็บในฐานข้อมูลแบบ hash)
            if (password_verify($password, $row['password'])) {
                // ตั้งค่าเซสชันเมื่อผู้ใช้ล็อกอินสำเร็จ
                $_SESSION['user_id'] = $row['user_id'];  // ตั้งค่า user_id ในเซสชัน
                $_SESSION['user'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // เปลี่ยนเส้นทางไปยังหน้าที่เหมาะสมตามบทบาทของผู้ใช้
                if ($row['role'] === 'admin') {
                    header('Location: admin_dashboard.php');  // หากเป็นแอดมิน
                } elseif ($row['role'] === 'employee') {
                    header('Location: employee_dashboard.php');  // หากเป็นพนักงาน
                } elseif ($row['role'] === 'customer') {
                    header('Location: customer_dashboard.php');  // หากเป็นลูกค้า
                }
                exit();  // หยุดการทำงานของสคริปต์หลังจากเปลี่ยนเส้นทาง
            } else {
                echo "รหัสผ่านไม่ถูกต้อง.";  // หากรหัสผ่านไม่ถูกต้อง
            }
        } else {
            echo "ไม่พบผู้ใช้นี้.";  // หากไม่พบผู้ใช้ในฐานข้อมูล
        }

        // ปิด statement
        $stmt->close();
    }
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
