<?php
// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: admin_login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search | UK E-Sports League</title>
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
                        <a class="nav-link" href="view_participants_edit_delete.php">
                            <i class="bi bi-people me-1"></i> Participants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="search_form.php">
                            <i class="bi bi-search me-1"></i> Search
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
        <h1 class="text-center mb-5">
            <i class="bi bi-search me-2"></i>
            Search Database
        </h1>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-person-search me-2"></i>
                            Search Participants
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">Search for participants by email, first name, or surname.</p>
                        <form action="search_result.php" method="get" class="needs-validation" novalidate>
                            <input type="hidden" name="type" value="participant">
                            <div class="mb-4">
                                <label for="search_term" class="form-label">Search Term</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0">
                                        <i class="bi bi-search text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control" id="search_term" name="search_term" placeholder="Enter email, first name, or surname" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Partial matches will be included in results.
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a search term.
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-2"></i>
                                    Search Participants
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-people-fill me-2"></i>
                            Search Teams
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">Search for teams by team name and view all team members.</p>
                        <form action="search_result.php" method="get" class="needs-validation" novalidate>
                            <input type="hidden" name="type" value="team">
                            <div class="mb-4">
                                <label for="team_name" class="form-label">Team Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0">
                                        <i class="bi bi-search text-secondary"></i>
                                    </span>
                                    <input type="text" class="form-control" id="team_name" name="search_term" placeholder="Enter team name" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Partial matches will be included in results.
                                </div>
                                <div class="invalid-feedback">
                                    Please enter a team name.
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="bi bi-search me-2"></i>
                                    Search Teams
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="admin_menu.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Dashboard
            </a>
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
