<?php
session_start();
$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to log in first!'); window.location.href='login.html';</script>";
    exit;
}

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    echo "<script>alert('Invalid course selection!'); window.location.href='courses.php';</script>";
    exit;
}

$course_id = $_GET['course_id'];
$user_id = $_SESSION['user_id'];

// Fetch course details
$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $course = $result->fetch_assoc();
} else {
    echo "<script>alert('Course not found!'); window.location.href='courses.php';</script>";
    exit;
}
$stmt->close();
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



    <title><?php echo $course['title']; ?> - Course</title>
</head>

<body>


    <!-- <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div> -->
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

    <center>
        <div class="container">
            <h1><?php echo $course['title']; ?></h1>
            <p><?php echo $course['description']; ?></p>

            <!-- Dynamic Video Embedding -->
            <?php
            $video_url = $course['video_url'];
            $embed_code = '';

            if (strpos($video_url, 'youtube.com/embed') !== false) {
                // Direct embed URL
                $embed_code = '<iframe width="800" height="400" src="' . htmlspecialchars($video_url) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
            } elseif (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                // Convert watch URL to embed
                parse_str(parse_url($video_url, PHP_URL_QUERY), $ytParams);
                $videoId = $ytParams['v'] ?? '';
                if (!$videoId && strpos($video_url, 'youtu.be') !== false) {
                    $path = parse_url($video_url, PHP_URL_PATH);
                    $videoId = ltrim($path, '/');
                }
                $embed_code = '<iframe width="800" height="400" src="https://www.youtube-nocookie.com/embed/' . $videoId . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
            } elseif (strpos($video_url, 'drive.google.com') !== false) {
                // Handle Google Drive
                if (preg_match('/\/d\/(.+?)\//', $video_url, $matches)) {
                    $fileId = $matches[1];
                    $embed_code = '<iframe src="https://drive.google.com/file/d/' . $fileId . '/preview" width="800" height="400" allow="autoplay"></iframe>';
                }
            } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
                // Handle direct video file links
                $embed_code = '<video width="800" height="400" controls><source src="' . htmlspecialchars($video_url) . '" type="video/mp4">Your browser does not support the video tag.</video>';
            } elseif (strpos($video_url, 'vimeo.com') !== false) {
                // Handle Vimeo
                $vimeoId = (int) substr(parse_url($video_url, PHP_URL_PATH), 1);
                $embed_code = '<iframe src="https://player.vimeo.com/video/' . $vimeoId . '" width="800" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
            } else {
                // Fallback for unknown types: just show the link
                $embed_code = '<a href="' . htmlspecialchars($video_url) . '" target="_blank">View Video</a>';
            }

            echo $embed_code;
            ?>

            <!-- Notepad Feature -->
            <h3>Take Notes</h3>
            <textarea id="notes" rows="2" cols="50" placeholder="Write your notes here..."></textarea>
            <br>
            <button class="btn text-light w-10 py-1" onclick="downloadNotes()">Save Notes</button>
        </div>
    </center>


    <script>
        function downloadNotes() {
            let notes = document.getElementById("notes").value;
            let blob = new Blob([notes], {
                type: "text/plain"
            });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "Course_Notes.txt";
            link.click();
        }
    </script>

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


</body>

</html>