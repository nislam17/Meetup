<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Register</title>

<?php

include "include.php";

//if the user is already logged in, redirect them back to homepage
if(isset($_SESSION["username"])) {
  echo "You are already logged in. ";
  echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.";
  header("refresh: 3; index.php");
}
else {
  //if the user have entered _all_ entries in the form, insert into database
  if(isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["zip"]) && isset($_POST["username"]) && isset($_POST["password"])) {

    //check if username already exists in database
    if ($stmt = $mysqli->prepare("select username from member where username = ?")) {
      $stmt->bind_param("s", $_POST["username"]);
      $stmt->execute();
      $stmt->bind_result($username);
        if ($stmt->fetch()) {
          echo "That username already exists. ";
          echo "You will be redirected in 3 seconds or click <a href=\"register.php\">here</a>.";
          header("refresh: 3; register.php");
		  $stmt->close();
        }
		//if not then insert the entry into database, note that user_id is set by auto_increment
		else {
		    $stmt->close();
		    if ($stmt = $mysqli->prepare("insert into member (username,password,firstname,lastname,zip) values (?,?,?,?,?)")) {
              $stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]), $_POST["firstname"], $_POST["lastname"], $_POST["zip"]);
              $stmt->execute();
              $stmt->close();
              echo "Registration complete, click <a href=\"index.php\">here</a> to return to homepage."; 
          }		  
        }	 
	}
  }
  //if not then display registration form
  else {
    echo "Enter your information below: <br /><br />\n";
    echo '<form action="register.php" method="POST">';
    echo "\n";	
	echo 'First Name: <input type="text" name="firstname" /><br />';
    echo "\n";
    echo 'Last Name: <input type="text" name="lastname" /><br />';
    echo "\n";
	echo 'Zip Code: <input type="text" name="zip" /><br />';
    echo "\n";
    echo 'Username: <input type="text" name="username" /><br />';
    echo "\n";
	echo 'Password: <input type="password" name="password" /><br />';
    echo "\n";
	echo '<input type="submit" value="Submit" />';
    echo "\n";
	echo '</form>';
	echo "\n";
	echo '<br /><a href="index.php">Go back</a>';

  }
}
$mysqli->close();


?>


</html>