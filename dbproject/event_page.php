<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include ("include.php");

//check if the group exists and prints out group, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select * from events where event_id = ?")) {
  $stmt->bind_param("i", $_GET["event_id"]);//, $_GET["group_name"]);
  $stmt->execute();
  $stmt->bind_result($id,$name,$description,$stime,$etime,$gid,$lname,$zip);
  if($stmt->fetch()) {
	$name = htmlspecialchars($name);
	echo "<title>[$id] $name</title>\n";
	//echo "<h1>$name</h1>\n";
  }
  else {
    echo "Event not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

if(!isset($_SESSION["username"])) {
  echo '<a href="login.php">Login</a> or <a href="register.php">register</a><br /><br />.';
}

if ($stmt = $mysqli->prepare("select group_name from groups where group_id = ?")) {
  $stmt->bind_param("i", $gid);//, $_GET["group_name"]);
  $stmt->execute();
  $stmt->bind_result($gname);
  if($stmt->fetch()) {
	echo "<h1>(<a href='group_page.php?group_id=";
	echo $gid;
	echo "'\>$gname</a>";
	echo ") $name</h1>";
  }
  $stmt->close();
}

echo "$description <br />";
echo "From $stime to $etime <br />";
echo "at $lname ($zip) <br /><br />";

if(isset($_POST["rsvp"])){
	if ($stmt = $mysqli->prepare("insert into attend (event_id, username, rsvp) values (?,?,1)")) {
      $stmt->bind_param("is", $_GET["event_id"], $_SESSION["username"]);
      $stmt->execute();
      $stmt->close();
	}
}

if ($stmt = $mysqli->prepare("select rsvp from attend join events using (event_id) where event_id = ? and username = ?")) {
  $stmt->bind_param("is", $_GET["event_id"], $_SESSION["username"]);
  $stmt->execute();
  $stmt->bind_result($rsvp);
  if($stmt->fetch()){
	//echo "You are in this event's group <br />";
	if($rsvp == 1){
		echo "You are RSVP'd for this event <br />";
		//echo "<a href='createevent.php?group_id=";
		//echo $id;
		//echo "'\>Create Event</a>";
	}
  }
  else if (!isset($_POST['rsvp'])){
	echo "You are not RSVP'd.";
	echo '<form action="event_page.php?event_id=';
	echo $id;
	echo '" method="POST">';
	echo '<button name="rsvp" value=1>RSVP</button>';
    echo "<br />";
	echo '</form>';
  }
  $stmt->close();
}

echo "<br />Potential Time Conflicts: <br />";
//print out all the events for this group
if ($stmt = $mysqli->prepare("select event_id,title,start_time,end_time
								from (events e natural join attend)
								where username = ? and (event_id,username) not in (
									select event_id,username
									from events natural join attend
									where e.start_time > end_time or e.end_time < start_time or event_id = ?
								)")) {
  $stmt->bind_param("si", $_SESSION["username"], $_GET["event_id"]);
  $stmt->execute();
  $stmt->bind_result($id,$title,$stime,$etime);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Start Time</td><td>End Time</td></tr><br />";
  while($stmt->fetch()) {
	//$name = nl2br(htmlspecialchars($name)); //nl2br function replaces \n and \r with <br />
	//$time = htmlspecialchars($time);
	//echo '<table border="2" width="30%"><tr><td>';
	echo "\n";
	echo "<tr>";
	echo "<td>$id</td>";
	echo "<td><a href='event_page.php?event_id=";
	echo $id;
	echo "'\>$title</a></td>";
	echo "<td>$stime</td>";
	echo "<td>$etime</td>";
	//echo $id;
	echo "</tr>";
	//echo "</td></tr></table><br />\n";
  }
  echo "</table><br />\n";
  $stmt->close();
}

if(isset($_SESSION["username"])){
  echo '<a href="groups.php?username=';
  echo htmlspecialchars($_SESSION["username"]);
  echo '">My Groups</a><br />';
}

echo '<a href="index.php">Go back</a><br /><br />';


$mysqli->close();
?>

</html>