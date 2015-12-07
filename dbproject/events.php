<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include ("include.php");

//check if the user exists and prints out username, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select username from member where username = ?")) {
  $stmt->bind_param("s", $_GET["username"]);
  $stmt->execute();
  $stmt->bind_result($username);
  if($stmt->fetch()) {
	$username = htmlspecialchars($username);
	echo "<title>$username's Events</title>\n";
	echo "$username's groups <br />\n";
  }
  else {
    echo "Events not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

//check if the user is also the one who is logged in
if(isset($_SESSION["username"]) && $_SESSION["username"] == $_GET["username"]) {
  echo 'These are your events.'; // You may click <a href="post.php">here</a> to post.<br />';
}

//print out all the user's events
if ($stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name from (events e join groups using (group_id)) join attend a using (event_id) where a.username = ?")) {
  $stmt->bind_param("s", $_GET["username"]);
  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime,$gid,$group);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td><td>Group</td></tr><br />";
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
	echo "<td>$description</td>";
	echo "<td>$stime</td>";
	echo "<td>$etime</td>";
	echo "<td><a href='group_page.php?group_id=";
	echo $gid;
	echo "'\>$group</a></td>";
	echo "</tr>";
  }
  echo "</table><br />\n";
  $stmt->close();
}

echo '<a href="index.php">Go back</a><br /><br />';
//echo "\n";

$mysqli->close();
?>

</html>