<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View participants</title>
</head>
<body>
<style>
	
        body{
            font-family: Arial, Helvetica, sans-serif;
            padding: 10px;
            margin: 10px;
            background-color: black;
			display: flex;
      flex-direction: column;
             
        }
        h1{
            text-align: center;
            color: white;
            text-decoration:underline;
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
    <a href="admin_menu.php">Back to Admin Portal</a>
    <h1>View all of the participants for edit or delete</h1>
    
    <table>
        <tr>
            <th>ID</th><th>Firstname</th><th>Surname</th><th>Email</th><th>Power Output</th><th>Distance</th><th>Club ID</th><th>Update participant</th><th>Delete Participant</th>
    <?php
        
    //including connection variables - remember to update these if you are using XAMPP    
    include 'dbconnect.php';
        
        try {
            $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password); //building a new connection object
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            //TODO SELECT - view the participants with links to edit or delete them. 
             //Selecting multiple rows from a MySQL database using the PDO::query function.
            //SOrting the data by the ID field and limiting the results to the last 50 for the purpose of this example
            $sql = "SELECT * FROM participant ORDER BY id";
            
            //For each result that we return, loop through the result and perform the echo statements.
            //Data will be formatted into a table
            //$row is an array with the fields for each record returned from the SELECT
                foreach($conn->query($sql, PDO::FETCH_ASSOC) as $row){
                    echo '<tr>';
                    echo '<td>'. $row['id'] . '</td>';
                    echo '<td>'. $row['firstname'] . '</td>';
                    echo '<td>'. $row['surname'] . '</td>';
                    echo '<td>'. $row['email'] . '</td>';
                    echo '<td>'. $row['power_output'] . '</td>';
                    echo '<td>'. $row['distance'] . '</td>';
                    echo '<td>'. $row['club_id'] . '</td>';
                    
                   
                    //Updated script now includes a link to update or delete the person
                    //The link passes over the id of the person to the URL, to use on either the delete or the update script. 
                    echo '<td><a href="update_participant.php?id='.$row['id'].'" class="button">Update this participant</a></td>';
                    echo '<td><a href="delete_participant.php?id='.$row['id'].'" class="dbutton" onclick="return confirm(\'Are you sure you want to delete this participant?\');">Delete this participant</a></td>';
                    
                    echo '</tr>';
                }
            
            }
            
        catch(PDOException $e)
            {
            echo $e->getMessage(); //If we are not successful we will see an error
            }
        ?>
</table> 

</body>
</html>