<?php
var_dump($_GET);

//Get input data
$sentences = array();
if($_GET['mt1'] != '')
	$sentences[] = $_GET['mt1'];
if($_GET['mt2'] != '')
	$sentences[] = $_GET['mt2'];
if($_GET['mt3'] != '')
	$sentences[] = $_GET['mt3'];
if($_GET['mt4'] != '')
	$sentences[] = $_GET['mt4'];
if($_GET['src'] != '')
	$src = $_GET['src'];

//Parse input data
foreach($sentences as $sentence){
	$output = shell_exec('exp.bat "'.$sentence.'"');
	$boom = explode("\n", $output);
	$parsed = $boom[4];
	var_dump($parsed);
}
//Chunk input data

//Choose output chunks

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo 'This is a server using Windows!';
} else {
    echo 'This is a server not using Windows!';
}
?>