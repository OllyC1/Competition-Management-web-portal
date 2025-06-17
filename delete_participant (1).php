<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cit-E Cycling Hub - Delete Participant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        
        .success-message {
            color: #28a745;
            font-size: 18px;
            margin: 20px 0;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0056b3;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        include 'dbconnect.php';

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $id = $_GET['id'];
            
            $sql = $conn->prepare("DELETE FROM participant WHERE id = ? ");
            $sql->bindValue(1, $id);
            $sql->execute();
            
            echo '<div class="icon"><i class="fas fa-check-circle"></i></div>';
            echo '<div class="success-message">Participant #' . $id . ' has been successfully deleted</div>';
            echo '<a href="view_participants_edit_delete.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Participants List</a>';
        }
        catch(PDOException $e) {
            echo '<div class="error-message">' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
</body>
</html>
