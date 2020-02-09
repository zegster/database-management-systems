<?php
/* PHP 7.4.2 */
/* Start Session */
session_start();


/* Redirect back to login page if login information doesn't exist */ 
if(!isset($_SESSION['user_id']))
{
    header("Location: ./login.php");
}


?>