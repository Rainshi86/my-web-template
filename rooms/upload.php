<?php
// เชื่อมต่อกับฐานข้อมูล
include '../connect.php';

if (isset($_POST['submit'])) {
    // ตรวจสอบว่ามีไฟล์ที่เลือก
    if ($_FILES['roomImage']['error'] == 0) {
        $targetDir = "uploads/"; // โฟลเดอร์ที่จะเก็บไฟล์
        $fileName = basename($_FILES['roomImage']['name']);
        $targetFile = $targetDir . $fileName;

        // ตรวจสอบประเภทของไฟล์ (เช่น .jpg, .png)
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            // อัปโหลดไฟล์
            if (move_uploaded_file($_FILES['roomImage']['tmp_name'], $targetFile)) {
                // บันทึก URL ของไฟล์ในฐานข้อมูล
                $imageURL = $targetFile;
                $sql = "INSERT INTO rooms (room_name, price, room_type, image_url) 
                        VALUES ('Deluxe Room', 1000, 'Deluxe', '$imageURL')";
                if ($conn->query($sql) === TRUE) {
                    echo "อัปโหลดและบันทึกข้อมูลสำเร็จ!";
                } else {
                    echo "เกิดข้อผิดพลาด: " . $conn->error;
                }
            } else {    
                echo "ไม่สามารถอัปโหลดไฟล์ได้.";
            }
        } else {
            echo "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ.";
        }
    } else {
        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
    }
}

$conn->close();
?>
