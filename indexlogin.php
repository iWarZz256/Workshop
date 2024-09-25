<?php session_start(); 


if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    include 'headerlogin.php'; 
} else {
    include 'header.php'; 
} ?>