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

// Check if participant ID is provided
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get participant ID from URL
    $id = trim($_GET["id"]);
    
    // Prepare a select statement
    $sql = "SELECT * FROM participant WHERE id = ?";
    
    if($stmt = $conn->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_id);
        
        // Set parameters
        $param_id = $id;
        
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            $result = $stmt->get_result();
            
            if($result->num_rows == 1){
                // Fetch result row as an associative array
                $row = $result->fetch_assoc();
                
                // Retrieve individual field values
                $firstname = $row["firstname"];
                $surname = $row["surname"];
                $email = $row["email"];
                $kills = $row["kills"];
                $deaths = $row["deaths"];
                $team_id = $row["team_id"];
                
                // Get team name
                $team_sql = "SELECT name FROM team WHERE id = ?";
                $team_stmt = $conn->prepare($team_sql);
                $team_stmt->bind_param("i", $team_id);
                $team_stmt->execute();
                $team_result = $team_stmt->get_result();
                $team_row = $team_result->fetch_assoc();
                $team_name = $team_row ? $team_row["name"] : "Unknown Team";
                $team_stmt->close();
                
            } else{
                // Participant with specified ID does not exist
                header("location: view_participants_edit_delete.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    // Close statement
    $stmt->close();
    
} else{
    // URL doesn't contain ID parameter
    header("location: view_participants_edit_delete.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Participant | UK E-Sports League</title>
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
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Participant
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fs-4 me-2"></i>
                                <div>
                                    <strong>Note:</strong> You can only update the kills and deaths for this participant.
                                </div>
                            </div>
                        </div>
                        
                        <form action="edit_participant.php" method="post" class="needs-validation" novalidate>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($firstname); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Surname</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($surname); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Team</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($team_name); ?>" readonly>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="kills" class="form-label">Kills</label>
                                    <input type="number" class="form-control" id="kills" name="kills" value="<?php echo $kills; ?>" required min="0">
                                    <div class="invalid-feedback">
                                        Please enter a valid number of kills.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="deaths" class="form-label">Deaths</label>
                                    <input type="number" class="form-control" id="deaths" name="deaths" value="<?php echo $deaths; ?>" required min="0">
                                    <div class="invalid-feedback">
                                        Please enter a valid number of deaths.
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <input type="hidden" name="team_id" value="<?php echo $team_id; ?>">
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-2"></i>
                                    Update Participant
                                </button>
                                <a href="view_participants_edit_delete.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Cancel
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
