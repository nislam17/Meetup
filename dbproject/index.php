<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Blog Example</title>

<?php

include ("include.php");

if(!isset($_SESSION["username"])) {
  echo "Welcome to MeetUp, you are not logged in. <br /><br >\n";
  echo 'You may view the meetups listed below or select an interest to see the groups that share that interest. <a href="login.php">Login</a> or <a href="register.php">register</a>.';
  echo "\n";
}
else {
  $username = htmlspecialchars($_SESSION["username"]);
  echo "Welcome $username. You are logged in.<br /><br />\n";
  echo 'You may view the meetups listed below, <a href="view.php?username=';
  echo htmlspecialchars($_SESSION["username"]);
  echo '">view your upcoming events</a>, or <a href="creategroup.php">create a group</a>, or <a href="logout.php">logout</a>.';
  echo "\n";
}
echo "<br /><br />\n";
if ($stmt = $mysqli->prepare("select username, user_id from users order by username")) {
  $stmt->execute();
  $stmt->bind_result($username, $user_id);
  while ($stmt->fetch()) {
    echo '<a href="view.php?user_id=';
	echo htmlspecialchars($user_id);
	$username = htmlspecialchars($username);
	echo "\">$username's blog</a><br />\n";
  }
  $stmt->close();
  $mysqli->close();
}

?>

</html>