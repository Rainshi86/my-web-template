<?php
include '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<p>ไม่ได้ล็อกอิน!</p>";
    exit;
}

$userId = $_SESSION['user_id'];

// ตรวจสอบสิทธิ์ของผู้ใช้
$query = "SELECT role FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p>ไม่พบข้อมูลผู้ใช้</p>";
    exit;
}

// กำหนด query สำหรับดึงข้อมูล bookings
if ($user['role'] == 'admin') {
    // Admin เห็นการจองทั้งหมด
    $query = "SELECT b.*, r.room_name, r.floor 
              FROM bookings b
              JOIN rooms r ON b.room_id = r.room_id";
    $stmt = $pdo->prepare($query);
} else {
    // User เห็นเฉพาะของตัวเอง
    $query = "SELECT b.*, r.room_name, r.floor 
              FROM bookings b
              JOIN rooms r ON b.room_id = r.room_id
              WHERE b.user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
}

$stmt->execute();
$bookings = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }
        .booking {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .payment-status {
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
            color: #fff;
        }
        .payment-status.pending { background-color: #f0ad4e; }
        .payment-status.paid { background-color: #5bc0de; }
        .payment-status.failed { background-color: #d9534f; }
        .booking-id { color: #007bff; }
        .no-bookings {
            text-align: center;
            font-size: 18px;
            color: #888;
        }
        .back-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .back-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h2>ข้อมูลการจองห้องพัก</h2>

    <?php
    if ($bookings) {
        foreach ($bookings as $booking) {
            echo "<div class='booking'>";
            echo "<p><strong>รหัสการจอง:</strong> <span class='booking-id'>" . htmlspecialchars($booking['booking_id']) . "</span></p>";
            echo "<p><strong>ชั้นที่:</strong> " . htmlspecialchars($booking['floor']) . "</p>";
            echo "<p><strong>ห้องที่:</strong> " . htmlspecialchars($booking['room_name']) . "</p>";   
            echo "<p><strong>วันที่เช็คอิน:</strong> " . htmlspecialchars($booking['check_in']) . "</p>";
            echo "<p><strong>วันที่เช็คเอาท์:</strong> " . htmlspecialchars($booking['check_out']) . "</p>";
            echo "<p><strong>ราคารวมทั้งหมด:</strong> ฿" . htmlspecialchars($booking['total_price']) . "</p>";
            
            $paymentStatus = strtolower(htmlspecialchars($booking['payment_status']));

            echo "<p><strong>สถานะการชำระเงิน:</strong> 
                    <span class='payment-status " . ($paymentStatus == 'pending' ? 'pending' : ($paymentStatus == 'paid' ? 'paid' : 'failed')) . "'>
                    " . htmlspecialchars($booking['payment_status']) . "
                    </span>
                  </p>";

            echo "</div>";

            // แจ้งเตือนเฉพาะ booking ปัจจุบัน
            if ($paymentStatus == "completed" || $paymentStatus == "processed") {
                echo "<script>
                        setTimeout(function() { 
                            alert('🎉 การตรวจสอบการชำระเงินเสร็จแล้ว! คุณสามารถเข้าพักที่ Apartment ได้เลย 🎊');
                        }, 500);
                      </script>";
            } elseif ($paymentStatus == "cancelled" || $paymentStatus == "failed") {
                echo "<script>
                        setTimeout(function() {
                            alert('❌ ทำรายการไม่สำเร็จ โปรดทำรายการใหม่');
                        }, 500);
                      </script>";
            }
        }
    } else {
        echo "<p class='no-bookings'>ไม่มีการจอง</p>";
    }
    ?>

    <a href="../index.php" class="back-btn">กลับสู่หน้าหลัก</a>
</div>

</body>
</html>
