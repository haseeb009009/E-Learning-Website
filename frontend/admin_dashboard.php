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
    <meta charset="utf-8">
    <title>Admin Dashboard - CourseCraft</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/iconn.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

</head>

<body>
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Admin Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <!--tables  -->

    <div class="container py-5">
        <h4 class="mt-4">👥 Users</h4>
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php while ($u = $users->fetch_assoc()): ?>
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
            <?php while ($p = $payments->fetch_assoc()): ?>
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
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Instructor</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
            <?php while ($c = $courses->fetch_assoc()): ?>
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
    <!--tables  -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-3">Quick Link</h4>
                    <p><a class="text-light" href="about.php">About Us</a></p>
                    <p><a class="text-light" href="contact.php">Contact Us</a></p>
                    <p><a class="text-light" href="">Privacy Policy</a></p>
                    <p><a class="text-light" href="">Terms & Condition</a></p>
                    <p><a class="text-light" href="">FAQs & Help</a></p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, karachi</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+923085791717</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>CourseCraft@gmail.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h4 class="text-white mb-3">Subscribe to our Newsletter</h4>
                    <p>Subscribe now and join our growing community of learners committed to lifelong education! </p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <form action="#">
                            <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="email"
                                placeholder="Your email" required>
                            <button type="submit"
                                class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2"><a
                                    href="mailto:keertidvcorai@gmail.com">Subscribe</a></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="index.php">CourseCraft</a>, All Right Reserved.

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>