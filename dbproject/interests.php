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
	echo "<title>$username's Interests</title>\n";
	echo "$username's interests <br />\n";
  }
  else {
    echo "Interests not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

//check if the user is also the one who is logged in
if(isset($_SESSION["username"]) && $_SESSION["username"] == $_GET["username"]) {
  echo 'These are your interests.<br /><br />';
 
  if(isset($_POST["interest"])) {
    //insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
    if ($stmt = $mysqli->prepare("insert into interested_in (interest_name,username) values (?,?)")) {
      $stmt->bind_param("ss", $_POST["interest"], $username);
      $stmt->execute();
      $stmt->close();
	  unset($iname);
    }  
  }

 
  echo '<form action="interests.php?username=';
  echo $_SESSION["username"];
  echo '" method="POST">';	
 
  echo '<select name="interest">';
  if ($stmt = $mysqli->prepare("select * from interest where (interest_name) not in (select interest_name from interested_in where username = ?)")){
	$stmt->bind_param("s", $username);
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

  
  
}



//print out all the user's interests
if ($stmt = $mysqli->prepare("select distinct interest_name from interested_in natural left outer join about where username = ?")) {
  $stmt->bind_param("s", $_GET["username"]);
  $stmt->execute();
  $stmt->bind_result($iname);
  echo '<table border="2" width="10%">';
  echo "<tr><td>Interest</td></tr><br />";
  while($stmt->fetch()) {
	  
	echo "<tr>";
		
	echo "<td><a href='interest_page.php?interest_name=";
	echo $iname;
	echo "'\>$iname</a></td>";
	
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