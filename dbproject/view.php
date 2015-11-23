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
	echo "<title>$username's blog</title>\n";
	echo "$username's blog: <br />\n";
  }
  else {
    echo "Blog not found. \n";
    echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
    header("refresh: 3; index.php");
  }
  $stmt->close();
}

//check if the user is also the one who is logged in
if(isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $_GET["user_id"]) {
  echo 'This is your blog. You may click <a href="post.php">here</a> to post.<br />';
  echo "\n";
}

echo '<a href="index.php">Go back</a><br /><br />';
echo "\n";

//print out all the messages from this user in a pretty table
if ($stmt = $mysqli->prepare("select text, time from messages where user_id = ? order by time desc")) {
  $stmt->bind_param("i", $_GET["user_id"]);
  $stmt->execute();
  $stmt->bind_result($text,$time);
  while($stmt->fetch()) {
	$text = nl2br(htmlspecialchars($text)); //nl2br function replaces \n and \r with <br />
	$time = htmlspecialchars($time);
	echo '<table border="2" width="30%"><tr><td>';
	echo "\n";
	echo "$time, $username wrote:</td></tr><tr><td><br />$text<br /><br /></td></tr></table><br />\n";
  }
  $stmt->close();
}
$mysqli->close();
?>

</html>