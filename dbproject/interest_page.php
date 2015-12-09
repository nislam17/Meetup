<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>

<?php

include ("include.php");

//check if the interest exists and prints out interest, if not redirects back to homepage
if ($stmt = $mysqli->prepare("select * from interest where interest_name = ?")) {
  $stmt->bind_param("s", $_GET["interest_name"]);
  $stmt->execute();
  $stmt->bind_result($iname);
  if($stmt->fetch()) {
	$name = htmlspecialchars($iname);
	echo "<title>$iname</title>\n";
	echo "<h1>$iname</h1>\n";
  }
  else {
    echo "Interest not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

if(!isset($_SESSION["username"])) {
  echo '<a href="login.php">Login</a> or <a href="register.php">register</a><br /><br />.';
}


//print out all the groups with this interest
if ($stmt = $mysqli->prepare("select group_id,group_name from groups natural join about where interest_name = ? group by group_name")) {
  $stmt->bind_param("s", $iname);
  $stmt->execute();
  $stmt->bind_result($gid,$gname);
  echo '<table border="2" width="30%">';
  echo "<tr><td>ID</td><td>Group</td></tr><br />";
  while($stmt->fetch()) {

    echo "<tr>";
	
	echo "<td>$gid</td>";
	echo "<td><a href='group_page.php?group_id=";
	echo $gid;
	echo "'\>$gname</a></td>";
	
	echo "</tr>";
  }
  echo "</table><br />\n";
  $stmt->close();
}

if(isset($_SESSION["username"])){
  echo '<a href="interests.php?username=';
  echo htmlspecialchars($_SESSION["username"]);
  echo '">My Interests</a><br />';
}

echo '<a href="index.php">Go back</a><br /><br />';


$mysqli->close();
?>

</html>