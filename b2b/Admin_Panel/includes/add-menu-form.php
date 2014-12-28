<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content">
	<?php 
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
			
		$max_serve = 10;
			
		if(isset($_POST['btnAdd'])){
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
					
			if($image_error > 0){
				$error['menu_image'] = "*Image should be uploaded.";
			}else if(!(($image_type == "image/gif") || 
				($image_type == "image/jpeg") || 
				($image_type == "image/jpg") || 
				($image_type == "image/x-png") ||
				($image_type == "image/png") || 
				($image_type == "image/pjpeg")) &&
				!(in_array($extension, $allowedExts))){
			
				$error['menu_image'] = "*Image type should be jpg, jpeg, gif, or png.";
			}
				
			if(!empty($menu_name) && !empty($category_ID) && !empty($price) && is_numeric($price) &&
				!empty($serve_for) && empty($error['menu_image']) && !empty($description)){
				
				// create random image file name
				$string = '0123456789';
				$file = preg_replace("/\s+/", "_", $_FILES['menu_image']['name']);
				$function = new functions;
				$menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					
				// upload new image
				$upload = move_uploaded_file($_FILES['menu_image']['tmp_name'], 'upload/images/'.$menu_image);
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_menu (Menu_name, Category_ID, Price, Serve_for, Menu_image, Description)
						VALUES(?, ?, ?, ?, ?, ?)";
						
				$upload_image = 'upload/images/'.$menu_image;
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('ssssss', 
								$menu_name, 
								$category_ID, 
								$price, 
								$serve_for, 
								$upload_image,
								$description
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				
				if($result){
					$error['add_menu'] = "*New menu has been successfully added.";
				}else{
					$error['add_menu'] = "*Failed adding new menu.";
				}
			}
				
			}
	?>
	<h1>Add Menu</h1>
	<hr />
	<form method="post"
		enctype="multipart/form-data">
		<p>Menu Name:</p>
		<input type="text" name="menu_name" />
		<p class="alert"><?php echo isset($error['menu_name']) ? $error['menu_name'] : '';?></p>
	    <p>Price(<?php echo $currency;?>):</p>
		<input type="text" name="price" />
		<p class="alert"><?php echo isset($error['price']) ? $error['price'] : '';?></p>
	    <p>Serve for(people):</p>
		<select name="serve_for">
			<?php for($i=1;$i<$max_serve+1;$i++){ 
				if($i == 1){?>
					<option value="<?php echo $i; ?>" selected="<?php echo $i; ?>" ><?php echo $i; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
			<?php }}?>
		</select>
		<p class="alert"><?php echo isset($error['serve_for']) ? $error['serve_for'] : '';?></p>
	    <p>Category:</p>
		<select name="category_ID">
			<?php while($stmt_category->fetch()){ ?>
				<option value="<?php echo $category_data['Category_ID']; ?>"><?php echo $category_data['Category_name']; ?></option>
			<?php } ?>
		</select>
		<p class="alert"><?php echo isset($error['category_ID']) ? $error['category_ID'] : '';?></p>
		<p>Image for preview:</p>
		<input type="file" name="menu_image" id="menu_image"/>
		<p class="alert"><?php echo isset($error['menu_image']) ? $error['menu_image'] : '';?></p>
		<p>Menu description:</p>
		<textarea name="description"></textarea>
		<p class="alert"><?php echo isset($error['description']) ? $error['description'] : '';?></p>
		<input type="submit" value="Submit" name="btnAdd" />
		<input type="reset" value="Clear"/>
		<p class="alert"><?php echo isset($error['add_menu']) ? $error['add_menu'] : '';?></p>
	</form>
				
	<div class="separator"> </div>
</div>
			

<?php 
	$stmt_category->close();
	include_once('close_database.php'); ?>