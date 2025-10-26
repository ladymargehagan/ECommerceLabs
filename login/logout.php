<?php
session_start();       
session_unset();      
session_destroy();     

// redirect back to index page
header("Location: ../index.php");
exit();
