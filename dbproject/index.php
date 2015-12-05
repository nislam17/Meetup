<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Blog Example</title>

<?php

include ("include.php");

if(!isset($_SESSION["username"])) {
  echo "Welcome to MeetUp, you are not logged in. <br /><br >\n";
  //echo 'You may view the meetups listed below or select an interest to see the groups that share that interest. <a href="login.php">Login</a> or <a href="register.php">register</a>.';
  echo "\n";
}
else {
  $username = htmlspecialchars($_SESSION["username"]);
  echo "Welcome $username. You are logged in.<br /><br />\n";
  
  echo '<a href="groups.php?username=';
  echo htmlspecialchars($_SESSION["username"]);
  echo '">My groups</a><br />';

  
  echo '<a href="view.php?username=';
  echo htmlspecialchars($_SESSION["username"]);
  echo '">My upcoming events</a><br />';
  
  echo '<a href="creategroup.php">Create a group</a><br />';
  
 // echo '<a href="logout.php">Logout</a>';
  echo "\n";
}
if(!isset($_SESSION["username"])) {
  echo '<a href="login.php">Login</a> or <a href="register.php">register</a><br /><br />.';
}
else {
  echo '<a href="logout.php">Logout</a><br /><br />';
}

if ($stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name from events e join groups using (group_id)")) {
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
	echo "<td>$title</td>";
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


?>

</html>