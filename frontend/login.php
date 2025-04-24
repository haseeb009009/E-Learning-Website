<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='login.html';</script>";
        exit;
    }

    // Query to find the user
    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $db_password);
        $stmt->fetch();

        if ($password === $db_password) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;


            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgBox = document.createElement('div');
                msgBox.id = 'msgBox';
                msgBox.innerHTML = '&#10004; Login successful! Redirecting to Home...';
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
                msgBox.style.boxShadow = '0 6px 12px rgb(0, 0, 0)';
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
                    window.location.href = 'index.php';
                }, 1000);
            });
        </script>";
            exit;
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgBox = document.createElement('div');
                msgBox.id = 'msgBox';
                msgBox.innerHTML = '&#9888; Invalid password! Redirecting to login...';
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
    } else {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let msgBox = document.createElement('div');
            msgBox.id = 'msgBox';
            msgBox.innerHTML = '&#9888; No user found with this email! Redirecting to login...';
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

    $stmt->close();
}
$conn->close();
