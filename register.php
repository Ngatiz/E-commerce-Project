<?php include 'header.php'?>
<?php include 'navbar.php'?>
<?php include 'db.php'?>

<?php
// Initialize error and success messages
$errorMsg = $successMsg = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['registerName'];
    $email = $_POST['registerEmail'];
    $password = $_POST['registerPassword'];

    // Simple validation
    if (empty($name) || empty($email) || empty($password)) {
        $errorMsg = "All fields are required.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $checkEmail = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $errorMsg = "Email already exists. Please login.";
        } else {
            // Insert user data into the database
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";

            if ($conn->query($sql) === TRUE) {
                $successMsg = "Registration successful! Redirecting to login...";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'login';
                        }, 3000); // 3 seconds delay
                    </script>";
            } else {
                $errorMsg = "Error: " . $conn->error;
            }
        }
    }
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center">Register</h3>

            <!-- Display Error Message -->
            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
            <?php endif; ?>

            <!-- Display Success Message -->
            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success">
                    <?php echo $successMsg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="registerName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="registerName" name="registerName" placeholder="Enter your name" value="<?php echo isset($name) ? $name : ''; ?>" >
                </div>
                <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="registerEmail" name="registerEmail" placeholder="Enter your email" value="<?php echo isset($email) ? $email : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="registerPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="registerPassword" name="registerPassword" placeholder="Create a password">
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
            </form>

            <p class="mt-3 text-center">Already have an account? <a href="login">Login here</a></p>
        </div>
    </div>
</div>

<?php include 'footer.php'?>