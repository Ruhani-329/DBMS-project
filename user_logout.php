<?php
session_start();
session_unset();
session_destroy();
header("Location: welcome.html"); // Replace with your welcome page file name
exit();
?>
