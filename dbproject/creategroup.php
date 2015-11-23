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
  if(isset($_POST["groupname"]) && isset($_POST["description"])) {

    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("insert into _group (group_name, description, username) values (?,?,?)")) {
      $stmt->bind_param("sss", $_POST["groupname"], $_POST["description"], $_SESSION["username"]);
      $stmt->execute();
      $stmt->close();
	  /*
	  $stmt = $mysqli->prepare(select group_id from _group where username = ? and (group_id,username) not in (select group_id,username from groupuser));
	  $stmt->bind_param("s", "$_SESSION["username"]);
	  $stmt->execute();
	  $stmt->bind_result($id);
	  $stmt->close();
	  
	  $stmt = $mysqli->prepare("insert into groupuser (group_id, username, authorized) values (?,?,1)")
      $stmt->bind_param("is", $id, $_SESSION["username"]);
	  $stmt->execute();
      $stmt->close();
*/
	  $username = htmlspecialchars($_SESSION["username"]);
	  echo "Your group was created. \n";
      //echo "You will be returned to your homepage in 3 seconds or click <a href=\"view.php?username=$username\">here</a>.";
      //header("refresh: 3; view.php?username=$username");
	  echo "You will be returned to your homepage in 3 seconds or click <a href=\"index.php\">here</a>.";
      header("refresh: 3; index.php");

    }  
  }
  //if not then display the form for posting message
  else {
    echo "Group Name: <br /><br />\n";
    echo '<form action="creategroup.php" method="POST">';
    echo "\n";	
    echo '<textarea cols="20" rows="1" name="groupname" /></textarea><br />';
    echo "\n";
	echo "Description: <br /><br />\n";
    //echo '<form action="creategroup.php" method="POST">';
    //echo "\n";	
    echo '<textarea cols="40" rows="20" name="description" /></textarea><br />';
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