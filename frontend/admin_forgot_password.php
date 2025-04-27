<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Forgot Password | CourseCraft</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/iconn.png" rel="icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
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
</nav>
<!-- Navbar End -->

<!-- Header -->
<div class="container-fluid bg-primary py-5 mb-5 page-header">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 text-white">Forgot Password</h1>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Form -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.3s">
            <form class="bg-light rounded p-5">
                <p class="text-center">Please contact the super admin to reset your password.</p>
                <div class="text-center">
                    <a href="admin_login.php" class="btn btn-primary py-2 px-5 mt-3">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
