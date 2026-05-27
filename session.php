<?php
include_once 'db.php';
// include_once 'database.php';

session_start(); 

	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sid = $_SESSION['userid'];
	
	$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = '$sid'");

	$stmt->execute();
	
	$readrow = $stmt->fetch(PDO::FETCH_ASSOC);

	$sid = $readrow['fld_staff_id'];
	$name = $readrow['fld_staff_name'];
	$email= $readrow['fld_staff_email'];
	$salary = $readrow['fld_staff_salary'];
	$pos = $readrow['fld_staff_position'];
	$pass= $readrow['fld_staff_password'];
		
if($sid==''){
	header("location:login.php");
	}
	else {
	header("");
	}
?>