<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Post</title>

<?php

include "include.php";

//if the user is not logged in, redirect them back to homepage
if(!isset($_SESSION["username"])) {
  echo "You are not logged in. ";
  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
else {
  //if the user have entered a message, insert it into database
  if(isset($_POST["message"])) {

    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("insert into messages (user_id, text) values (?,?)")) {
      $stmt->bind_param("is", $_SESSION["user_id"], $_POST["message"]);
      $stmt->execute();
      $stmt->close();
	  $user_id = htmlspecialchars($_SESSION["user_id"]);
	  echo "Your message is posted. \n";
      echo "You will be returned to your blog in 3 seconds or click <a href=\"view.php?user_id=$user_id\">here</a>.";
      header("refresh: 3; view.php?user_id=$user_id");
    }  
  }
  //if not then display the form for posting message
  else {
    echo "Enter your message: <br /><br />\n";
    echo '<form action="post.php" method="POST">';
    echo "\n";	
    echo '<textarea cols="40" rows="10" name="message" />enter your message here</textarea><br />';
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