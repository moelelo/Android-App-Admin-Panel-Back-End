<?php
	// database configuration
	$host ="localhost";
	$user ="root";
	$pass ="root";
	$database = "u667470538_auiss";
	$connect = new mysqli($host, $user, $pass,$database) or die("Error : ".mysql_error());
	
	// access key to access API
	$access_key = "12345";
	
	// google play url
	$gplay_url = "https://play.google.com/store/apps/details?id=";
	
	// email configuration
	$admin_email = "contact@website.com";
	$email_subject = "B2B: Information Email";
	$change_message = "You have change your admin info such as email and or password.";
	$reset_message = "Your new password is ";
	
	// reservation notification configuration
	$reservation_subject = "B2B: New Reservation";
	$reservation_message = "There is new reservation. please check it on admin page.";
	
	// copyright
	$copyright = "B2B &copy; 2014 b2b.com. All rights reserved.";
?>