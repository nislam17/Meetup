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
	echo "<title>$username's groups</title>\n";
	echo "$username's groups <br />\n";
  }
  else {
    echo "Groups not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

//check if the user is also the one who is logged in
if(isset($_SESSION["username"]) && $_SESSION["username"] == $_GET["username"]) {
  echo 'These are your groups.<br />'; // You may click <a href="post.php">here</a> to post.<br />';
  echo "\n";
}

//print out all the user's groups
if ($stmt = $mysqli->prepare("select group_id,group_name from groups join belongs_to b using (group_id) where b.username = ?")) {
  $stmt->bind_param("i", $_GET["username"]);
  $stmt->execute();
  $stmt->bind_result($id,$name);
  echo '<table border="2" width="30%">';
  echo "<tr><td>Group ID</td><td>Group Name</td></tr><br />";
  while($stmt->fetch()) {
	$name = nl2br(htmlspecialchars($name)); //nl2br function replaces \n and \r with <br />
	//$time = htmlspecialchars($time);
	//echo '<table border="2" width="30%"><tr><td>';
	echo "\n";
	echo "<tr><td>$id</td><td><a href='group_page.php?group_id=";
	echo $id;
	echo "'\>$name</a></td></tr><br />";
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