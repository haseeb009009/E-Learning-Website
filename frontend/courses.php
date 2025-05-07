<?php
include 'auth.php';

$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch courses
$sql = "SELECT id, title, price, duration FROM courses";
$result = $conn->query($sql);

// Store results
$courses = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CourseCraft : Courses</title>
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
    <div>
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


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
                    <a href="index.php" class="nav-item nav-link ">Home</a>
                    <a href="courses.php" class="nav-item nav-link">Courses</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">About</a>
                        <div class="dropdown-menu fade-down m-0">
                            <a href="about.php" class="dropdown-item">About</a>
                            <a href="team.php" class="dropdown-item">Our Team</a>
                            <a href="contact.php" class="dropdown-item">Contact</a>
                            <a href="instructor.php" class="dropdown-item">Our instructors</a>
                            <a href="testimonial.php" class="dropdown-item">Testimonial</a>
                        </div>
                    </div>
                    <a href="contact.php" class="nav-item nav-link"></a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.html" class="nav-item nav-link"><i class="fa fa-user"></i>login</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="profile.php" class="nav-item nav-link">Profile</a>
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

        <!-- Header Start -->
        <div class="container-fluid bg-primary py-5 mb-5 page-header">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-lg-10 text-center">
                        <h1 class="display-3 text-white animated slideInDown">Courses</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </div>
<!-- Courses Start -->
<div class="container mb-5">
    <form method="GET" action="courses.php" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search for courses..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <select name="filter" class="form-control me-2">
            <option value="">All</option>
            <option value="free" <?= isset($_GET['filter']) && $_GET['filter'] === 'free' ? 'selected' : '' ?>>Free</option>
            <option value="paid" <?= isset($_GET['filter']) && $_GET['filter'] === 'paid' ? 'selected' : '' ?>>Paid</option>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

<?php
if ((isset($_GET['search']) && !empty(trim($_GET['search']))) || isset($_GET['filter'])) {
    $searchTerm = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

    $sql = "SELECT id, title, price, duration FROM courses WHERE 1=1";

    if (!empty($searchTerm)) {
        $sql .= " AND title LIKE '%$searchTerm%'";
    }

    if ($filter === 'free') {
        $sql .= " AND price = 0";
    } elseif ($filter === 'paid') {
        $sql .= " AND price > 0";
    }

    $result = $conn->query($sql);

    $courses = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
}
?>

        <div class="row g-4 py-2">
                <?php foreach ($courses as $course): 
                $isFree = floatval($course['price']) == 0.0;
                $label = $isFree ? 'FREE' : 'PAID';
                $labelColor = $isFree ? '#fb873f' : '#0ed44c';
            ?>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="course-item shadow">
                    <div class="position-relative overflow-hidden text-light image">
                        <img class="img-fluid" src="img/course-<?= $course['id'] ?>.jpg" alt="">
                        <div style="position:absolute;top: 15px;left: 16px; font-size:12px; border-radius:3px; background-color:<?= $labelColor ?>;"
                            class="px-2 py-1 fw-bold text-uppercase">
                            <?= $label ?>
                        </div>
                    </div>
                    <div class="p-2 pb-0">
                        <h5 class="mb-1">
                            <a href="course_details.php?course_id=<?= $course['id'] ?>" class="text-dark">
                                <?= htmlspecialchars($course['title']) ?>
                            </a>
                        </h5>
                    </div>
                    <div class="d-flex">
                        <small class="flex-fill text-center py-1 px-2"><i class="fa fa-star text-warning me-2"></i>4.5</small>
                        <small class="flex-fill text-center py-1 px-2"><i class="fa fa-user-graduate me-2"></i>Learners</small>
                        <small class="flex-fill text-center py-1 px-2"><i class="fa fa-user me-2"></i>Beginner</small>
                    </div>
                    <div class="d-flex">
                        <small class="flex-fill text-left p-2 px-2"><i class="fa fa-clock me-2"></i><?= htmlspecialchars($course['duration']) ?> Hrs</small>
                        <small class=" text-primary py-1 px-2 fw-bold fs-6" style="float:right;">
                            <a href="course_details.php?course_id=<?= $course['id'] ?>">Enroll Now</a>
                            <i class="fa fa-chevron-right me-2 fs-10"></i>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Courses End -->

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