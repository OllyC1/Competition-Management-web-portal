<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    
</head>
<body>
    <?php
        
        include 'dbconnect.php';
        
       
            try {

                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {

                $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password); //building a new connection object
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $username= $_POST['username'];
                $username= filter_var($username, FILTER_SANITIZE_STRING);
                $password=$_POST['password'];
                $password= filter_var($password, FILTER_SANITIZE_STRING);


                $sql = $conn->prepare("SELECT * FROM `user` WHERE username= :username AND password = :password ");
                $sql->bindParam(':username', $username);
                $sql->bindParam(':password', $password);
                $sql -> execute(); //execute the statement
                
               
               
              
                if($sql->rowCount()) 
                { //check if we have results by counting rows
                
                    //set session var
                    $_SESSION['login'] = 1;

                    //get the user information from the query
                    $row = $sql->fetch();

                    //Save session var of the ID of the person logged in, incase we need it later
                    $_SESSION['user_id'] = $row['id'];

                    //redirect the user to a restricted area
                    header("Location: admin_menu.php");
                    
                }
            else
                {
                    echo "Wrong username or password";
                }
            
        }
        else 
        {
            echo "You are here by mistake";
        }
    }
            catch(PDOException $e)
                {
                echo $e->getMessage(); //If we are not successful in connecting or running the query we will see an error
                }
        
       
        ?>


</body>
</html>