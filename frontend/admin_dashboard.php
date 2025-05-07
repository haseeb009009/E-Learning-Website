<?php
// connect.php
include 'admin_auth.php';


$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Handle Add/Edit/Delete Users
if (isset($_POST['save_user'])) {
    if (!empty($_POST['user_id'])) {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $_POST['username'], $_POST['email'], $_POST['password'], $_POST['user_id']);
        $stmt->execute();
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_POST['username'], $_POST['email'], $_POST['password']);
        $stmt->execute();
        header("Location: admin_dashboard.php");
    }
    exit;
}

if (isset($_GET['delete_user'])) {
    $conn->query("DELETE FROM users WHERE id=" . (int)$_GET['delete_user']);
    header("Location: admin_dashboard.php ");
    exit;
}

// Handle Add/Edit/Delete Courses
if (isset($_POST['save_course'])) {
    if (!empty($_POST['course_id'])) {
        $stmt = $conn->prepare("UPDATE courses SET title=?,  price=?, video_url=? WHERE id=?");
        $stmt->bind_param("ssdsi", $_POST['title'],  $_POST['price'], $_POST['video_url'], $_POST['course_id']);
        $stmt->execute();
        header("Location: admin_dashboard.php");
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (title, price, video_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $_POST['title'], $_POST['price'], $_POST['video_url']);
        $stmt->execute();
        header("Location: admin_dashboard.php");
    }
    exit;
}
if (isset($_GET['delete_course'])) {
    $conn->query("DELETE FROM courses WHERE id=" . (int)$_GET['delete_course']);
    header("Location: admin_dashboard.php");
    exit;
}

// Handle Payment Status Update
if (isset($_POST['update_payment'])) {
    $stmt = $conn->prepare("UPDATE payments SET payment_status=? WHERE id=?");
    $stmt->bind_param("si", $_POST['payment_status'], $_POST['payment_id']);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

if (isset($_GET['delete_payment'])) {
    $conn->query("DELETE FROM payments WHERE id=" . (int)$_GET['delete_payment']);
    header("Location: admin_dashboard.php");
    exit;
}

// Search feature
$user_search = $_GET['user_search'] ?? '';
$course_search = $_GET['course_search'] ?? '';
$payment_search = $_GET['payment_search'] ?? '';

// Fetch records with search
if ($user_search != '') {
    $users = $conn->query("SELECT * FROM users WHERE username LIKE '%$user_search%' OR email LIKE '%$user_search%' LIMIT $limit OFFSET $offset");
} else {
    $users = $conn->query("SELECT * FROM users LIMIT $limit OFFSET $offset");
}

if ($course_search != '') {
    $courses = $conn->query("SELECT * FROM courses WHERE title LIKE '%$course_search%' OR instructor LIKE '%$course_search%' LIMIT $limit OFFSET $offset");
} else {
    $courses = $conn->query("SELECT * FROM courses LIMIT $limit OFFSET $offset");
}

if ($payment_search != '') {
    $payments = $conn->query("SELECT payments.*, users.username AS user_name, courses.title AS course_name FROM payments 
    JOIN users ON payments.user_id = users.id 
    JOIN courses ON payments.course_id = courses.id
    WHERE users.username LIKE '%$payment_search%' OR courses.title LIKE '%$payment_search%'
    LIMIT $limit OFFSET $offset");
} else {
    $payments = $conn->query("SELECT payments.*, users.username AS user_name, courses.title AS course_name FROM payments 
    JOIN users ON payments.user_id = users.id 
    JOIN courses ON payments.course_id = courses.id
    LIMIT $limit OFFSET $offset");
}
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
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="img/iconn.png" alt="" height="50px">
            <div class="ms-2">
                <p class="m-0 fw-bold" style="font-size: 25px;">CourseCraft</p>
                <p class="m-0" style="font-size: 12px;">E-learning platform</p>
            </div>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="contact.php" class="nav-item nav-link"></a>
                <?php if (!isset($_SESSION['admin_id'])): ?>
                    <a href="login.html" class="nav-item nav-link"><i class="fa fa-user"></i>login</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <a href="admin_logout.php" class="nav-item nav-link">Logout</a>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <a href="profile.php" class="nav-item nav-link"></a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="#" class="nav-item nav-link">
                    <div id="google_translate_element">
                    </div>
                </a>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    <div class="container-fluid bg-primary py-3 mb-5 page-header">
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

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <!-- USERS -->
        <h4>👥 Users</h4>

        <!-- Search users -->
        <form method="get" class="mb-3">
            <input type="text" name="user_search" class="form-control form-control-sm" placeholder="Search users..." value="<?= htmlspecialchars($_GET['user_search'] ?? '') ?>">
        </form>

        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered table-sm">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($u = $users->fetch_assoc()): ?>
                        <tr>
                            <form method="post">
                                <td><?= $u['id'] ?></td>
                                <td><input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" class="form-control form-control-sm"></td>
                                <td><input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" class="form-control form-control-sm"></td>
                                <td><input type="password" name="password" placeholder="New Password" class="form-control form-control-sm"></td>
                                <td>
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" name="save_user" class="btn btn-sm btn-primary">Save</button>
                                    <a href="?delete_user=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')">Delete</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                    <tr>
                        <form method="post">
                            <td>New</td>
                            <td><input type="text" name="username" class="form-control form-control-sm" placeholder="Username" required></td>
                            <td><input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required></td>
                            <td><input type="password" name="password" class="form-control form-control-sm" placeholder="Password" required></td>
                            <td><button type="submit" name="save_user" class="btn btn-sm btn-success">Add</button></td>
                        </form>
                    </tr>
                </table>
            </div>
        </div>

        <!-- PAYMENTS -->
        <h4>💳 Payments</h4>

        <!-- Search payments -->
        <form method="get" class="mb-3">
            <input type="text" name="payment_search" class="form-control form-control-sm" placeholder="Search payments..." value="<?= htmlspecialchars($_GET['payment_search'] ?? '') ?>">
        </form>

        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered table-sm">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($p = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['user_name']) ?></td>
                            <td><?= htmlspecialchars($p['course_name']) ?></td>
                            <td>$<?= $p['amount'] ?></td>
                            <td>
                                <form method="post" style="display:flex; align-items:center; gap:5px;">
                                    <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                    <select name="payment_status" class="form-select form-select-sm">
                                        <option value="pending" <?= $p['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="completed" <?= $p['payment_status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_payment" class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                            <td><?= $p['payment_date'] ?></td>
                            <td>
                                <a href="?delete_payment=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete payment?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>

        <!-- COURSES -->
        <div>
            <h4>📚 Courses</h4>

            <!-- Search courses -->
            <form method="get" class="mb-3">
                <input type="text" name="course_search" class="form-control form-control-sm" placeholder="Search courses..." value="<?= htmlspecialchars($_GET['course_search'] ?? '') ?>">
            </form>

            <div class="card mb-4">
                <div class="card-body">
                    <table class="table table-striped table-hover table-bordered table-sm">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>video url</th>
                            <th>Action</th>


                        </tr>
                        <?php while ($c = $courses->fetch_assoc()): ?>
                            <tr>
                                <form method="post">
                                    <td><?= $c['id'] ?></td>
                                    <td><input type="text" name="title" value="<?= htmlspecialchars($c['title']) ?>" class="form-control form-control-sm"></td>
                                    <td><input type="number" step="0.01" name="price" value="<?= $c['price'] ?>" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="video_url" value="<?= htmlspecialchars($c['video_url']) ?>" class="form-control form-control-sm"></td>
                                    <td>
                                        <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                                        <button type="submit" name="save_course" class="btn btn-sm btn-primary">Save</button>
                                        <a href="?delete_course=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete course?')">Delete</a>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                        <tr>
                            <form method="post">
                                <td>New</td>
                                <td><input type="text" name="title" class="form-control form-control-sm" placeholder="Title" required></td>
                                <td><input type="number" step="0.01" name="price" class="form-control form-control-sm" placeholder="Price" required></td>
                                <td><input type="text" name="video_url" class="form-control form-control-sm" placeholder="url" required></td>
                                <td><button type="submit" name="save_course" class="btn btn-sm btn-success">Add</button></td>
                            </form>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
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
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Street, Vehari</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+923047727722</p>
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
                                    href="choudharyhaseeb86@gmail.com">Subscribe</a></button>
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