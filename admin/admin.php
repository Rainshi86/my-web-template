<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        .menu { display: flex; justify-content: center; gap: 20px; }
        .menu a {
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .menu a:hover { background-color: #0056b3; }
    </style>
    <?php

        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../index.php');
            exit();
        }
    ?>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <div class="menu">
        <a href="manage_users.php">Manage users</a>
        <a href="manage_rooms.php">Manage rooms</a>
        <a href="update_status.php">Update status</a>
        <a href="booking_list.php">Booking list</a> 
    </div>
</body>
</html>
