<?php
session_start();


// Enable error reporting for debugging (Remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

$reset_link = ""; // Variable to store the clickable reset link
$show_reset_form = false; // Flag to show reset form

// If the user submits the forgot password form
if (isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "<script>alert('Please enter your email'); window.location.href='forgot_password.php';</script>";
        exit;
    }

    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // Fetch logged-in user's email
        $sql = "SELECT email FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($logged_in_email);
        $stmt->fetch();
        $stmt->close();

        // Ensure the given email matches the logged-in user's email
        if ($email !== $logged_in_email) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgBox = document.createElement('div');
                msgBox.id = 'msgBox';
                msgBox.innerHTML = '&#9888; Please enter your correct email';
                msgBox.style.position = 'fixed';
                msgBox.style.top = '50%';
                msgBox.style.left = '50%';
                msgBox.style.transform = 'translate(-50%, -50%)';
                msgBox.style.background = '#fb873f';
                msgBox.style.color = 'black';
                msgBox.style.padding = '16px 28px';
                msgBox.style.borderRadius = '10px';
                msgBox.style.fontSize = '18px';
                msgBox.style.fontWeight = 'bold';
                msgBox.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.3)';
                msgBox.style.display = 'flex';
                msgBox.style.alignItems = 'center';
                msgBox.style.justifyContent = 'center';
                msgBox.style.gap = '12px';
                msgBox.style.zIndex = '1000';
                msgBox.style.opacity = '0.95';
                msgBox.style.textAlign = 'center';
                msgBox.style.width = 'auto';
                msgBox.style.maxWidth = '80%';
                document.body.appendChild(msgBox);
                setTimeout(function() {
                    window.location.href = 'forgot_password.php';
                }, 1000);
            });
        </script>";
            exit;
        }
    }


    // Check if email exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a reset token
        $token = bin2hex(random_bytes(20));
        $stmt->close();

        // Store token in the database
        $sql = "UPDATE users SET reset_token = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Generate the clickable reset link
        $reset_link = "<p>Password reset link: <a href='forgot_password.php?token=$token'>Click here to reset your password</a></p>";
    } else {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let msgBox = document.createElement('div');
            msgBox.id = 'msgBox';
            msgBox.innerHTML = '&#9888; EMAIL NOT FOUND...';
            msgBox.style.position = 'fixed';
            msgBox.style.top = '50%';
            msgBox.style.left = '50%';
            msgBox.style.transform = 'translate(-50%, -50%)';
            msgBox.style.background = '#fb873f';
            msgBox.style.color = 'black';
            msgBox.style.padding = '16px 28px';
            msgBox.style.borderRadius = '10px';
            msgBox.style.fontSize = '18px';
            msgBox.style.fontWeight = 'bold';
            msgBox.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.3)';
            msgBox.style.display = 'flex';
            msgBox.style.alignItems = 'center';
            msgBox.style.justifyContent = 'center';
            msgBox.style.gap = '12px';
            msgBox.style.zIndex = '1000';
            msgBox.style.opacity = '0.95';
            msgBox.style.textAlign = 'center';
            msgBox.style.width = 'auto';
            msgBox.style.maxWidth = '80%';
            document.body.appendChild(msgBox);
            setTimeout(function() {
                window.location.href = 'forgot_password.php';
            }, 1000);
        });
    </script>";
        exit;
    }
}

// If the user clicked the reset link
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $sql = "SELECT id FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $show_reset_form = true; // Show the password reset form
    } else {
        echo "<script>alert('Invalid or expired token'); window.location.href='login.html';</script>";
        exit;
    }
}

if (isset($_POST['update_password'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Start session only if not already started
    }

    $token = $_POST['token'];
    $new_password = trim($_POST['new_password']);

    if (empty($new_password)) {
        echo "<script>alert('Please enter a new password'); window.history.back();</script>";
        exit;
    }

    // Update the password and clear the token
    $sql = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $token);
    $stmt->execute();

    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgBox = document.createElement('div');
                msgBox.id = 'msgBox';
                msgBox.innerHTML = '&#10004; Password updated successfully! Redirecting to your profile...';
                msgBox.style.position = 'fixed';
                msgBox.style.top = '50%';
                msgBox.style.left = '50%';
                msgBox.style.transform = 'translate(-50%, -50%)';
                msgBox.style.background = '#fb873f';
                msgBox.style.color = 'black';
                msgBox.style.padding = '16px 28px';
                msgBox.style.borderRadius = '10px';
                msgBox.style.fontSize = '18px';
                msgBox.style.fontWeight = 'bold';
                msgBox.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.3)';
                msgBox.style.display = 'flex';
                msgBox.style.alignItems = 'center';
                msgBox.style.justifyContent = 'center';
                msgBox.style.gap = '12px';
                msgBox.style.zIndex = '1000';
                msgBox.style.opacity = '0.95';
                msgBox.style.textAlign = 'center';
                msgBox.style.width = 'auto';
                msgBox.style.maxWidth = '80%';
                document.body.appendChild(msgBox);
                setTimeout(function() {
                    window.location.href = 'profile.php';
                }, 1000);
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgBox = document.createElement('div');
                msgBox.id = 'msgBox';
                msgBox.innerHTML = '&#10004; Password updated successfully! Redirecting to login...';
                msgBox.style.position = 'fixed';
                msgBox.style.top = '50%';
                msgBox.style.left = '50%';
                msgBox.style.transform = 'translate(-50%, -50%)';
                msgBox.style.background = '#fb873f';
                msgBox.style.color = 'black';
                msgBox.style.padding = '16px 28px';
                msgBox.style.borderRadius = '10px';
                msgBox.style.fontSize = '18px';
                msgBox.style.fontWeight = 'bold';
                msgBox.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.3)';
                msgBox.style.display = 'flex';
                msgBox.style.alignItems = 'center';
                msgBox.style.justifyContent = 'center';
                msgBox.style.gap = '12px';
                msgBox.style.zIndex = '1000';
                msgBox.style.opacity = '0.95';
                msgBox.style.textAlign = 'center';
                msgBox.style.width = 'auto';
                msgBox.style.maxWidth = '80%';
                document.body.appendChild(msgBox);
                setTimeout(function() {
                window.location.href = 'login.html';
                }, 1000);
            });
        </script>";
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forgot Password</title>
    <meta charset="utf-8">
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
                <a href="login.html" class="nav-item nav-link"><i class="fa fa-user"> login</i></a>
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
                    <h1 class="display-3 text-white animated slideInDown">forgot Password</h1>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
     <!-- Hform -->
    <div class="container-xxl py-2 mt-4">
        <div class="container">
            <div class="row g-4 wow fadeInUp" data-wow-delay="0.5s ">
                <center>
                    <?php if (!$show_reset_form): ?>
                        <form class="shadow p-4" style="max-width: 550px;" method="POST">
                            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                                <h1 class="mb-5 bg-white text-center px-3">Forgot Password</h1>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn text-light w-100 py-3" type="submit" name="reset_request">Submit</button>
                                </div>
                                <div class="col-12 text-center">
                                    <?php echo $reset_link; ?>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <form class="shadow p-4" style="max-width: 550px;" method="POST">
                            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                                <h1 class="mb-5 bg-white text-center px-3">Reset Password</h1>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
                                        <label for="new_password">New Password</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn text-light w-100 py-3" type="submit" name="update_password">Reset Password</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </center>
            </div>
        </div>
    </div>
<!-- form End -->

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


</body>

</html>