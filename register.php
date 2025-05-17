	<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register your interest</title>
	<style> 
		p{
		text-align:center;
		color: Blue;
			line-height: 20px;
		}
	
	
	</style>
</head>
<body>
    <?php
    //including connection variables  
    include 'dbconnect.php';

    //saving all of the user's POST values from the the form submission to local variables
    $firstname = $_POST['firstname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $terms = $_POST['terms'];
    

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password); //building a new connection object
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $conn->prepare("INSERT INTO interest (firstname, surname, email, terms)
            VALUES (:firstname, :surname, :email, :terms)");
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':surname', $surname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':terms', $terms);
            
            
            // use execute() to run the query
            $stmt->execute();
				 echo "<a href='index.html'>Back to Menu</a>";
				echo"<br>";
            echo " <p><strong>$firstname You have registered succesfully!</strong></p>"; 
				 echo "<P> One of our team members will be in touch soon</p>"; 
            // If successful we will see this
            // you could then check PHPMyAdmin to see if the record was inserted
            
                

            }
            catch(PDOException $e)
                {
                echo $e->getMessage(); //If we are not successful we will see an error

                }


























                //made you look
        
        
        ?>


</body>
</html>