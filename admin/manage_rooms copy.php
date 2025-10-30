<?php
require_once '../db.php';

session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../index.php');
            exit();
        }

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $room_name = $_POST['room_name'];
        $room_type = $_POST['room_type'];
        $price = $_POST['price'];
        $status = $_POST['status'];

        // Insert room data
        $stmt = $pdo->prepare("INSERT INTO rooms (room_name, room_type, price, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$room_name, $room_type, $price, $status]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        // Delete room data
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $room_name = $_POST['room_name'];
        $room_type = $_POST['room_type'];
        $price = $_POST['price'];
        $status = $_POST['status'];

        // Update room data
        $stmt = $pdo->prepare("UPDATE rooms SET room_name = ?, room_type = ?, price = ?, status = ?, updated_at = NOW() WHERE room_id = ?");
        $stmt->execute([$room_name, $room_type, $price, $status, $id]);
    }
}

// Fetch rooms
$stmt = $pdo->query("SELECT * FROM rooms");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="manage_rooms.css">
</head>
<body>
    <div class="container">
        <h1>Room Management</h1>

        <!-- Add Room Form -->
        <form method="POST" class="form-container">
            <input type="text" name="room_name" placeholder="Room Name" required>
            <input type="text" name="room_type" placeholder="Room Type" required>
            <input type="number" name="price" placeholder="Price" required>
            <select name="status" required>
                <option value="">-- Select Room Status --</option>
                <option value="available">Available</option>
                <option value="booked">Booked</option>
                <option value="maintenance">Maintenance</option>
            </select>
            <button type="submit" name="add">Add Room</button>
            <a class="home" href="../index.php">Home</a>
        </form>

        <!-- Room Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Name</th>
                    <th>Room Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_id']) ?></td>
                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                    <td><?= htmlspecialchars($room['room_type']) ?></td>
                    <td><?= htmlspecialchars($room['price']) ?></td>
                    <td><?= htmlspecialchars($room['status']) ?></td>
                    <td><?= htmlspecialchars($room['created_at']) ?></td>
                    <td><?= htmlspecialchars($room['updated_at']) ?></td>
                    <td>
                        <!-- Edit Form -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $room['room_id'] ?>">
                            <input type="text" name="room_name" value="<?= htmlspecialchars($room['room_name']) ?>" required>
                            <input type="text" name="room_type" value="<?= htmlspecialchars($room['room_type']) ?>" required>
                            <input type="number" name="price" value="<?= htmlspecialchars($room['price']) ?>" required>
                            <input type="file" name="room_image" accept="image/*" required>
                            <select name="status" required>
                                <option value="available" <?= $room['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="booked" <?= $room['status'] == 'booked' ? 'selected' : '' ?>>Booked</option>
                                <option value="maintenance" <?= $room['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                            <button type="submit" name="edit">Edit</button> 
                        </form>

                        <!-- Delete Form -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $room['room_id'] ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this room?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
