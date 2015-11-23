<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Login</title>

<?php

include "include.php";

//if the user is already logged in, redirect them back to homepage
if(isset($_SESSION["username"])) {
  echo "You are already logged in. \n";
  echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
else {
  //if the user have entered both entries in the form, check if they exist in the database
  if(isset($_POST["username"]) && isset($_POST["password"])) {

    //check if entry exists in database
    if ($stmt = $mysqli->prepare("select * from member where username = ? and password = ?")) {
      $stmt->bind_param("ss", $_POST["username"], md5($_POST["password"]));
      $stmt->execute();
      $stmt->bind_result($user_id, $username, $password);
	    //if there is a match set session variables and send user to homepage
        if ($stmt->fetch()) {
		  $_SESSION["user_id"] = $user_id;
		  $_SESSION["username"] = $username;
		  $_SESSION["password"] = $password;
		  $_SESSION["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"]; //store clients IP address to help prevent session hijack
          echo "Login successful. \n";
          echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.";
          header("refresh: 3; index.php");
        }
		//if no match then tell them to try again
		else {
		  sleep(1); //pause a bit to help prevent brute force attacks
		  echo "Your username or password is incorrect, click <a href=\"login.php\">here</a> to try again.";
		}
      $stmt->close();
	  $mysqli->close();
    }  
  }
  //if not then display login form
  else {
    echo "Enter your username and password below: <br /><br />\n";
    echo '<form action="login.php" method="POST">';
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
?>

</html>