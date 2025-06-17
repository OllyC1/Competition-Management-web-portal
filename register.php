<?php
// Include database connection
include 'dbconnect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize error array
    $errors = [];
    $success = false;
    
    // Validate and sanitize inputs
    $firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Validate firstname
    if (empty($firstname)) {
        $errors[] = "First name is required";
    } elseif (strlen($firstname) > 50) {
        $errors[] = "First name must be less than 50 characters";
    }
    
    // Validate surname
    if (empty($surname)) {
        $errors[] = "Surname is required";
    } elseif (strlen($surname) > 50) {
        $errors[] = "Surname must be less than 50 characters";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email must be less than 100 characters";
    }
    
    // Validate terms
    if (!$terms) {
        $errors[] = "You must agree to the terms";
    }
    
    // If no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            // Prepare SQL statement
            $sql = "INSERT INTO merchandise (firstname, surname, email, terms) VALUES (?, ?, ?, ?)";
            
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $firstname, $surname, $email, $terms);
            
            // Execute the statement
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
            
            // Close statement
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Result | UK E-Sports League</title>
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
                        <a class="nav-link active" href="register_form.html">
                            <i class="bi bi-gift me-1"></i> Merchandise
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_login.html">
                            <i class="bi bi-shield-lock me-1"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($success) && $success): ?>
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            </div>
                            <h2 class="mb-4">Registration Successful!</h2>
                            <p class="lead mb-4">Thank you for registering for UK E-Sports League merchandise. We'll keep you updated with the latest merchandise information and event updates.</p>
                            <div class="d-grid gap-2 col-md-6 mx-auto">
                                <a href="index.html" class="btn btn-primary">
                                    <i class="bi bi-house-door me-2"></i>
                                    Return to Home
                                </a>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($errors) && !empty($errors)): ?>
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-1"></i>
                                <h2 class="mt-3">Registration Failed</h2>
                            </div>
                            
                            <div class="alert alert-danger">
                                <h5 class="alert-heading">Please fix the following errors:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="register_form.html" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Back to Registration Form
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <h2>No form submission detected</h2>
                            <p class="lead mb-4">Please use the registration form to sign up for merchandise.</p>
                            <div class="d-grid gap-2 col-md-6 mx-auto">
                                <a href="register_form.html" class="btn btn-primary">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Go to Registration Form
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
</body>
</html>
