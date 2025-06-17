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

// Initialize variables
$participant_info = [];
$delete_success = false;
$error_message = "";

// Process delete operation
if(isset($_POST["id"]) && !empty($_POST["id"])){
    try {
        // Prepare a delete statement
        $sql = "DELETE FROM participant WHERE id = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_id);
            
            // Set parameters
            $param_id = trim($_POST["id"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $delete_success = true;
            } else{
                $error_message = "Error: " . $stmt->error;
            }
        }
         
        // Close statement
        $stmt->close();
    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["id"]))){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: view_participants_edit_delete.php");
        exit();
    } else {
        // Get participant info before deletion
        $id = trim($_GET["id"]);
        $sql = "SELECT p.*, t.name as team_name FROM participant p 
                LEFT JOIN team t ON p.team_id = t.id 
                WHERE p.id = ?";
        
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("i", $id);
            
            if($stmt->execute()){
                $result = $stmt->get_result();
                
                if($result->num_rows == 1){
                    $participant_info = $result->fetch_assoc();
                } else {
                    header("location: view_participants_edit_delete.php");
                    exit();
                }
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Participant | UK E-Sports League</title>
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
                <?php if($delete_success): ?>
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            </div>
                            <h2 class="mb-4">Participant Deleted</h2>
                            <p class="lead mb-4">The participant has been successfully removed from the database.</p>
                            <div class="d-grid gap-2">
                                <a href="view_participants_edit_delete.php" class="btn btn-primary">
                                    <i class="bi bi-people me-2"></i>
                                    Return to Participants
                                </a>
                            </div>
                        </div>
                    </div>
                <?php elseif(!empty($error_message)): ?>
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h2 class="mb-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Error
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                            <div class="d-grid">
                                <a href="view_participants_edit_delete.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Back to Participants
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h2 class="mb-0">
                                <i class="bi bi-trash me-2"></i>
                                Delete Participant
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                                    <div>
                                        <h5 class="alert-heading">Warning!</h5>
                                        <p class="mb-0">Are you sure you want to delete this participant? This action cannot be undone.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if(!empty($participant_info)): ?>
                                <div class="card mb-4 bg-dark-lighter">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Participant Information</h5>
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <li class="list-group-item bg-transparent">
                                                <strong>Name:</strong> <?php echo htmlspecialchars($participant_info["firstname"] . " " . $participant_info["surname"]); ?>
                                            </li>
                                            <li class="list-group-item bg-transparent">
                                                <strong>Email:</strong> <?php echo htmlspecialchars($participant_info["email"]); ?>
                                            </li>
                                            <li class="list-group-item bg-transparent">
                                                <strong>Team:</strong> <?php echo htmlspecialchars($participant_info["team_name"]); ?>
                                            </li>
                                            <li class="list-group-item bg-transparent">
                                                <strong>Stats:</strong> <?php echo htmlspecialchars($participant_info["kills"]); ?> kills, 
                                                <?php echo htmlspecialchars($participant_info["deaths"]); ?> deaths
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="bi bi-trash me-2"></i>
                                        Yes, Delete Participant
                                    </button>
                                    <a href="view_participants_edit_delete.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>
                                        No, Cancel
                                    </a>
                                </div>
                            </form>
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
