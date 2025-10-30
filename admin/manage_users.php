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
        $name = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $role = $_POST['role'];

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['user_id'];
        $name = $_POST['username'];
        $email = $_POST['email'];
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, edited_at = NOW() WHERE user_id = ?");
        $stmt->execute([$name, $email, $id]);
    }
}

// Fetch users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="manage_users.css">
   
</head>
<body>
    <h1>User Management</h1>

    <!-- Add User Form -->
    <form method="POST">
        <input type="text" name="username" placeholder="username" required>
        <input type="text" name="password" placeholder="password" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="role" placeholder="role" required>
            <option value="">-- เลือกสถานะผู้ใช้งาน --</option>
            <option value="customer">customer</option>
            <option value="employee">employee</option>
            <option value="admin">admin</option>
        </select>
        <button type="submit" name="add">Add User</button>
        <a class="home" href="../index.php">Home</a>
    </form>

    <!-- User Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>firstname</th>
                <th>lastname</th>
                <th>address</th>
                <th>phone</th>
                <th>cid</th>
                <th>username</th>
                <th>password</th>
                <th>email</th>
                <th>role</th>
                <th>created_at</th>
                <th>edited_at</th>
                <th>edit_users</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['user_id']) ?></td>
                <td><?= htmlspecialchars($user['firstname']) ?></td>
                <td><?= htmlspecialchars($user['lastname']) ?></td>
                <td><?= htmlspecialchars($user['address']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td><?= htmlspecialchars($user['cid']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['password']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td><?= htmlspecialchars($user['edited_at']) ?></td>
                <td>
                    <!-- Edit Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        <button type="submit" name="edit">Edit</button> 
                    </form>
                    <!-- Delete Form -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
                        <button type="submit" name="delete" onclick="return confirm('Are you sure?')" >Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
