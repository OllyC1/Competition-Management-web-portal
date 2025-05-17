<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>search result</title>
    <style>
          body{
            font-family: Arial, Helvetica, sans-serif;
            padding: 10px;
            margin: 10px;
            background-color: black;
			display: flex;
      flex-direction: column;
             
        }
		h4{
		text-align:center;
		color: Blue;
			line-height: 20px;	
			
		}
		p{
		 color: white;
		}
        h1{
            text-align: center;
            color: white;
            text-decoration:underline;
        }
		h3{
		 color: white;
		}
		h2{
		 color: white;
		}
        a.button {
            
            padding:5px;
            background-color:green;
            color:white;
            border-radius:3px;
            margin-top:3px;
            display:block;
            width:130px;
            text-decoration:none;
			 
        }
        a.dbutton {
        
            padding:5px;
            background-color:red;
            color:white;
            border-radius:3px;
            margin-top:3px;
            display:block;
            width:130px;
            text-decoration:none;
			
        }
	 table, th, td {
  border: 1px solid white;
  border-collapse: collapse;
}
        table{
            color: white;
        }
        td{
            text-align: center;
        }
    </style>
	
   
</head>
<body>
 
    
<a href="admin_menu.php">Back to Admin portal</a>

<?php
// Database connection details
include "dbconnect.php";
// Connect to the database
$conn = mysqli_connect($host, $username, $password, $database);

// Check if the connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Get the search terms from the form
$search_term_1 = mysqli_real_escape_string($conn, $_POST['firstname']);
$search_term_2 = mysqli_real_escape_string($conn, $_POST['club']);

// Search for the first thing in the first table
if (!empty($search_term_1)) {
  $sql1 = "SELECT * FROM participant WHERE firstname LIKE '%$search_term_1%'";
  $result1 = mysqli_query($conn, $sql1);

  // Output the results for the first search
  if (mysqli_num_rows($result1) > 0) {
    echo "<h2>Results for the first search:</h2>";
    while ($row = mysqli_fetch_assoc($result1)) {
        echo "<table>";
        
          echo  "<tr><th>ID</th><th>Firstname</th><th>Surname</th><th>Email</th><th>power_output(Watts)</th><th>Distance(kM</th><th>club_id</th></tr>";
        

        echo '<tr>';
                    echo '<td>'. $row['id'] . '</td>';
                    echo '<td>'. $row['firstname'] . '</td>';
                    echo '<td>'. $row['surname'] . '</td>';
                    echo '<td>'. $row['email'] . '</td>';
                    echo '<td>'. $row['power_output'] . '</td>';
                    echo '<td>'. $row['distance'] . '</td>';
                    echo '<td>'. $row['club_id'] . '</td>';
                    
                    
                    echo '</tr>';
              echo "</table>" ;      
    }
  } else {
    echo "<h4>No results found for the first search.</h4>";
  }
}

// Search for the second thing in the second table
if (!empty($search_term_2)) {
 

        // Fetch club information based on the search query
        $clubSql = "SELECT * FROM club WHERE name LIKE '%$search_term_2%'";
        $clubResult = $conn->query($clubSql);

        if ($clubResult->num_rows > 0) {
            while ($clubRow = $clubResult->fetch_assoc()) {
                $clubId = $clubRow['id'];
                $clubName = $clubRow['name'];
                echo "<h2>$clubName</h2>";

                // Fetch participants associated with the club
                $participantSql = "SELECT * FROM participant WHERE club_id = '$clubId'";
                $participantResult = $conn->query($participantSql);
                $participants = [];

                if ($participantResult->num_rows > 0) {
                    while ($participantRow = $participantResult->fetch_assoc()) {
                        $participants[] = $participantRow;
                    }

                    // Calculate total distance traveled and power output
                    $totalDistance = 0;
                    $totalPower = 0;

                    foreach ($participants as $participant) {
                        $totalDistance += $participant['distance'];
                        $totalPower += $participant['power_output'];
                    }

                    // Calculate average distance traveled and power output
                    $count = count($participants);
                    $averagePower = $totalPower / $count;
					 $averageDistance = $totalDistance / $count;
						 echo "<table>";

                    // Display the total and average data
                    echo "<p><strong>Total Distance Traveled:</strong> $totalDistance</p>";
                    echo "<p><strong>Total Power Output:</strong> $totalPower</p>";
                    echo "<p><strong>Average Distance Traveled:</strong> $averageDistance</p>";
                    echo "<p><strong>Average Power Output:</strong> $averagePower</p>";

                    // Display the participant information
                    echo "<h3>Participants</h3>";
                  
						 echo "<table>";
        
          echo  "<tr><th>ID</th><th>Firstname</th><th>Surname</th><th>Distance travelled(KM)</th><th>Power output(Watts)</th></tr>";
        
                   

                    foreach ($participants as $participant) {
						$participantId = $participant['id'];
                        $participantFirstName = $participant['firstname'];
                        $participantSurname = $participant['surname'];
                        $participantDistance = $participant['distance'];
                        $participantPower = $participant['power_output'];

						 

        echo '<tr>';
						 echo '<td>'.$participantId. '</td>';
                    echo '<td>'.$participantFirstName. '</td>';
                    echo '<td>'. $participantSurname . '</td>';
                    echo '<td>'.$participantDistance. '</td>';
                    echo '<td>'. $participantPower. '</td>';
                    
                    
                        
					}
					  echo '</tr>';
              echo "</table>" ;    

                    
                } else {
                    echo "<h4>No participants associated with this club.</h4>";
                }
			}
		}
    }
    ?>

</body>
</html>