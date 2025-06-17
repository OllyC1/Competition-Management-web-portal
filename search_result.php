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
$search_results = [];
$search_term = "";
$search_type = "";
$error_message = "";
$team_kd_ratio = 0;

// Check if search parameters are provided
if(isset($_GET["type"]) && isset($_GET["search_term"]) && !empty($_GET["search_term"])){
    $search_type = $_GET["type"];
    $search_term = $_GET["search_term"];
    
    // Perform search based on type
    if($search_type == "participant"){
        try {
            // Search for participant by email, firstname, or surname
            $sql = "SELECT p.*, t.name as team_name FROM participant p 
                LEFT JOIN team t ON p.team_id = t.id 
                WHERE p.email LIKE ? OR p.firstname LIKE ? OR p.surname LIKE ?";
            
            if($stmt = $conn->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $param_search = "%" . $search_term . "%";
                $stmt->bind_param("sss", $param_search, $param_search, $param_search);
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    
                    // Fetch results
                    while($row = $result->fetch_assoc()){
                        // Calculate K/D ratio
                        $row["kd_ratio"] = ($row["deaths"] > 0) ? round($row["kills"] / $row["deaths"], 2) : $row["kills"];
                        $search_results[] = $row;
                    }
                } else{
                    $error_message = "Error executing search query.";
                }
                
                // Close statement
                $stmt->close();
            }
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    } elseif($search_type == "team"){
        try {
            // Search for team by team name
            $sql = "SELECT t.*, p.id as player_id, p.firstname, p.surname, p.email, p.kills, p.deaths 
                FROM team t 
                LEFT JOIN participant p ON t.id = p.team_id 
                WHERE t.name LIKE ?";
            
            if($stmt = $conn->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $param_search = "%" . $search_term . "%";
                $stmt->bind_param("s", $param_search);
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    
                    $team_data = [];
                    $total_kills = 0;
                    $total_deaths = 0;
                    
                    // Fetch results
                    while($row = $result->fetch_assoc()){
                        // Store team info
                        if(!isset($team_data["team_info"])){
                            $team_data["team_info"] = [
                                "id" => $row["id"],
                                "team_name" => $row["name"],
                                "location" => $row["location"]
                            ];
                        }
                        
                        // Add player to team if player exists
                        if($row["player_id"]){
                            // Calculate player K/D ratio
                            $kd_ratio = ($row["deaths"] > 0) ? round($row["kills"] / $row["deaths"], 2) : $row["kills"];
                            
                            $team_data["players"][] = [
                                "id" => $row["player_id"],
                                "firstname" => $row["firstname"],
                                "surname" => $row["surname"],
                                "email" => $row["email"],
                                "kills" => $row["kills"],
                                "deaths" => $row["deaths"],
                                "kd_ratio" => $kd_ratio
                            ];
                            
                            // Add to team totals
                            $total_kills += $row["kills"];
                            $total_deaths += $row["deaths"];
                        }
                    }
                    
                    // Calculate team K/D ratio
                    $team_kd_ratio = ($total_deaths > 0) ? round($total_kills / $total_deaths, 2) : $total_kills;
                    $team_data["team_stats"] = [
                        "total_kills" => $total_kills,
                        "total_deaths" => $total_deaths,
                        "team_kd_ratio" => $team_kd_ratio
                    ];
                    
                    $search_results = $team_data;
                } else{
                    $error_message = "Error executing search query.";
                }
                
                // Close statement
                $stmt->close();
            }
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    } else {
        $error_message = "Invalid search type.";
    }
} else {
    $error_message = "Please provide a search term.";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | UK E-Sports League</title>
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
        <h1 class="text-center mb-4">
            <i class="bi bi-search me-2"></i>
            Search Results
        </h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="search-info flex-grow-1">
                        <h5 class="mb-0">
                            <i class="bi bi-filter me-2"></i>
                            Search Term: <span class="text-primary">"<?php echo htmlspecialchars($search_term); ?>"</span>
                        </h5>
                    </div>
                    <div>
                        <a href="search_form.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            New Search
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(!empty($error_message)): ?>
            <div class="alert alert-danger">
                <div class="d-flex">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                    <div>
                        <h5 class="alert-heading">Error</h5>
                        <p class="mb-0"><?php echo $error_message; ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if($search_type == "participant" && empty($error_message)): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-person-search me-2"></i>
                        Participant Search Results
                    </h3>
                </div>
                <div class="card-body">
                    <?php if(count($search_results) > 0): ?>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Found <?php echo count($search_results); ?> participant(s) matching your search.
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Surname</th>
                                        <th>Email</th>
                                        <th>Team</th>
                                        <th>Kills</th>
                                        <th>Deaths</th>
                                        <th>K/D Ratio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($search_results as $participant): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($participant["firstname"]); ?></td>
                                            <td><?php echo htmlspecialchars($participant["surname"]); ?></td>
                                            <td><?php echo htmlspecialchars($participant["email"]); ?></td>
                                            <td><?php echo htmlspecialchars($participant["team_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($participant["kills"]); ?></td>
                                            <td><?php echo htmlspecialchars($participant["deaths"]); ?></td>
                                            <td>
                                                <span class="badge bg-primary p-2">
                                                    <?php echo $participant["kd_ratio"]; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                                <div>
                                    <h5 class="alert-heading">No Results Found</h5>
                                    <p class="mb-0">No participants found matching "<?php echo htmlspecialchars($search_term); ?>". Try a different search term.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif($search_type == "team" && empty($error_message)): ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Team Search Results
                    </h3>
                </div>
                <div class="card-body">
                    <?php if(isset($search_results["team_info"])): ?>
                        <div class="card mb-4 bg-dark-lighter">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="mb-2">
                                            <?php echo htmlspecialchars($search_results["team_info"]["team_name"]); ?>
                                        </h4>
                                        <p class="mb-0">
                                            <i class="bi bi-geo-alt me-2"></i>
                                            <?php echo htmlspecialchars($search_results["team_info"]["location"]); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-card mb-0">
                                            <div class="stat-value"><?php echo $search_results["team_stats"]["team_kd_ratio"]; ?></div>
                                            <div class="stat-label">Team K/D Ratio</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-dark-lighter h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Kills</h5>
                                        <div class="display-4 text-primary">
                                            <?php echo $search_results["team_stats"]["total_kills"]; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-dark-lighter h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Deaths</h5>
                                        <div class="display-4 text-danger">
                                            <?php echo $search_results["team_stats"]["total_deaths"]; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-dark-lighter h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Team Members</h5>
                                        <div class="display-4 text-secondary">
                                            <?php echo isset($search_results["players"]) ? count($search_results["players"]) : 0; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(isset($search_results["players"]) && count($search_results["players"]) > 0): ?>
                            <h5 class="mb-3">
                                <i class="bi bi-people me-2"></i>
                                Team Members
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Surname</th>
                                            <th>Email</th>
                                            <th>Kills</th>
                                            <th>Deaths</th>
                                            <th>K/D Ratio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($search_results["players"] as $player): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($player["firstname"]); ?></td>
                                                <td><?php echo htmlspecialchars($player["surname"]); ?></td>
                                                <td><?php echo htmlspecialchars($player["email"]); ?></td>
                                                <td><?php echo htmlspecialchars($player["kills"]); ?></td>
                                                <td><?php echo htmlspecialchars($player["deaths"]); ?></td>
                                                <td>
                                                    <span class="badge bg-primary p-2">
                                                        <?php echo $player["kd_ratio"]; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                No players found for this team.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                                <div>
                                    <h5 class="alert-heading">No Results Found</h5>
                                    <p class="mb-0">No teams found matching "<?php echo htmlspecialchars($search_term); ?>". Try a different search term.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="search_form.php" class="btn btn-primary me-2">
                <i class="bi bi-search me-2"></i>
                New Search
            </a>
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
</body>
</html>
