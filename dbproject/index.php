<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Meetup</title>

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

  
  echo '<a href="events.php?username=';
  echo htmlspecialchars($_SESSION["username"]);
  echo '">My Upcoming Events</a><br />';
  
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

if (isset($_SESSION["username"]) && $stmt = $mysqli->prepare("select event_id,title,e.description,start_time,end_time,group_id,group_name,rsvp,a.username 
							  from (events e natural left outer join attend a) join groups using (group_id)
							  where a.username = ? or (not exists (select rsvp from attend where username = ? and event_id = e.event_id) && ((event_id,a.username) in 
								(select event_id,max(username)
								from attend
								where username != ?
                                group by username) or a.username is null))
							  ")) {
  $stmt->bind_param("sss", $_SESSION["username"], $_SESSION["username"], $_SESSION["username"]);								  
  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime,$gid,$group,$rsvp,$uname);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td><td>Group</td><td>RSVP'd?</td></tr><br />";
  while($stmt->fetch()) {
	$isRSVP = "no";
	if (isset($rsvp) && $uname == $_SESSION["username"] && $rsvp == 1){
		$isRSVP = "yes";
	}
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
	echo "<td>$isRSVP</td>";
	echo "</tr>";
  }
  echo "</table><br />\n";
  $stmt->close();
}

else if ($stmt = $mysqli->prepare("select distinct event_id,title,e.description,start_time,end_time,group_id,group_name from events e join groups using (group_id)")) {

  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime,$gid,$group);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td><td>Group</td><td>RSVP'd?</td></tr><br />";
  while($stmt->fetch()) {
	$isRSVP = "no";
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
	echo "<td>$isRSVP</td>";
	echo "</tr>";
  }
  echo "</table><br />\n";
  $stmt->close();


}

?>

</html>