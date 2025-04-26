<?php
$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

// Handle status update from dropdown
if (isset($_POST['update_status_id']) && isset($_POST['payment_status'])) {
    $id = $_POST['update_status_id'];
    $status = $_POST['payment_status'];

    $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php"); // Refresh after update
    exit;
}

// Delete user/course/payment
if (isset($_GET['delete_user'])) {
    $conn->query("DELETE FROM users WHERE id = " . $_GET['delete_user']);
}
if (isset($_GET['delete_course'])) {
    $conn->query("DELETE FROM courses WHERE id = " . $_GET['delete_course']);
}
if (isset($_GET['delete_payment'])) {
    $conn->query("DELETE FROM payments WHERE id = " . $_GET['delete_payment']);
}

// Fetch data
$users = $conn->query("SELECT * FROM users");
$courses = $conn->query("SELECT * FROM courses");
$payments = $conn->query("SELECT payments.*, users.username AS user_name, courses.title AS course_name 
FROM payments 
JOIN users ON payments.user_id = users.id 
JOIN courses ON payments.course_id = courses.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CourseCraft</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-4 text-primary">Admin Dashboard</h2>

        <h4 class="mt-4">👥 Users</h4>
        <table class="table table-bordered">
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Action</th></tr>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['username'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><a href="?delete_user=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h4 class="mt-5">💳 Payments</h4>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Course</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Date</th>
                <th>Action</th>
            </tr>
            <?php while($p = $payments->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['user_name'] ?></td>
                <td><?= $p['course_name'] ?></td>
                <td>$<?= $p['amount'] ?></td>
                <td>
                    <form method="post" action="admin_dashboard.php" style="display:inline-block;">
                        <input type="hidden" name="update_status_id" value="<?= $p['id'] ?>">
                        <select name="payment_status" onchange="this.form.submit()" class="form-select form-select-sm">
                            <option value="pending" <?= $p['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $p['payment_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </form>
                </td>
                <td><?= $p['payment_date'] ?></td>
                <td>
                    <a href="?delete_payment=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <h4 class="mt-5">📚 Courses</h4>
        <table class="table table-bordered">
            <tr><th>ID</th><th>Title</th><th>Instructor</th><th>Price</th><th>Action</th></tr>
            <?php while($c = $courses->fetch_assoc()): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= $c['title'] ?></td>
                <td><?= $c['instructor'] ?></td>
                <td>$<?= $c['price'] ?></td>
                <td><a href="?delete_course=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?');">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
