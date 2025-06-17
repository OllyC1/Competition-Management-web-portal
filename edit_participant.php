<?php
// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: admin_login.html");
    exit;
}

// Include database connection
include 'dbconnect.php';

// Define variables and initialize with empty values
$kills = $deaths = "";
$kills_err = $deaths_err = "";
$success = false;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate kills
    if(empty(trim($_POST["kills"]))){
        $kills_err = "Please enter the number of kills.";
    } elseif(!is_numeric($_POST["kills"]) || $_POST["kills"] < 0){
        $kills_err = "Please enter a valid number of kills.";
    } else{
        $kills = trim($_POST["kills"]);
    }
    
    // Validate deaths
    if(empty(trim($_POST["deaths"]))){
        $deaths_err = "Please enter the number of deaths.";
    } elseif(!is_numeric($_POST["deaths"]) || $_POST["deaths"] < 0){
        $deaths_err = "Please enter a valid number of deaths.";
    } else{
        $deaths = trim($_POST["deaths"]);
    }
    
    // Check input errors before updating the database
    if(empty($kills_err) && empty($deaths_err)){
        try {
            // Prepare an update statement
            $sql = "UPDATE participant SET kills = ?, deaths = ? WHERE id = ?";
            
            if($stmt = $conn->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("iii", $param_kills, $param_deaths, $param_id);
                
                // Set parameters
                $param_kills = $kills;
                $param_deaths = $deaths;
                $param_id = $_POST["id"];
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    $success = true;
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            
            // Close statement
            $stmt->close();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Result | UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_menu.php">
                <i class="bi bi-controller me-2"></i>UK E-Sports League
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_menu.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="view_participants_edit_delete.php">
                            <i class="bi bi-people me-1"></i> Participants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <?php if($success): ?>
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            </div>
                            <h2 class="mb-4">Update Successful!</h2>
                            <p class="lead mb-4">The participant's kills and deaths have been updated successfully.</p>
                            <div class="d-grid gap-2">
                                <a href="view_participants_edit_delete.php" class="btn btn-primary">
                                    <i class="bi bi-people me-2"></i>
                                    Return to Participants
                                </a>
                                <a href="admin_menu.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    Go to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                <?php elseif(!empty($kills_err) || !empty($deaths_err)): ?>
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h2 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Update Failed
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-danger">
                                <h5 class="alert-heading">Please fix the following errors:</h5>
                                <ul class="mb-0">
                                    <?php if(!empty($kills_err)): ?>
                                        <li><?php echo $kills_err; ?></li>
                                    <?php endif; ?>
                                    <?php if(!empty($deaths_err)): ?>
                                        <li><?php echo $deaths_err; ?></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="javascript:history.back()" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Go Back
                                </a>
                                <a href="view_participants_edit_delete.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-people me-2"></i>
                                    Return to Participants
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <h2>No form submission detected</h2>
                            <p class="lead mb-4">Please use the edit form to update participant information.</p>
                            <div class="d-grid">
                                <a href="view_participants_edit_delete.php" class="btn btn-primary">
                                    <i class="bi bi-people me-2"></i>
                                    Go to Participants
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
