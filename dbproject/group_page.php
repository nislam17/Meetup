<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include ("include.php");

//check if the group exists and prints out group, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select group_id,group_name,description from groups where group_id = ?")) {
  $stmt->bind_param("i", $_GET["group_id"]);
  $stmt->execute();
  $stmt->bind_result($id,$name,$gdesc);
  if($stmt->fetch()) {
	$name = htmlspecialchars($name);
	echo "<title>[$id] $name</title>\n";
	echo "<h1>$name</h1>\n";
	echo $gdesc;
	echo "<br /><br />";
  }
  else {
    echo "Group not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

if ($stmt = $mysqli->prepare("select interest_name from groups natural join about where group_id = ?")) {
  $stmt->bind_param("i", $_GET["group_id"]);
  $stmt->execute();
  $stmt->bind_result($iname);
  echo "Group Interests: <br />";
  while($stmt->fetch()) {
	$iname = htmlspecialchars($iname);
    echo "<a href='interest_page.php?interest_name=";
	echo $iname;
	echo "'\>$iname</a>";
	echo "<br />";
  }
  echo "<br />";
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
	$stmt->close();
	if($authorized == 1){
		echo "You are authorized <br />";
		
		
      if(isset($_POST["interest"])) {
        //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
        if ($stmt = $mysqli->prepare("insert into about (interest_name,group_id) values (?,?)")) {
          $stmt->bind_param("ss", $_POST["interest"], $id);
          $stmt->execute();
          $stmt->close();
    	  unset($iname);
		  header("refresh: 1; group_page.php?group_id=$id");
        }  
      }

      echo '<form action="group_page.php?group_id=';
      echo $id;
      echo '" method="POST">';	
 
      echo '<select name="interest">';
      if ($stmt = $mysqli->prepare("select * from interest where (interest_name) not in (select interest_name from about where group_id = ?)")){
	    $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($iname);
        while($stmt->fetch()){
	 	  echo '<option value="';
 		  echo $iname;
		  echo '">';
		  echo $iname;
		  echo "</option>";
		  
  	    }
		
  	  echo "</select>";
      $stmt->close();
	  
      }
	  
    echo '<input type="submit" value="Add interest" />';
    echo "<br />";
    echo '</form>';
    echo "<br />";
						
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
  else{
	$stmt->close();
  }
}

//print out all the events for this group
if ($stmt = $mysqli->prepare("select event_id,title,description,start_time,end_time,rsvp,username
							  from events e natural left outer join attend
							  where group_id = ? and (username = ? or (not exists (select rsvp from attend where username = ? and event_id = e.event_id) && ((event_id,username) in 
								(select event_id,max(username)
								from attend
								where username != ?
                                group by event_id) or username is null))) order by start_time
                              ")) {
  $stmt->bind_param("isss", $_GET["group_id"], $_SESSION["username"], $_SESSION["username"], $_SESSION["username"]);
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

echo "You may also like these groups:";
//print out all the groups with similar interests
if ($stmt = $mysqli->prepare("select distinct group_id,group_name 
							  from groups natural join about
							  where ((interest_name) in 
								(select interest_name from groups natural join about where group_id = ?))
								and ((group_id) not in
								(select group_id from belongs_to where username = ? or group_id = ?))
							  order by group_name")) {
  $stmt->bind_param("isi", $_GET["group_id"], $_SESSION["username"], $_GET["group_id"]);
  $stmt->execute();
  $stmt->bind_result($gid,$name);
  echo '<table border="2" width="30%">';
  echo "<tr><td>Group ID</td><td>Group Name</td></tr>";
  while($stmt->fetch()) {
	$name = nl2br(htmlspecialchars($name)); //nl2br function replaces \n and \r with <br />
	//echo "\n";
	echo "<tr>";
	echo "<td>$gid</td>";
		
	echo "<td><a href='group_page.php?group_id=";
	echo $gid;
	echo "'\>$name</a></td>";
		
	echo "</tr>";
	//echo "</td></tr></table>";
  }
  echo "</table>";
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