<?php
include '../db.php';

$amphure_id = $_GET['amphure_id'] ?? '';

if ($amphure_id) {
    $stmt = $pdo->prepare("SELECT id, name_th FROM districts WHERE amphure_id = :amphure_id");
    $stmt->bindParam(':amphure_id', $amphure_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>
