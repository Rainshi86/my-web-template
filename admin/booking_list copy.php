<?php
include '../db.php';
$stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manage_rooms.css">
    <title>Document</title>
</head>
<body>
    <h1>Booking Management</h1>

    <!-- Booking Table -->
    <table border="1">
        <thead>
            <tr>
                <th>หมายเลขการจอง</th>
                <th>ห้อง</th>
                <th>ชื่อผู้จอง</th>
                <th>วันที่เช็คอิน</th>
                <th>วันที่เช็คเอาท์</th>
                <th>สถานะการชำระเงิน</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= htmlspecialchars($booking['booking_id']) ?></td>
                <td><?= htmlspecialchars($booking['room_name']) ?></td>
                <td><?= htmlspecialchars($booking['firstname'] . " " . $booking['lastname']) ?></td>
                <td><?= htmlspecialchars($booking['check_in']) ?></td>
                <td><?= htmlspecialchars($booking['check_out']) ?></td>
                <td>
                    <?php 
                        if ($booking['payment_status'] == 'pending') echo "รอดำเนินการ";
                        elseif ($booking['payment_status'] == 'processed') echo "ดำเนินการแล้ว";
                        elseif ($booking['payment_status'] == 'completed') echo "สำเร็จ";
                        elseif ($booking['payment_status'] == 'cancelled') echo "ยกเลิก";
                    ?>
                </td>
                <td>
                    <!-- Edit Form -->
                    <form action="update_status.php" method="POST" style="display: inline;">
                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                        <select name="payment_status">
                            <option value="pending" <?= $booking['payment_status'] == 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                            <option value="processed" <?= $booking['payment_status'] == 'processed' ? 'selected' : '' ?>>ดำเนินการแล้ว</option>
                            <option value="completed" <?= $booking['payment_status'] == 'completed' ? 'selected' : '' ?>>สำเร็จ</option>
                            <option value="cancelled" <?= $booking['payment_status'] == 'cancelled' ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                        <button type="submit">อัปเดต</button>
                    </form>
                    <!-- Delete Form -->
                    <form action="delete_booking.php" method="POST" style="display: inline;">
                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                        <button type="submit" name="delete" onclick="return confirm('ต้องการลบการจองนี้หรือไม่?')">ลบ</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a class="home" href="../index.php">Home</a>
</body>
</html>