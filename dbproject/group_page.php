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

if(isset($_POST["delEvent"])) {
	//echo "trying to delete";
	if($stmt = $mysqli->prepare("delete from attend where event_id=?")) {
	$stmt->bind_param("i", $_POST["delEvent"]); 
	$stmt->execute(); 
	$stmt->close();
}
	if($stmt = $mysqli->prepare("delete from events where event_id=?")) {
	$stmt->bind_param("i", $_POST["delEvent"]);
	$stmt->execute();
	$stmt->close();
	}	
	unset($_POST["delEvent"]);
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
		
	  if(isset($_POST["Authorize"])){
		if ($stmt = $mysqli->prepare("update belongs_to set authorized=1 where username=?")) {
          $stmt->bind_param("s", $_POST["Authorize"]);
          $stmt->execute();
          $stmt->close();		 		 
		  unset($_POST["Authorize"]);
		}
	  }
		
      if(isset($_POST["interest"])) {
        if ($stmt = $mysqli->prepare("insert into about (interest_name,group_id) values (?,?)")) {
          $stmt->bind_param("ss", $_POST["interest"], $id);
          $stmt->execute();
          $stmt->close();
    	  unset($_POST["interest"]);
		  header("refresh: 1; group_page.php?group_id=$id");
        }  
      }

      echo '<form action="group_page.php?group_id=';
      echo $id;
      echo '" method="POST">';	
 
      echo '<select name="interest">';
      if ($stmt = $mysqli->prepare("select interest_name from interest where (interest_name) not in (select interest_name from about where group_id = ?)")){
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
  else if (isset($_SESSION["username"]) && !isset($_POST['joining'])){
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
if (isset($_SESSION["username"]) && $stmt = $mysqli->prepare("select event_id,title,description,start_time,end_time,rsvp,username
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
	echo "</tr>";
  }
  echo "</table><br />\n";
  $stmt->close();
}

else if ($stmt = $mysqli->prepare("select distinct event_id,title,description,start_time,end_time
								   from events e natural left outer join attend
								   where group_id = ?
								   order by start_time
								")) {
  $stmt->bind_param("i", $_GET["group_id"]);
  $stmt->execute();
  $stmt->bind_result($id,$title,$description,$stime,$etime);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Event</td><td>Description</td><td>Start Time</td><td>End Time</td></tr><br />";
  while($stmt->fetch()) {
	$isRSVP = "no";
	echo "\n";
	echo "<tr>";
	echo "<td>$id</td>";
	echo "<td><a href='event_page.php?event_id=";
	echo $id;
	echo "'\>$title</a></td>";
	echo "<td>$description</td>";
	echo "<td>$stime</td>";
	echo "<td>$etime</td>";
	echo "</tr>";
  }
  echo "</table><br />\n";
  $stmt->close();


}

echo "Group Members:";
//print out all the members
if ($stmt = $mysqli->prepare("select username,authorized 
							  from belongs_to
							  where group_id = ? 
							  order by authorized desc")) {
  $stmt->bind_param("i", $_GET["group_id"]);//, $_SESSION["username"]);
  $stmt->execute();
  $stmt->bind_result($uname,$uauthor);
  echo '<table border="2" width="30%">';
  echo "<tr><td>Username</td><td>Interests</td><td>Groups</td><td>Events</td><td>Authorized?</td></tr>";
  while($stmt->fetch()) {
	$isauthorized = "no";
	if ($uauthor == 1){
			$isauthorized = "yes";
	}
	$name = nl2br(htmlspecialchars($name)); //nl2br function replaces \n and \r with <br />
	echo "<tr>";
	echo "<td>$uname</td>";
		
	echo "<td><a href='interests.php?username=";
	echo $uname;
	echo "'\>Interests</a></td>";
	
	echo "<td><a href='groups.php?username=";
	echo $uname;
	echo "'\>Groups</a></td>";

	echo "<td><a href='events.php?username=";
	echo $uname;
	echo "'\>Events</a></td>";
	if ($authorized == 1 && $isauthorized == "no"){
		echo "<td>";
		echo '<form action="group_page.php?group_id=';
		echo $_GET["group_id"];
		echo '" method="POST">';
		echo '<button name="Authorize" value=';
		echo $uname;
		echo '>Authorize</button>';
		echo "<br /><br />";
		echo '</form>';
		echo "</td>";
	}
	
	else{
		echo "<td>$isauthorized</td>";
	}
	
	echo "</tr>";
  }
  echo "</table>";
  echo "<br />";
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
	echo "<tr>";
	echo "<td>$gid</td>";
		
	echo "<td><a href='group_page.php?group_id=";
	echo $gid;
	echo "'\>$name</a></td>";
		
	echo "</tr>";
  }
  echo "</table>";
  echo "<br />";
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
