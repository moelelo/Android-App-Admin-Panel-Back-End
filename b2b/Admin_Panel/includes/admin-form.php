<?php
	include_once('connect_database.php'); 
	include('variables/variables.php'); 
?>

<div id="content">
	<?php 
			$username = $_SESSION['user'];
			$sql_query = "SELECT Password, Email 
					FROM tbl_user 
					WHERE Username = ?";
			
			// create array variable to store previous data
			$data = array();
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $username);			
				// Execute query
				$stmt->execute();
				// store result 
				$stmt->store_result();
				$stmt->bind_result($data['Password'],
					$data['Email']
					);
				$stmt->fetch();
				$stmt->close();
			}
			
			$previous_password = $data['Password'];
			$previous_email = $data['Email'];
			
			if(isset($_POST['btnChange'])){
				$email = $_POST['email'];
				$old_password = hash('sha256',$username.$_POST['old_password']);
				$new_password = hash('sha256',$username.$_POST['new_password']);
				$confirm_password = hash('sha256',$username.$_POST['confirm_password']);
				
				// create array variable to handle error
				$error = array();
				
				// check password
				if(!empty($_POST['old_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password'])){
					if(!empty($_POST['old_password'])){
						if($old_password == $previous_password){
							if(!empty($_POST['new_password']) || !empty($_POST['confirm_password'])){
								if($new_password == $confirm_password){
									// update password in user table
									$sql_query = "UPDATE tbl_user 
											SET Password = ?
											WHERE Username = ?";
									
									$stmt = $connect->stmt_init();
									if($stmt->prepare($sql_query)) {	
										// Bind your variables to replace the ?s
										$stmt->bind_param('ss', 
													$new_password, 
													$username);
										// Execute query
										$stmt->execute();
										// store result 
										$update_result = $stmt->store_result();
										$stmt->close();
									}
								}else{
									$error['confirm_password'] = "*Confirm password does not match with New password.";
								}
							}else{
								$error['confirm_password'] = "*Confirm password and New Password should be filled.";
							}
						}else{
							$error['old_password'] = "*Old password is incorrect.";
						}
					}else{
						$error['old_password'] = "*Old password should be filled.";
					}
				}
				
				if(empty($email)){
					$error['email'] = "*Email should be filled.";
				}else{
					$valid_mail = "/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i";
					if (!preg_match($valid_mail, $email)){
						$error['email'] = "*Email format is incorrect.";
						$email = "";
					}else{
						// update password in user table
						$sql_query = "UPDATE tbl_user 
								SET Email = ?
								WHERE Username = ?";
						
						$stmt = $connect->stmt_init();
						if($stmt->prepare($sql_query)) {	
							// Bind your variables to replace the ?s
							$stmt->bind_param('ss', 
										$email, 
										$username);
							// Execute query
							$stmt->execute();
							// store result 
							$update_result = $stmt->store_result();
							$stmt->close();
						}
					}
				}
				
				// check update result
				if($update_result){
					$to = $email;
					$subject = $email_subject;
					$message = $change_message;
					$from = $admin_email;
					$headers = "From:" . $from;
					mail($to,$subject,$message,$headers);
					$error['update_user'] = "*User data has been successfully updated.";
				}else{
					$error['update_user'] = "*Failed updating user data.";
				}
			}		

			$sql_query = "SELECT Email FROM tbl_user WHERE Username = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $username);			
				// Execute query
				$stmt->execute();
				// store result 
				$stmt->store_result();
				$stmt->bind_result($previous_email);
				$stmt->fetch();
				$stmt->close();
			}		
	?>
	<h1>Admin</h1>
	<hr />
	<form method="post">
		<p>Email:</p>
		<input type="email" name="email" value="<?php echo $previous_email; ?>"/>
		<p class="alert"><?php echo isset($error['email']) ? $error['email'] : '';?></p>
	    <p>Old password:</p>
		<input type="password" name="old_password"/>
		<p class="alert"><?php echo isset($error['old_password']) ? $error['old_password'] : '';?></p>
	    <p>New password:</p>
		<input type="password" name="new_password"/>
		<p class="alert"><?php echo isset($error['new_password']) ? $error['new_password'] : '';?></p>
		<p>Confirm new password:</p>
		<input type="password" name="confirm_password"/>
		<p class="alert"><?php echo isset($error['confirm_password']) ? $error['confirm_password'] : '';?></p>
	    <input type="submit" value="Change" name="btnChange"/>
		<p class="alert"><?php echo isset($error['update_user']) ? $error['update_user'] : '';?></p>
	</form>
	<div class="separator"> </div>
</div>
			
<?php include_once('close_database.php'); ?>
