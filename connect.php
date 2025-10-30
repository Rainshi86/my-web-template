<?php
// ตั้งค่าการเชื่อมต่อ MySQL
$servername = "localhost";
$usernameserver = "root";
$password = "";
$dbname = "rentroomdb";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $usernameserver, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// // ตัวอย่างคำสั่ง SQL สำหรับการเพิ่มข้อมูล
// $sql = "INSERT INTO users (user, password) 
//         VALUES ('user', 'password')";

// // ตรวจสอบการเพิ่มข้อมูล
// if ($conn->query($sql) === TRUE) {
//     echo "New record created successfully";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }

// ปิดการเชื่อมต่อ
// $conn->close();
?>
