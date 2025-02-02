<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
} else {
    echo "Hiring functionality goes here.";
}
?>
