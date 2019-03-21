<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(empty($_POST['g-recaptcha-response'])) 
	{
		header('location: error/no_recaptcha.html');
		 exit();
	}
	
	
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	
	$secret = '6LcU_5gUAAAAAK7T1MKD47niwxzSC8fxme67niWS';
	$recaptcha = $_POST['g-recaptcha-response'];
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$url_data = $url.'?secret='.$secret.'&response='.$recaptcha.'&remoteip='.$ip;
	
	$curl = curl_init();
	
	curl_setopt($curl,CURLOPT_URL, $url_data);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, FALSE);
	
	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
	
	$res = curl_exec($curl);
	curl_close($curl);
	
	$res = json_decode($res);
	
		if($res->success == 0)
	{
		 header('location: error/error_recaptcha.html');
		 exit();
	 }
 }

$name =  stripslashes(htmlspecialchars(strip_tags(trim($_POST['name']))));
$phone = stripslashes(htmlspecialchars(strip_tags(trim($_POST['phone']))));
$birth =  stripslashes(htmlspecialchars(strip_tags(trim($_POST['birth']))));
$email =  stripslashes(htmlspecialchars(strip_tags(trim($_POST['email']))));
$comment = stripslashes(htmlspecialchars(strip_tags(trim($_POST['comment']))));

if (!preg_match('/\b([A-Z]{1}[a-z]{1,30}[- ]{0,1}|[A-Z]{1}[- \']{1}[A-Z]{0,1}  
    [a-z]{1,30}[- ]{0,1}|[a-z]{1,2}[ -\']{1}[A-Z]{1}[a-z]{1,30}){2,5}$/', $name))
	{
		header('location: error/error_name.html');
		exit();
	}

if (!preg_match("/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/",$phone))
	{
		header('location: error/error_phone.html');
		exit();
	}

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		header('location: error/error_email.html');
		exit();
	}

$db_host = "localhost"; 
$db_user = "root"; 
$db_password = ""; 
$db_base = 'aero'; 
$db_table = "academy";

$mysqli = new mysqli($db_host,$db_user,$db_password);
mysqli_set_charset($mysqli, UTF8);

$db_selected = mysqli_select_db($mysqli,$db_base);

if (!$db_selected) {
  $sql = "CREATE DATABASE IF NOT EXISTS aero";
  $usebd = "USE aero";
  $sqltb = "CREATE TABLE academy
  (
  name VARCHAR(255) NOT NULL,
  phone TEXT NOT NULL,
  birth DATE NOT NULL,
  email VARCHAR(255) NOT NULL,
  comment TEXT(255) NOT NULL
  )";
  
  $mysqli->query($sql);
  $mysqli->query($usebd);
  $mysqli->query($sqltb);
}

if ($mysqli->connect_error) 
{
	die('Ошибка : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}
$name = $mysqli->real_escape_string($name);
$comment = $mysqli->real_escape_string($comment);
$email =  $mysqli->real_escape_string($email);
$phone = $mysqli->real_escape_string($phone);
$birth =  $mysqli->real_escape_string($birth);

$result = $mysqli->query("INSERT INTO ".$db_table." (name, phone, birth, email, comment) VALUES ('$name', '$phone', '$birth', '$email', '$comment')");

if ($result == true)
{
	header('location: error/success.html');
	exit();
}
else
{
	header('location: error/error.html');
	exit();
}
$mysqli->close();
?>