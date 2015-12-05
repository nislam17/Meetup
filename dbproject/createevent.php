<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Post</title>

<?php

include "include.php";

//check if the group exists and prints out group, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select group_id,group_name from group where group_id = ?")) {
  $stmt->bind_param("is", $_GET["group_id"]);//, $_GET["group_name"]);
  $stmt->execute();
  $stmt->bind_result($id,$name);
  if($stmt->fetch()) {
	$name = htmlspecialchars($name);
	echo "<title>[$id] $name</title>\n";
	echo "$name<br />\n";
  }
  else {
    echo "Group not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

//if the user is not logged in, redirect them back to homepage
if(!isset($_SESSION["username"])) {
  echo "You are not logged in. ";
  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
else {
  //if the user have entered an event, insert it into database
  //DOES NOT WORK YET, 1 == 0 USED AS A SAFETY
   if(isset($_POST["eventname"]) && isset($_POST["description"]) && (1 == 0)) {

    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("insert into events (group_name, description, username) values (?,?,?)")) {
      $stmt->bind_param("sss", $_POST["groupname"], $_POST["description"], $_SESSION["username"]);
      $stmt->execute();
      $stmt->close();

	  $stmt = $mysqli->prepare("select group_id from groups where username = ? and (group_id,username) not in (select group_id,username from belongs_to)");
	  $stmt->bind_param("s", $_SESSION["username"]);
	  $stmt->execute();
	  $stmt->bind_result($id);
	  $stmt->fetch();
	  echo $id;
	  $stmt->close();
	  
	  $stmt = $mysqli->prepare("insert into belongs_to (group_id, username, authorized) values (?,?,1)");
      $stmt->bind_param("is", $id, $_SESSION["username"]);
	  $stmt->execute();
      $stmt->close();

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
    echo "Event Name: <br /><br />\n";
    echo '<form action="createevent.php?group_id="';
	echo $_GET["group_id"];
	echo '" method="POST">';	
    echo '<textarea cols="20" rows="1" name="eventname" /></textarea><br />';
    echo "<br />";
	
	echo "Description: <br /><br />\n";
    echo '<textarea cols="40" rows="20" name="description" /></textarea><br />';
    echo "<br />";

	echo "Start Time: <br /><br />\n";
    echo '<input type="datetime-local" name="stime" /></textarea><br />';
    echo "<br />";
	
	echo "End Time: <br /><br />\n";
    echo '<input type="datetime-local" name="etime" /></textarea><br />';
    echo "<br />";

	echo '<input type="submit" value="Submit" />';
    echo "<br />";
	echo '</form>';
	echo "<br />";
	
	echo "<br />";
    echo '<a href="group_page.php?group_id=';
    echo $_GET["group_id"];
    echo '">Go back</a><br />';

  }
}
$mysqli->close();
?>

</html>