<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include ("include.php");

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

if(!isset($_SESSION["username"])) {
  echo '<a href="login.php">Login</a> or <a href="register.php">register</a><br /><br />.';
}

if(isset($_POST["joining"])){
	if ($stmt = $mysqli->prepare("insert into belongs_to (group_id, username, authorized) values (?,?,0)")) {
      $stmt->bind_param("is", $_GET["group_id"], $_SESSION["username"]);
      $stmt->execute();
      $stmt->close();
	}
}

if ($stmt = $mysqli->prepare("select authorized from belongs_to where group_id = ? and username = ?")) {
  $stmt->bind_param("is", $_GET["group_id"], $_SESSION["username"]);
  $stmt->execute();
  $stmt->bind_result($authorized);
  if($stmt->fetch()){
	echo "You are in this group <br />";
	if($authorized == 1){
		echo "You are authorized <br />";
		echo "<a href='createevent.php?group_id=";
		echo $id;
		echo "'\>Create Event</a><br />";
	}
  }
  else if (!isset($_POST['joining']) && isset($_SESSION["username"])){
	echo "You are not in this group.";
	echo '<form action="group_page.php?group_id=';
	echo $id;
	echo '" method="POST">';
	echo '<button name="joining" value=1>Join Group</button>';
    echo "<br />";
	echo '</form>';
  }
  $stmt->close();
}

//print out all the events for this group
if ($stmt = $mysqli->prepare("select event_id,title,description,start_time,end_time,rsvp,username
							  from events natural left outer join attend
							  where group_id = ? and (username = ? or (event_id,username) in 
								(select event_id,max(username)
								from attend
								where username != ?
                                group by username)) or username is null
                              ")) {
  $stmt->bind_param("iss", $_GET["group_id"], $_SESSION["username"], $_SESSION["username"]);
  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime,$rsvp,$uname);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td><td>RSVP'd?</td></tr><br />";
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
	echo "<td>$isRSVP</td>";
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