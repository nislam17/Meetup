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
	echo "$username's events <br />\n";
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
  echo 'These are your events.<br />'; // You may click <a href="post.php">here</a> to post.<br />';
}

echo "<br />View Events by:";
echo '<form action="events.php?username=';
echo $username;
echo '" method="POST">';	
echo '<select name="sorted">;
  <option value="Upcoming" selected="selected" >Upcoming</option>
  <option value="Past">Past</option>
  <option value="Group">Group</option>
  <option value="Event Name">Event Name</option>
</select>';
echo '<input type="submit" value="Update" />';
echo '</form>';
echo "<br />";

if(isset($_POST["sorted"]) && $_POST["sorted"] == "Past"){
	$stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name 
							  from (events e join groups using (group_id)) join attend a using (event_id) 
							  where a.username = ? and (end_time < UTC_TIMESTAMP() - interval '5' hour)
							  order by start_time");
	echo $_POST["sorted"];
}
else if(isset($_POST["sorted"]) && $_POST["sorted"] == "Group"){
	$stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name 
							  from (events e join groups using (group_id)) join attend a using (event_id) 
							  where a.username = ? and ((start_time > UTC_TIMESTAMP() - interval '5' hour) or (end_time > UTC_TIMESTAMP() - interval '5' hour))
							  order by group_id,start_time");
	echo $_POST["sorted"];
}
else if(isset($_POST["sorted"]) && $_POST["sorted"] == "Event Name"){
	$stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name 
							  from (events e join groups using (group_id)) join attend a using (event_id) 
							  where a.username = ? and ((start_time > UTC_TIMESTAMP() - interval '5' hour) or (end_time > UTC_TIMESTAMP() - interval '5' hour))
							  order by title");
	echo $_POST["sorted"];
}
else{
	$stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name 
							  from (events e join groups using (group_id)) join attend a using (event_id) 
							  where a.username = ? and ((start_time > UTC_TIMESTAMP() - interval '5' hour) or (end_time > UTC_TIMESTAMP() - interval '5' hour))
							  order by start_time");
	echo "Upcoming";
}

//print out all the user's events
if ($stmt) {
  $stmt->bind_param("s", $_GET["username"]);
  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime,$gid,$group);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td><td>Group</td></tr><br />";
  while($stmt->fetch()) {
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

echo "Events in your area"; 
if ($stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name 
							  from (events e join groups using (group_id)) join attend a using (event_id) 
							  where zip = (select zipcode from member where username = ?) and event_id not in (select event_id from attend where username = ?)")) {
$stmt->bind_param("ss", $_SESSION["username"], $_SESSION["username"]); 
$stmt->execute(); 
  $stmt->bind_result($id,$title,$description,$stime,$etime,$gid,$group);
echo '<table border = "2" width = 30%">'; 
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td><td>Group</td></tr><br />";
while ($stmt->fetch()) {
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
echo "</table>";
echo "<br />";
$stmt->close();
}

echo '<a href="index.php">Go back</a><br /><br />';

$mysqli->close();
?>

</html>
