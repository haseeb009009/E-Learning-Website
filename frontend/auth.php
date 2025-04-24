<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    session_destroy(); // Clear session completely
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        let msgBox = document.createElement('div');
        msgBox.id = 'msgBox';
        msgBox.innerHTML = '&#9888; Needs to Login! Redirecting to login...';
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
    exit;
}
?>
