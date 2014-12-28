<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content">
	<?php 
	
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		
		// create array variable to store category data
		$category_data = array();
			
		$sql_query = "SELECT Category_ID, Category_name 
				FROM tbl_category 
				ORDER BY Category_ID ASC";
				
		$stmt_category = $connect->stmt_init();
		if($stmt_category->prepare($sql_query)) {	
			// Execute query
			$stmt_category->execute();
			// store result 
			$stmt_category->store_result();
			$stmt_category->bind_result($category_data['Category_ID'], 
				$category_data['Category_name']
				);
				
		}
			
		$sql_query = "SELECT Menu_image FROM tbl_menu WHERE Menu_ID = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_menu_image);
			$stmt->fetch();
			$stmt->close();
		}
		
		
		// get currency symbol from setting table
		$sql_query = "SELECT Value 
				FROM tbl_setting 
				WHERE Variable = 'Currency'";
		
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($currency);
			$stmt->fetch();
			$stmt->close();
		}	
		
		
		if(isset($_POST['btnEdit'])){
			
			$menu_name = $_POST['menu_name'];
			$category_ID = $_POST['category_ID'];
			$price = $_POST['price'];
			$serve_for = $_POST['serve_for'];
			$description = $_POST['description'];
			
			// get image info
			$menu_image = $_FILES['menu_image']['name'];
			$image_error = $_FILES['menu_image']['error'];
			$image_type = $_FILES['menu_image']['type'];
				
			// create array variable to handle error
			$error = array();
			
			if(empty($menu_name)){
				$error['menu_name'] = "*Menu name should be filled.";
			}
				
			if(empty($category_ID)){
				$error['category_ID'] = "*Category should be selected.";
			}				
				
			if(empty($price)){
				$error['price'] = "*Price should be filled.";
			}else if(!is_numeric($price)){
				$error['price'] = "*Price should be in numeric.";
			}
				
			if(empty($serve_for)){
				$error['serve_for'] = "*Serve for should be selected.";
			}			

			if(empty($description)){
				$error['description'] = "*Description should be filled.";
			}
			
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			$extension = end(explode(".", $_FILES["menu_image"]["name"]));
			
			if(!empty($menu_image)){
				if(!(($image_type == "image/gif") || 
					($image_type == "image/jpeg") || 
					($image_type == "image/jpg") || 
					($image_type == "image/x-png") ||
					($image_type == "image/png") || 
					($image_type == "image/pjpeg")) &&
					!(in_array($extension, $allowedExts))){
					
					$error['menu_image'] = "*Image type should be jpg, jpeg, gif, or png.";
				}
			}
			
					
			if(!empty($menu_name) && !empty($category_ID) && !empty($price) && is_numeric($price) &&
				!empty($serve_for) && !empty($description) && empty($error['menu_image'])){
				
				if(!empty($menu_image)){
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['menu_image']['name']);
					$function = new functions;
					$menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
				
					// delete previous image
					$delete = unlink("$previous_menu_image");
					
					// upload new image
					$upload = move_uploaded_file($_FILES['menu_image']['tmp_name'], 'upload/images/'.$menu_image);
	  
					// updating all data
					$sql_query = "UPDATE tbl_menu 
							SET Menu_name = ? , Category_ID = ?, Price = ?, Serve_for = ?, Menu_image = ?, Description = ? 
							WHERE Menu_ID = ?";
					
					$upload_image = 'upload/images/'.$menu_image;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sssssss', 
									$menu_name, 
									$category_ID, 
									$price, 
									$serve_for, 
									$upload_image,
									$description,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}else{
					
					// updating all data except image file
					$sql_query = "UPDATE tbl_menu 
							SET Menu_name = ? , Category_ID = ?, 
							Price = ?, Serve_for = ?, Description = ? 
							WHERE Menu_ID = ?";
							
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ssssss', 
									$menu_name, 
									$category_ID, 
									$price, 
									$serve_for, 
									$description,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}
					
				// check update result
				if($update_result){
					$error['update_data'] = "*Menu has been successfully updated.";
				}else{
					$error['update_data'] = "*Failed updating menu.";
				}
			}
			
		}
		
		// create array variable to store previous data
		$data = array();
			
		$sql_query = "SELECT * FROM tbl_menu WHERE Menu_ID = ?";
			
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['Menu_ID'], 
					$data['Menu_name'], 
					$data['Category_ID'], 
					$data['Price'], 
					$data['Serve_for'], 
					$data['Menu_image'],
					$data['Description']
					);
			$stmt->fetch();
			$stmt->close();
		}
		
			
	?>
	<h1>Edit Menu</h1>
	<hr />
	<form method="post"
		enctype="multipart/form-data">
		<p>Menu Name:</p>
		<input type="text" name="menu_name" value="<?php echo $data['Menu_name']; ?>"/>
		<p class="alert"><?php echo isset($error['menu_name']) ? $error['menu_name'] : '';?></p>
	    <p>Price(<?php echo $currency;?>):</p>
		<input type="text" name="price" value="<?php echo $data['Price']; ?>" />
		<p class="alert"><?php echo isset($error['price']) ? $error['price'] : '';?></p>
	    <p>Serve for(people):</p>
		<select name="serve_for">
			<?php for($i=1;$i<11;$i++){ 
				if($i == $data['Serve_for']){?>
					<option value="<?php echo $i; ?>" selected="<?php echo $data['Serve_for']; ?>" ><?php echo $i; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
				<?php }}?>
		</select>
		<p class="alert"><?php echo isset($error['serve_for']) ? $error['serve_for'] : '';?></p>
	    <p>Category:</p>
		<select name="category_ID">
			<?php while($stmt_category->fetch()){ 
				if($category_data['Category_ID'] == $data['Category_ID']){?>
					<option value="<?php echo $category_data['Category_ID']; ?>" selected="<?php echo $data['Category_ID']; ?>" ><?php echo $category_data['Category_name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $category_data['Category_ID']; ?>" ><?php echo $category_data['Category_name']; ?></option>
				<?php }} ?>
		</select>
		<p class="alert"><?php echo isset($error['category_ID']) ? $error['category_ID'] : '';?></p>
	    <p>Image for preview:</p>
		<input type="file" name="menu_image" id="menu_image"/><br />
		<img src="<?php echo $data['Menu_image']; ?>" width="280" height="190"/>
		<p class="alert"><?php echo isset($error['menu_image']) ? $error['menu_image'] : '';?></p>
		<p>Menu description:</p>
		<textarea name="description"><?php echo $data['Description']; ?></textarea>
		<p class="alert"><?php echo isset($error['description']) ? $error['description'] : '';?></p>
		<input type="submit" value="Submit" name="btnEdit" />
		<p class="alert"><?php echo isset($error['update_data']) ? $error['update_data'] : '';?></p>
	</form>
	<div class="separator"> </div>
	</div>

<?php 
	$stmt_category->close();
	include_once('close_database.php'); ?>