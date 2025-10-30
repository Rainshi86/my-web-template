<?php
include '../db.php';
$bookingId = $_GET['booking_id'] ?? 0;

// ดึงข้อมูลการจอง
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ?");
$stmt->bindValue(1, $bookingId);
$stmt->execute();
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    echo "ไม่พบข้อมูลการจอง";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE booking_id = ?");
    $stmt->bindValue(1, $bookingId);
    $stmt->execute();
    echo "✅ การจองได้รับการอนุมัติแล้ว!";
}
?>

<h2>ตรวจสอบการจอง</h2>
<p>ชื่อห้อง: <?php echo htmlspecialchars($booking['room_name']); ?></p>
<p>วันที่เช็คอิน: <?php echo htmlspecialchars($booking['check_in']); ?></p>
<p>วันที่เช็คเอาท์: <?php echo htmlspecialchars($booking['check_out']); ?></p>
<p>หลักฐานการโอนเงิน:</p>
<img src="../uploads/<?php echo htmlspecialchars($booking['payment_slip']); ?>" width="300">

<form method="POST">
    <button type="submit">✅ อนุมัติการจอง</button>
</form>
