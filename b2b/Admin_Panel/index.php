<?php 
    // to keep the content in a server-side buffer until it's ready to display it
	ob_start(); 

	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="css/reset.css" />
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
<title>B2B: Admin Panel</title>
    </head>
    <body>
    	<div id="container">
			<?php include('includes/login_form.php');?>
	        <?php include('includes/footer.php');?>
    	</div>
    </body>
</html>