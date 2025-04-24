<?php
// Enable error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $pass = trim($_POST["password"]);

    // Validate inputs
    if (empty($user) || empty($email) || empty($pass)) {
        die("All fields are required!");
    }

    // Check if user already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // User already exists - Show message and redirect
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let msgBox = document.createElement('div');
            msgBox.id = 'msgBox';
            msgBox.innerHTML = '&#9888; User already registered! Redirecting to login...';
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
            }, 2000);
        });
    </script>";
        exit;
    }

    // Close the check statement
    $check_stmt->close();

    // Store password as plain text (Not recommended for security reasons)
    $plain_password = $pass;

    // Insert new user into the database
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user, $email, $plain_password);

    if ($stmt->execute()) {
        // Signup successful - Show message and redirect
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let msgBox = document.createElement('div');
            msgBox.id = 'msgBox';
            msgBox.innerHTML = '&#10004; Signup successful! Redirecting to login...';
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
            }, 2000);
        });
    </script>";
    } else {
        // Error - Show message and redirect back
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.body.innerHTML += '<div id=\"msgBox\" style=\"position:fixed; top:20px; left:50%; transform:translateX(-50%); background:#28a745; color:#fff; padding:10px 20px; border-radius:5px; font-size:16px; z-index:1000;\">Error in registration! Please try again.</div>';
                setTimeout(function() {
                    window.history.back();
                }, 2000);
            });
        </script>";
    }

    // Close statements and connection
    $stmt->close();
    $conn->close();
}

?>
