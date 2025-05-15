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
$course_sql = "SELECT * FROM courses WHERE id = ?";
$course_stmt = $conn->prepare($course_sql);
$course_stmt->bind_param("i", $course_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();

if ($course_result->num_rows === 0) {
    echo "<script>alert('Course not found!'); window.location.href='courses.php';</script>";
    exit;
}
$course = $course_result->fetch_assoc();
$course_stmt->close();

// Fetch user details
$user_sql = "SELECT username, email FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    echo "<script>alert('User not found!'); window.location.href='login.html';</script>";
    exit;
}
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Payment check (if course is paid)
if ($course['price'] > 0) {
    $payment_sql = "SELECT * FROM payments WHERE user_id = ? AND course_id = ? AND payment_status = 'completed'";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("ii", $user_id, $course_id);
    $payment_stmt->execute();
    $payment_result = $payment_stmt->get_result();

    if ($payment_result->num_rows === 0) {
        echo "<script>alert('You must purchase this course to access it!'); window.location.href='courses.php';</script>";
        exit;
    }
    $payment_stmt->close();
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
            <h1><?php echo htmlspecialchars($course['title']); ?></h1>
            <?php
            $video_url = $course['video_url'];
            $embed_code = '';
            if (strpos($video_url, 'youtube.com/embed') !== false) {
                $embed_code = '<iframe width="800" height="400" src="' . htmlspecialchars($video_url) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
            } elseif (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                parse_str(parse_url($video_url, PHP_URL_QUERY), $ytParams);
                $videoId = $ytParams['v'] ?? '';
                if (!$videoId && strpos($video_url, 'youtu.be') !== false) {
                    $path = parse_url($video_url, PHP_URL_PATH);
                    $videoId = ltrim($path, '/');
                }
                $embed_code = '<iframe width="800" height="400" src="https://www.youtube-nocookie.com/embed/' . $videoId . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
            } elseif (strpos($video_url, 'drive.google.com') !== false) {
                if (preg_match('/\/d\/(.+?)\//', $video_url, $matches)) {
                    $fileId = $matches[1];
                    $embed_code = '<iframe src="https://drive.google.com/file/d/' . $fileId . '/preview" width="800" height="400" allow="autoplay"></iframe>';
                }
            } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $video_url)) {
                $embed_code = '<video width="800" height="400" controls><source src="' . htmlspecialchars($video_url) . '" type="video/mp4">Your browser does not support the video tag.</video>';
            } elseif (strpos($video_url, 'vimeo.com') !== false) {
                $vimeoId = (int) substr(parse_url($video_url, PHP_URL_PATH), 1);
                $embed_code = '<iframe src="https://player.vimeo.com/video/' . $vimeoId . '" width="800" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
            } else {
                $embed_code = '<a href="' . htmlspecialchars($video_url) . '" target="_blank">View Video</a>';
            }

            echo $embed_code;
            ?>

            <h3>Take Notes</h3>
            <textarea id="notes" rows="3" cols="60" placeholder="Write your notes here..."></textarea>
            <br>
            <button class="btn text-light w-10 py-1" onclick="downloadNotes()">Save Notes</button>
            <br><br>
            <button id="certificateBtn" class="btn text-light w-10 py-1" onclick="generateCertificate()" disabled>Download Certificate</button>
            <script>
                // Helper to enable certificate button after half video watched

                document.addEventListener("DOMContentLoaded", function () {
                    // Try to get the video or iframe
                    let video = document.querySelector("video");
                    let iframe = document.querySelector("iframe");
                    if (video) {
                        // For HTML5 video
                        video.addEventListener("timeupdate", function () {
                            if (video.currentTime >= video.duration / 2) {
                                document.getElementById("certificateBtn").disabled = false;
                            }
                        });
                    } else if (iframe && (iframe.src.includes("youtube") || iframe.src.includes("vimeo"))) {
                        if (iframe.src.includes("youtube")) {
                            // Inject enablejsapi=1 if not present
                            if (!iframe.src.includes("enablejsapi=1")) {
                                let src = iframe.src;
                                src += (src.includes("?") ? "&" : "?") + "enablejsapi=1";
                                iframe.src = src;
                            }
                            window.onYouTubeIframeAPIReady = function () {
                                let player = new YT.Player(iframe, {
                                    events: {
                                        'onStateChange': function (event) {
                                            if (event.data == YT.PlayerState.PLAYING) {
                                                let interval = setInterval(function () {
                                                    player.getDuration && player.getCurrentTime && player.getDuration() > 0 && player.getCurrentTime() >= player.getDuration() / 2 && (document.getElementById("certificateBtn").disabled = false, clearInterval(interval));
                                                }, 1000);
                                            }
                                        }
                                    }
                                });
                            };
                            // Load YouTube API if not loaded
                            if (!window.YT) {
                                let tag = document.createElement('script');
                                tag.src = "https://www.youtube.com/iframe_api";
                                document.body.appendChild(tag);
                            } else if (window.YT && window.YT.Player) {
                                window.onYouTubeIframeAPIReady();
                            }
                        }
                        // For Vimeo (basic, only works if allowed)
                        else if (iframe.src.includes("vimeo")) {
                            let vimeoPlayer = new Vimeo.Player(iframe);
                            vimeoPlayer.getDuration().then(function(duration) {
                                vimeoPlayer.on('timeupdate', function(data) {
                                    if (data.seconds >= duration / 2) {
                                        document.getElementById("certificateBtn").disabled = false;
                                    }
                                });
                            });
                        }
                    }
                });
                
                // Directly enable the certificate button (bypass video checks)
                
                // document.addEventListener("DOMContentLoaded", function () {
                //     document.getElementById("certificateBtn").disabled = false;
                // });
            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        </div>
    </center>




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
    <script>
    function downloadNotes() {
        const notes = document.getElementById("notes").value;
        const blob = new Blob([notes], { type: "text/plain" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "Course_Notes.txt";
        link.click();
    }

    function generateCertificate() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: "landscape", unit: "pt", format: "a4" });

        // Colors and styles
        const COLORS = {
            primary: "#fd7e14",
            secondary: "#f8f9fa",
            border: "#dee2e6",
            accent: "#343a40",
            gold: "#000000",
        };

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // PHP-injected data
        const userName = "<?php echo addslashes($user['username']); ?>";
        const userEmail = "<?php echo addslashes($user['email']); ?>";
        const courseTitle = "<?php echo addslashes($course['title']); ?>";
        const currentDate = new Date().toLocaleDateString();

        // Draw common certificate layout
        function drawCertificateLayout(withLogo = false) {
            // Borders
            doc.setLineWidth(6).setDrawColor(COLORS.gold).rect(20, 20, pageWidth - 40, pageHeight - 40, "S");
            doc.setLineWidth(2).setDrawColor(COLORS.primary).rect(35, 35, pageWidth - 70, pageHeight - 70, "S");

            // Header Bars
            doc.setFillColor(COLORS.primary).rect(20, 20, pageWidth - 40, 60, "F");
            doc.setFillColor(COLORS.gold).rect(20, 70, pageWidth - 40, 10, "F");

            // Ribbon
            doc.setFillColor(COLORS.primary).triangle(pageWidth - 120, 20, pageWidth - 60, 20, pageWidth - 90, 60, "F");

            // Logo or fallback text
            if (withLogo) {
                doc.addImage(logo, "PNG", 50, 30, 50, 50);
                doc.setFont("helvetica", "bold").setFontSize(28).setTextColor(255, 255, 255);
                doc.text("CourseCraft", 115, 65);
            } else {
                doc.setFont("helvetica", "bold").setFontSize(28).setTextColor(255, 255, 255);
                doc.text("CourseCraft", 50, 65);
            }

            // Title with shadow
            doc.setFontSize(40).setTextColor(60, 60, 60);
            doc.text("Certificate of Completion", pageWidth / 2 + 2, 162, { align: "center" });
            doc.setTextColor(COLORS.primary);
            doc.text("Certificate of Completion", pageWidth / 2, 160, { align: "center" });

            // Subtitle
            doc.setFont("helvetica", "italic").setFontSize(20).setTextColor(COLORS.accent);
            doc.text("This is to certify that", pageWidth / 2, 210, { align: "center" });

            // User Info
            doc.setFont("helvetica", "bold").setFontSize(26).setTextColor(COLORS.gold);
            doc.text(userName, pageWidth / 2, 255, { align: "center" });
            doc.setFontSize(16).setTextColor(COLORS.accent);
            doc.text(`(${userEmail})`, pageWidth / 2, 280, { align: "center" });

            // Course info
            doc.setFont("helvetica", "normal").setFontSize(18).setTextColor(COLORS.accent);
            doc.text("has successfully completed the course:", pageWidth / 2, 320, { align: "center" });

            doc.setFont("helvetica", "bold").setFontSize(24).setTextColor(COLORS.primary);
            doc.text(courseTitle, pageWidth / 2, 355, { align: "center" });

            // Divider
            doc.setDrawColor(COLORS.gold).setLineWidth(2);
            doc.line(pageWidth / 2 - 120, 370, pageWidth / 2 + 120, 370);

            // Date
            doc.setFont("helvetica", "normal").setFontSize(16).setTextColor(COLORS.accent);
            doc.text("Date: " + currentDate, pageWidth - 180, pageHeight - 60);

            // Signature
            doc.setDrawColor(COLORS.primary).setLineWidth(1);
            doc.line(pageWidth / 2 - 100, pageHeight - 110, pageWidth / 2 + 100, pageHeight - 110);
            doc.setFontSize(14).setTextColor(COLORS.accent);
            doc.text("CourseCraft Team", pageWidth / 2, pageHeight - 95, { align: "center" });

            // Seal
            doc.setDrawColor(COLORS.gold).setFillColor(COLORS.primary);
            doc.circle(pageWidth - 120, pageHeight - 120, 35, "FD");
            doc.setFont("helvetica", "bold").setFontSize(logo?.complete ? 10 : 18).setTextColor(255, 255, 255);
            doc.text(logo?.complete ? "APPROVED" : "CC", pageWidth - 120, pageHeight - 115, { align: "center" });

            // Save
            doc.save(`Certificate_${courseTitle.replace(/\s+/g, "_")}.pdf`);
        }

        // Load logo image
        const logo = new Image();
        logo.src = "img/iconn.png";

        logo.onload = () => drawCertificateLayout(true);
        logo.onerror = () => drawCertificateLayout(false);
    }
</script>


</body>

</html>