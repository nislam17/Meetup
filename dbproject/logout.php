<!DOCTYPE html>
<!-- Example Blog written by Raymond Mui -->
<html>
<title>Logout</title>

<?php
session_start();
session_destroy();
echo "You are logged out. You will be redirected in 3 seconds";
  header("refresh: 3; index.php");
?>

</html>