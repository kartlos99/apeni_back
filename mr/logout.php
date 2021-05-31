<?php
session_start();
session_unset();
if(session_destroy()) // Destroying All Sessions
{
    header("Location: login.php"); // Redirecting To Home Page
}
