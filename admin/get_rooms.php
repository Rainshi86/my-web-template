<?php
require_once '../db.php';

if (isset($_GET['floor'])) {
    $floor = $_GET['floor'];

    // ดึงข้อมูลห้องที่อยู่ในชั้นที่เลือก
    $stmt = $pdo->prepare("SELECT room_id, room_name FROM rooms WHERE floor = ?");
    $stmt->execute([$floor]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งข้อมูล JSON กลับไป
    echo json_encode($rooms);
}
?>
