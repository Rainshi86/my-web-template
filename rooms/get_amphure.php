<?php
include '../db.php';

$province_id = $_GET['province_id'] ?? '';

if ($province_id) {
    $stmt = $pdo->prepare("SELECT id, name_th FROM amphures WHERE province_id = :province_id");
    $stmt->bindParam(':province_id', $province_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}






?>
