<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content">
	<?php 
		if(isset($_POST['btnAdd'])){
			$category_name = $_POST['category_name'];
			
			// get image info
			$menu_image = $_FILES['category_image']['name'];
			$image_error = $_FILES['category_image']['error'];
			$image_type = $_FILES['category_image']['type'];
			
			// create array variable to handle error
			$error = array();
			
			if(empty($category_name)){
				$error['category_name'] = "*Category name should be filled.";
			}
			
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			$extension = end(explode(".", $_FILES["category_image"]["name"]));
					
			if($image_error > 0){
				$error['category_image'] = "*Image should be uploaded.";
			}else if(!(($image_type == "image/gif") || 
				($image_type == "image/jpeg") || 
				($image_type == "image/jpg") || 
				($image_type == "image/x-png") ||
				($image_type == "image/png") || 
				($image_type == "image/pjpeg")) &&
				!(in_array($extension, $allowedExts))){
			
				$error['category_image'] = "*Image type should be jpg, jpeg, gif, or png.";
			}
			
			if(!empty($category_name) && empty($error['category_image'])){
				
				// create random image file name
				$string = '0123456789';
				$file = preg_replace("/\s+/", "_", $_FILES['category_image']['name']);
				$function = new functions;
				$menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					
				// upload new image
				$upload = move_uploaded_file($_FILES['category_image']['tmp_name'], 'upload/images/'.$menu_image);
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_category (Category_name, Category_image)
						VALUES(?, ?)";
				
				$upload_image = 'upload/images/'.$menu_image;
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {
					
					// Bind your variables to replace the ?s
					$stmt->bind_param('ss', 
								$category_name, 
								$upload_image
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				
				if($result){
					$error['add_category'] = "*New category has been successfully added.";
				}else{
					$error['add_category'] = "*Failed adding new category.";
				}
			}
			
		}
	?>
	<h1>Add Category</h1>
	<hr />
	<form method="post"
		enctype="multipart/form-data">
		<p>Category Name:</p>
		<input type="text" name="category_name"/>
		<p class="alert"><?php echo isset($error['category_name']) ? $error['category_name'] : '';?></p>
		<p>Image for preview:</p>
		<input type="file" name="category_image" id="category_image" />
		<p class="alert"><?php echo isset($error['category_image']) ? $error['category_image'] : '';?></p>
		<input type="submit" value="Submit" name="btnAdd"/>
		<input type="reset" value="Clear"/>
		<p class="alert"><?php echo isset($error['add_category']) ? $error['add_category'] : '';?></p>
	</form>
		
	<div class="separator"> </div>
</div>
	
<?php include_once('close_database.php'); ?>