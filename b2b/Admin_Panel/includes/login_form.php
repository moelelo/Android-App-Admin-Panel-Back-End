<?php
	include_once('connect_database.php'); 
	
	// start session
	session_start();
	
	// if user click Login button
	if(isset($_POST['btnLogin'])){
	
		// get username and password
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		// set time for session timeout
		$currentTime = time() + 25200;
		$expired = 3600;
		
		// create array variable to handle error
		$error = array();
		
		// check whether $username is empty or not
		if(empty($username)){
			$error['username'] = "*Username should be filled.";
		}
		
		// check whether $password is empty or not
		if(empty($password)){
			$error['password'] = "*Password should be filled.";
		}
		
		// if username and password is not empty, check in database
		if(!empty($username) && !empty($password)){
			
			// change username to lowercase
			$username = strtolower($username);
			
			//encript password to sha256
		    $password = hash('sha256',$username.$password);
			
			// get data from user table
			$sql_query = "SELECT * 
				FROM tbl_user 
				WHERE username = ? AND password = ?";
						
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {
				// Bind your variables to replace the ?s
				$stmt->bind_param('ss', $username, $password);
				// Execute query
				$stmt->execute();
				/* store result */
				$stmt->store_result();
				$num = $stmt->num_rows;
				// Close statement object
				$stmt->close();
				if($num == 1){
					$_SESSION['user'] = $username;
					$_SESSION['timeout'] = $currentTime + $expired;
					header("location: reservation.php");
				}else{
					$error['failed'] = "*Login failed.";
				}
			}
			
		}	
	}
	?>
<div id="login_content">
	<h1>Login Admin</h1>
    <form method="post">
		<p>Username: </p>
	    <input type="text" name="username" />
		<p class="alert"><?php echo isset($error['username']) ? $error['username'] : '';?></p>
	    <p>Password:</p>
	    <input type="password" name="password" />
		<p class="alert"><?php echo isset($error['password']) ? $error['password'] : '';?></p>    
	    <input type="submit" value="Login" name="btnLogin" />
		<p class="alert"><?php echo isset($error['failed']) ? $error['failed'] : '';?></p>
    </form>

    <form action="http://localhost:8888/b2b/">
    	<input type="submit" value="Home Page" name="btnHome" />
    </form>
    <a href="forget-password.php">forget password?</a></br>
</div>
<?php include_once('close_database.php');?>