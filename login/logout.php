<?php
session_start();       
session_unset();      
session_destroy();     

// Redirect to login page (same directory)
header("Location: login.php");
exit();
