<?php
session_start(); // Start the session

// Destroy all sessions
session_unset();
session_destroy();

// Redirect to home page
header("Location: index");
exit();
?>