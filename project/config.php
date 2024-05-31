<?php
$servername = "localhost"; 
$username = "root"; 
$password = "";
$database = "tripleWProgramming"; 

$link = mysqli_connect($servername, $username, $password, $database);

mysqli_query($link, "SET NAMES utf8");

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
else{
    return $link;
}
?>
