<?php
include 'auth.php'; // restricts to logged-in users only
$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

// Get all courses
$courses = $conn->query("SELECT id, title, price FROM courses");

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $user_id = $_SESSION['user_id'];

    // Get price
    $stmt = $conn->prepare("SELECT price FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($amount);
    $stmt->fetch();
    $stmt->close();

    // Insert payment
    $insert = $conn->prepare("INSERT INTO payments (user_id, course_id, amount, payment_status) VALUES (?, ?, ?, 'pending')");
    $insert->bind_param("iid", $user_id, $course_id, $amount);
    $insert->execute();

    echo "<script>alert('Payment recorded with status: pending');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Payment Page</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Make a Test Payment</h2>
    <form method="post">
        <div class="mb-3">
            <label for="course_id" class="form-label">Select Course:</label>
            <select name="course_id" id="course_id" class="form-select" required>
                <?php while ($row = $courses->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['title'] ?> ($<?= $row['price'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Pay</button>
    </form>
</body>
</html>
