<?php
// Start session
session_start();

// Include database connection
include 'dbconnect.php';

// Initialize variables
$username = $password = "";
$login_error = "";
$attempt_count = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check if max attempts reached
    if ($attempt_count >= 5) {
        $login_error = "Too many failed login attempts. Please try again later.";
    } else {
        // Validate and sanitize inputs
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password']; // Don't sanitize password to preserve special characters
        
        // Validate username
        if (empty($username)) {
            $login_error = "Please enter a username.";
        }
        // Validate password
        elseif (empty($password)) {
            $login_error = "Please enter a password.";
        }
        // Validate credentials
        else {
            try {
                // Prepare a select statement
                $sql = "SELECT id, username, password FROM user WHERE username = ?";
                
                if ($stmt = $conn->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("s", $param_username);
                    
                    // Set parameters
                    $param_username = $username;
                    
                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Store result
                        $stmt->store_result();
                        
                        // Check if username exists
                        if ($stmt->num_rows == 1) {
                            // Bind result variables
                            $stmt->bind_result($id, $username, $hashed_password);
                            
                            if ($stmt->fetch()) {
                                // For this project, we're using plain text password as specified
                                if ($password == $hashed_password) {
                                    // Password is correct, reset login attempts
                                    $_SESSION['login_attempts'] = 0;
                                    
                                    // Store data in session variables
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["username"] = $username;
                                    $_SESSION["last_activity"] = time();
                                    
                                    // Redirect user to admin menu page
                                    header("location: admin_menu.php");
                                    exit;
                                } else {
                                    // Password is not valid
                                    $login_error = "Invalid username or password.";
                                    $_SESSION['login_attempts'] = $attempt_count + 1;
                                }
                            }
                        } else {
                            // Username doesn't exist
                            $login_error = "Invalid username or password.";
                            $_SESSION['login_attempts'] = $attempt_count + 1;
                        }
                    } else {
                        $login_error = "Oops! Something went wrong. Please try again later.";
                    }
                    
                    // Close statement
                    $stmt->close();
                }
            } catch (Exception $e) {
                $login_error = "An error occurred: " . $e->getMessage();
            }
        }
    }
    
    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Result | UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <i class="bi bi-controller me-2"></i>UK E-Sports League
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register_form.html">
                            <i class="bi bi-gift me-1"></i> Merchandise
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin_login.html">
                            <i class="bi bi-shield-lock me-1"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock fs-1 text-primary"></i>
                            <h2 class="mt-3">Admin Login</h2>
                        </div>
                        
                        <?php if(!empty($login_error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo $login_error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-1"></i> Username
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0">
                                        <i class="bi bi-person-fill text-primary"></i>
                                    </span>
                                    <input type="text" name="username" class="form-control" id="username" placeholder="Enter username" required value="<?php echo htmlspecialchars($username); ?>">
                                </div>
                                <div class="invalid-feedback">
                                    Please enter your username.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="bi bi-key me-1"></i> Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0">
                                        <i class="bi bi-lock-fill text-primary"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
                                    <button class="btn btn-dark border-0" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Please enter your password.
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </button>
                                <a href="index.html" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2025 UK E-Sports League. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        // Form validation
        (function() {
            'use strict';
            
            const forms = document.querySelectorAll('.needs-validation');
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
