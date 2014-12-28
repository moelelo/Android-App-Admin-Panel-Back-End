<?php
	// start session
	session_start();
	
	// set time for session timeout
	$currentTime = time() + 25200;
	$expired = 3600;
	
	// if session not set go to login page
	if(!isset($_SESSION['user'])){
		header("location:index.php");
	}
	
	if($currentTime > $_SESSION['timeout']){
		session_destroy();
		header("location:index.php");
	}
	
	unset($_SESSION['timeout']);
	$_SESSION['timeout'] = $currentTime + $expired;
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/reset.css" />
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
        <title>B2B</title>
    </head>
    <body>
    	<div id="container">
    		<?php include('includes/menubar.php'); ?>
    		<?php include('includes/add-category-form.php'); ?>
			<?php include('includes/footer.php'); ?>
    	</div>
    </body>
</html>