<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include "include.php";

//check if the group exists and prints out group, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select * from events where event_id = ?")) {
  $stmt->bind_param("i", $_GET["event_id"]);//, $_GET["group_name"]);
  $stmt->execute();
  $stmt->bind_result($id,$name,$description,$stime,$etime,$gid,$lname,$zip);
  if($stmt->fetch()) {
	$name = htmlspecialchars($name);
	echo "<title>Update [$id] $name</title>\n";
	echo "<h1>$name</h1>\n";
  }
  else {
    echo "Event not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

if(isset($_SESSION["username"]) && $stmt = $mysqli->prepare("select authorized from belongs_to where group_id = ? and username = ?")) {
  $stmt->bind_param("is", $gid, $_SESSION["username"]);
  $stmt->execute();
  $stmt->bind_result($authorized);
  if($stmt->fetch() && $authorized == 1){}
  else{
	echo "You are not authorized to edit this event. ";
	echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
	//header("refresh: 3; index.php");
  }
  $stmt->close();
}

//if the user is not logged in, redirect them back to homepage
if(!isset($_SESSION["username"])) {
  echo "You are not logged in. ";
  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}


else if ($authorized == 1){

  //if the user have entered an event, insert it into database

   if(isset($_POST["eventname"]) && isset($_POST["description"]) && isset($_POST["stime"]) && isset($_POST["etime"])) {
	
    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("update events set title=?,description=?,start_time=?,end_time=?,lname=?,zip=? where event_id = ?")) {
	
	  $values = explode('|',$_POST["location"]);
      $stmt->bind_param("sssssii", $_POST["eventname"], $_POST["description"], $_POST["stime"], $_POST["etime"], $values[0], $values[1], $id);
      $stmt->execute();
      $stmt->close();


	  //$username = htmlspecialchars($_SESSION["username"]);

	  echo "The event was updated. \n";
      //echo "You will be returned to your homepage in 3 seconds or click <a href=\"view.php?username=$username\">here</a>.";
      //header("refresh: 3; view.php?username=$username");
	  echo "You will be returned to the event page in 3 seconds or click";		
	  echo "<a href='event_page.php?event_id=";
	  echo $id;
	  echo "'\> here</a>";

      header("refresh: 3; event_page.php?event_id=$id");

    }  
  }
 
   //if not then display the form for posting message
  else {
    echo "Event Name: <br /><br />\n";
    echo '<form action="update_event.php?event_id=';
	echo $id;
	echo '" method="POST">';	
    echo '<input type="text" textarea cols="20" rows="1" name="eventname" value="';
	echo htmlspecialchars($name);
	echo '" /></textarea><br />';
    echo "<br />";
	
	echo "Description: <br /><br />\n";
    echo '<input type="text" size="100" name="description" value="';
	echo htmlspecialchars($description);
	echo '" /></textarea><br />';
    echo "<br />";

	echo "Start Time: <br /><br />\n";
	echo htmlspecialchars($stime);
	echo "<br />";
    echo '<input type="datetime-local" name="stime" value="';
	echo htmlspecialchars($stime);
	echo '" /></textarea><br />';
    echo "<br />";
	
	echo "End Time: <br /><br />\n";
	echo htmlspecialchars($etime);
	echo "<br />";
    echo '<input type="datetime-local" name="etime" value="';
	echo htmlspecialchars($etime);
	echo '" /></textarea><br />';
    echo "<br />";
	
	echo "Location: <br /><br />";
	echo '<select name="location">';
	$stmt = $mysqli->prepare("select lname,zip from location");
    $stmt->execute();
    $stmt->bind_result($nlname,$nzip);
    while($stmt->fetch()){
		echo '<option value="';
		echo $nlname;
		echo '|';
		echo $nzip;
		echo '"';
		if($nlname == $lname && $nzip == $zip){
			echo ' selected="selected"';
		}
		echo '>';
		echo $nlname;
		echo " (";
		echo $nzip;
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
    echo '<a href="event_page.php?event_id=';
    echo $id;
    echo '">Go back</a><br />';

  }
}
$mysqli->close();
?>

</html>
