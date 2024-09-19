<?php
session_start(); // Start the session

// Destroy the session
session_destroy();

// Redirect to the login page or homepage
header("Location: login.php");
exit();
