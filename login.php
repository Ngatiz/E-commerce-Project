<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
// Initialize error message
$error_message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

    // Email validation
    if (empty($email)) {
        $error_message = "Email is required.";
    }
    // Password validation
    if (empty($password)) {
        $error_message = "Password is required.";
    } else {
        // Prepare a SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if the email exists in the database
        if ($stmt->num_rows === 0) {
            $error_message = 'Invalid email or password. You do not have an account? <a href="register">Register</a>.';
        } else {
            // Bind result to the hashed password
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify password
            if (!password_verify($password, $hashed_password)) {
                $error_message = 'Invalid email or password. You do not have an account? <a href="register">Register</a>.';
            } else {
                // Successful login
                // Start session and set session variables
                session_start();
                $_SESSION['email'] = $email;

                // Redirect to the cart
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'cart';
                        }, 1000); // Redirect immediately
                    </script>";
            }
        }

        // Close the statement
        $stmt->close();
    }
}
       ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center">Login</h3>

            <!-- Display Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="loginEmail" name="loginEmail" placeholder="Enter your email" value="<?php echo isset($email) ? $email : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="mt-3 text-center">Don't have an account? <a href="register">Register here</a></p>
        </div>
    </div>
</div>

<?php include 'footer.php'?>
