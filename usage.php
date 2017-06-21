<?php
include 'functions.php';
header('Content-Type: application/json; charset=utf-8');
$url = 
$id = insta_id($url); //grabbing insta id from url
$info = media_info($id); //getting info. Returns array. Check functions.php
echo json_encode($info,JSON_PRETTY_PRINT); //Printing array as json.
?>