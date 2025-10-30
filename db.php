<?php

$host = 'mysql.railway.internal';  // จาก Railway
$dbname = '';
$usernamedb = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usernamedb, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "เชื่อมต่อฐานข้อมูลสำเร็จ";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>

// $host = 'localhost';  // Host
// $dbname = 'rentroomdb';  // Database name
// $usernamedb = 'root';  // Database username
// $password = '';  // Database password

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $usernamedb, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     // echo "เชื่อมต่อฐานข้อมูลสำเร็จ"; // เพิ่มข้อความนี้เพื่อตรวจสอบ
// } catch (PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
//     die();
// }
