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

// Fetch all participants
$sql = "SELECT p.*, t.name as team_name FROM participant p 
        LEFT JOIN team t ON p.team_id = t.id 
        ORDER BY p.firstname, p.surname";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Participants | UK E-Sports League</title>
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
                        <a class="nav-link" href="search_form.php">
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
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Participants Management
                    </h2>
                    <a href="admin_menu.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Dashboard
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Surname</th>
                                <th>Email</th>
                                <th>Team</th>
                                <th>Kills</th>
                                <th>Deaths</th>
                                <th>K/D Ratio</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    // Calculate K/D ratio
                                    $kd_ratio = ($row["deaths"] > 0) ? round($row["kills"] / $row["deaths"], 2) : $row["kills"];
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["firstname"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["surname"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["team_name"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["kills"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["deaths"]) . "</td>";
                                    echo "<td>" . $kd_ratio . "</td>";
                                    echo "<td class='text-nowrap'>";
                                    echo "<a href='edit_participant_form.php?id=" . $row["id"] . "' class='btn btn-primary btn-sm me-1'><i class='bi bi-pencil'></i> Edit</a>";
                                    echo "<a href='delete.php?id=" . $row["id"] . "' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i> Delete</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No participants found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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
</body>
</html>
