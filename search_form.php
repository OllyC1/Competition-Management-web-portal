<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register your interest</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://kit.fontawesome.com/db96ab1c41.js" crossorigin="anonymous"></script>
    <style>
	
		  @media screen and (min-width: 600px) {
          .wrapper {
            display: grid;
            grid-template-columns: 1fr 2fr;
            column-gap: 5%;
          }
	}
			   @font-face {
        font-family: olly;
        src: url(PartyDayDemoRegular.ttf);
      }
	
	 h1{
        text-align: center;
        font-size: 30px;
        font-family: olly;
		  line-height:15px;
      }
			  h2{
				  text-align: center;
				  margin-bottom: 50px;
			  }
     
      .container{
       height:50%;
        width:50%;
        display: flex;
      flex-direction: column;
      border: solid 1px rgb(93, 93, 223);
		  margin-top: 200px;
		  
     
      
      

      }
      form{
        padding-bottom: 10px;
      }
	i{
        color:black;
        font-size: 30px;
        margin-top: 5px;

      }
	 li{
        list-style-type: none;
      }
      button{
        margin-bottom: 5px;
        border-bottom: 5px;
        padding-bottom: 5px;
      }
		
	
	
	</style>

</head>

<body>
 <nav class="navbar navbar-expand-sm bg-primary">

<div class="container-fluid">
  
  <ul class="navbar-nav">
    <li class="nav-item">
      <a href="index.html"><i class="fa-solid fa-bars"></i></a>
       
  
      
    </li>
  </ul>
  <h1>Cit-E Cycling hub</h1>
</div>

</nav>
   
<div class="container">
	
  <div class="row">
    <div class="col-md-6 p-2">
    <h2>Search for  participant</h2>
    <form action="search_result.php" method="POST">
		<div class="form-group">
			<label for="firstname">Participant firstname or surname:</label>
       
        <input type="text" name="firstname"class="form-control" id="firstname" placeholder="Enter your firstname/surname" required><br>
        <input type="hidden" name="participant" value="1">
		</div>
        <button type="submit" class="btn btn-success" >Submit</button>

    </form>
</div>
	  <div class="col-md-6 p-2">
    <h2>Search for a club / team</h2>
    <form action="search_result.php" method="POST">
		<div class="form-group">
			<label for="club">Club name:</label>
       
        <input type="text" name="club"class="form-control" id="club" placeholder="Enter your Club name" required><br>
        
		</div>
        <button type="submit" class="btn btn-success" >Submit</button>

    </form>
</div>

</div>
</div>
</body>
</html>