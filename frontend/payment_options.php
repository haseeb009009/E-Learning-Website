<?php
include 'auth.php';

$host = "localhost";
$dbname = "lms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

$course_id = $_GET['course_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check = $conn->prepare("SELECT id FROM payments WHERE user_id = ? AND course_id = ?");
    $check->bind_param("ii", $user_id, $course_id);
    $check->execute();
    $existing = $check->get_result();

    if ($existing->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO payments (user_id, course_id, amount, payment_status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iid", $user_id, $course_id, $course['price']);

        if ($stmt->execute()) {
            echo "✅ Payment inserted!";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
    }

    echo "<script>window.location.href='payment.php?course_id=$course_id';</script>";
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CourseCraft : Payment Options</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/iconn.png" rel="icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }

        .payment-box {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-box bg-white shadow p-4">
                    <h2 class="text-primary mb-4 text-center">Select a Payment Method</h2>

                    <p><strong>Course:</strong> <?php echo $course['title']; ?></p>
                    <p><strong>Price:</strong> $<?php echo $course['price']; ?></p>

                    <form method="POST">

                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                        <div class="mb-3">
                            <label for="method">Choose a method:</label>
                            <select id="method" class="form-control" onchange="toggleFields(this.value)" required>
                                <option value="">-- Select --</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="wallet">E-Wallet (JazzCash, EasyPaisa)</option>
                            </select>
                        </div>

                        <div id="cardFields" class="hidden">
                            <div class="mb-3">
                                <label>Card Number</label>
                                <input type="text" class="form-control" placeholder="1234-5678-9012-3456" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" minlength="11" maxlength="16">
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label>Expiry Month</label>
                                    <input type="text" class="form-control" placeholder="MM" maxlength="2" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Expiry Year</label>
                                    <input type="text" class="form-control" placeholder="YYYY" maxlength="4" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4); validateYear(this);" minlength="4">
                                    <small class="text-danger d-none" id="yearError">Year must be 4 digits</small>
                                </div>
                                <script>
                                    function validateYear(input) {
                                        const errorElement = document.getElementById('yearError');
                                        if (input.value.length < 4) {
                                            errorElement.classList.remove('d-none');
                                        } else {
                                            errorElement.classList.add('d-none');
                                        }
                                    }
                                </script>
                                <div class="col-md-6 mb-3">
                                    <label>CVV</label>
                                    <input type="text" class="form-control" placeholder="123" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                                <p class="text-muted">Send payment of $<?php echo $course['price']; ?> to <strong>card number 03047727722 (NAME: CourseCraft limited)</strong></p>

                            </div>
                        </div>

                        <div id="walletFields" class="hidden">
                            <div class="mb-3">
                                <label>Choose Wallet</label>
                                <select class="form-control">
                                    <option>JazzCash</option>
                                    <option>EasyPaisa</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Your Wallet Account Number (for verification purpose)</label>
                                <input type="text" class="form-control" placeholder="03XXXXXXXXX" required
                                    oninput="validateWalletNumber(this)" maxlength="11">
                                <small class="text-danger d-none" id="walletError">Wallet number must be exactly 11 digits.</small>
                            </div>

                            <script>
                                function validateWalletNumber(input) {
                                    const errorElement = document.getElementById('walletError');
                                    if (input.value.length !== 11) {
                                        errorElement.classList.remove('d-none');
                                    } else {
                                        errorElement.classList.add('d-none');
                                    }
                                }
                            </script>
                            <p class="text-muted">Send payment of <?php echo $course['price']; ?> to <strong>03047727722 ( NAME: CourseCraft limited)</strong></p>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-3" onclick="return validateForm();">I've Paid</button>

                        <div id="messageBox" class="mt-3"></div>

                        <script>
                            function validateForm() {
                                const method = document.getElementById('method').value;
                                const messageBox = document.getElementById('messageBox');
                                messageBox.innerHTML = ''; // Clear previous messages

                                if (!method) {
                                    messageBox.innerHTML = '<p class="text-danger">Please select a payment method.</p>';
                                    return false;
                                }

                                if (method === 'card') {
                                    const cardNumber = document.querySelector('#cardFields input[placeholder="1234-5678-9012-3456"]').value;
                                    const expiryMonth = document.querySelector('#cardFields input[placeholder="MM"]').value;
                                    const expiryYear = document.querySelector('#cardFields input[placeholder="YYYY"]').value;
                                    const cvv = document.querySelector('#cardFields input[placeholder="123"]').value;

                                    if (!cardNumber || !expiryMonth || !expiryYear || !cvv) {
                                        messageBox.innerHTML = '<p class="text-danger">Please fill in all card details.</p>';
                                        return false;
                                    }
                                }

                                if (method === 'wallet') {
                                    const walletAccount = document.querySelector('#walletFields input[placeholder="03XXXXXXXXX"]').value;

                                    if (!walletAccount) {
                                        messageBox.innerHTML = '<p class="text-danger">Please provide your wallet account number.</p>';
                                        return false;
                                    }
                                }

                                messageBox.innerHTML = '<p class="text-success">Payment details submitted successfully. Redirecting...</p>';
                                setTimeout(() => {
                                    window.location.href = 'payment.php?course_id=<?php echo $course_id; ?>';
                                }, 2000);
                                return true;
                            }
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFields(method) {
            document.getElementById('cardFields').classList.toggle('hidden', method !== 'card');
            document.getElementById('walletFields').classList.toggle('hidden', method !== 'wallet');
        }
    </script>
</body>

</html>