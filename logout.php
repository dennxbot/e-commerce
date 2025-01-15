<?php
session_start();
include('includes/functions.php'); // Include the functions file

session_destroy();
redirect('login.php');
?>