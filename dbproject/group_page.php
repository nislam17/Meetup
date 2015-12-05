<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include ("include.php");

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

//check if the user is also the one who is logged in
//if(isset($_SESSION["username"]) && $_SESSION["username"] == $_GET["username"]) {
//  echo 'These are your groups. You may click <a href="post.php">here</a> to post.<br />';
//  echo "\n";
//}

//print out all the events for this group
if ($stmt = $mysqli->prepare("select event_id,title,description,start_time,end_time from events where group_id = ?")) {
   echo "I'm hereeeee";
  $stmt->bind_param("i", $_GET["group_id"]);
  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime);
  echo '<table border="2" width="30%">';
  echo "<tr><td>Event ID</td><td>Title</td><td>Description</td><td>Start Time</td><td>End Time</td></tr><br />";
  while($stmt->fetch()) {
	//$name = nl2br(htmlspecialchars($name)); //nl2br function replaces \n and \r with <br />
	//$time = htmlspecialchars($time);
	//echo '<table border="2" width="30%"><tr><td>';
	echo "\n";
	echo "<tr>";
	echo "<td>$id</td>";
	echo "<td>$title</td>";
	echo "<td>$description</td>";
	echo "<td>$stime</td>";
	echo "<td>$etime</td>";
	//echo $id;
	echo "</tr>";
	//echo "</td></tr></table><br />\n";
  }
  echo "</table><br />\n";
  $stmt->close();
}

echo '<a href="index.php">Go back</a><br /><br />';
echo "\n";

$mysqli->close();
?>

</html>