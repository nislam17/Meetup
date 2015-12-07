<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Create Event</title>

<?php

include "include.php";

//check if the group exists and prints out group, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select group_id,group_name from groups where group_id = ?")) {
  $stmt->bind_param("i", $_GET["group_id"]);//, $_GET["group_name"]);
  $stmt->execute();
  $stmt->bind_result($id,$name);
  if($stmt->fetch()) {
	$name = htmlspecialchars($name);
	echo "<title>[$id] $name</title>\n";
	echo "<h1>$name</h1>\n";
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

   if(isset($_POST["eventname"]) && isset($_POST["description"]) && isset($_POST["stime"]) && isset($_POST["etime"])) {
	
	//echo $_POST["location"];

    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("insert into events (title, description, start_time, end_time, group_id, lname, zip ) values (?,?,?,?,?,?,?)")) {
     $values = explode('|',$_POST["location"]);
     $stmt->bind_param("ssssisi", $_POST["eventname"], $_POST["description"], $_POST["stime"], $_POST["etime"], $id, $values[0], $values[1]);
      $stmt->execute();
      $stmt->close();


	  //$username = htmlspecialchars($_SESSION["username"]);

	  echo "Your event was created. \n";
      //echo "You will be returned to your homepage in 3 seconds or click <a href=\"view.php?username=$username\">here</a>.";
      //header("refresh: 3; view.php?username=$username");
	  echo "You will be returned to the group page in 3 seconds or click";		
	  echo "<a href='group_page.php?group_id=";
	  echo $_GET["group_id"];
	  echo "'\> here</a>";

      header("refresh: 3; group_page.php?group_id=$id");

    }  
  }
 
   //if not then display the form for posting message
  else {
    echo "Event Name: <br /><br />\n";
    echo '<form action="createevent.php?group_id=';
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
	
	echo "Location: <br /><br />";
	echo '<select name="location">';
	$stmt = $mysqli->prepare("select lname,zip from location");
    $stmt->execute();
    $stmt->bind_result($lname,$zip);
    while($stmt->fetch()){
		echo '<option value="';
		echo $lname;
		echo '|';
		echo $zip;
		echo '">';
		echo $lname;
		echo " (";
		echo $zip;
		echo ")";
		echo "</option>";
	}
	echo "</select><br /><br />";
    $stmt->close();

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
