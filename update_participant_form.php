<?php
//one single echo statement to include the entire person update form
//this contains a hidden input field so that we know which person to update
echo '
<form action="update_participant.php" method="POST">
<input type="hidden" name="id" value="'.$id.'">
<p>Firstname</p>
        <input type="text" name= "firstname"  value="'.$firstname.'"  readonly>
        <p>Surname</p>
        <input type="text" name= "surname"  value="'.$surname.'" readonly>
        <p>Email</p>
        <input type="email" name= "email" value="'.$email.'" readonly>
        <p>Power Output</p>
        <input type="number" name= "power_output" value="'.$power_output.'">
        <p>Distance</p>
        <input type="number" name= "distance" value="'.$distance.'">
        <p>Club ID</p>
        <input type="number" name= "club_id"  value="'.$club_id.'"readonly >
        
        <br />
<input type="submit" value="Update">
</form>';
?>