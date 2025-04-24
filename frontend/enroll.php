<?php
session_start();
$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    echo "<script>alert('You need to log in first!'); window.location.href='login.html';</script>";
    exit;
}

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    echo "<script>alert('Invalid course selection!'); window.location.href='courses.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['username']; // Assuming username is the email
$course_id = $_GET['course_id'];

// Fetch course details
$sql = "SELECT title FROM courses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
    $course_name = $course['title'];
} else {
    echo "<script>alert('Course not found!'); window.location.href='courses.php';</script>";
    exit;
}
$stmt->close();

// Create enrollments table if not exists
$sql = "CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    course_id INT NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Check if user is already enrolled
$sql = "SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script> window.location.href='watch_course.php?course_id=$course_id';</script>";
    exit;
}
$stmt->close();

// Insert enrollment record
$sql = "INSERT INTO enrollments (user_id, user_email, course_id, course_name) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $user_id, $user_email, $course_id, $course_name);
$stmt->execute();

// Redirect to the course page
echo "<script> window.location.href='watch_course.php?course_id=$course_id';</script>";
exit;
?>
