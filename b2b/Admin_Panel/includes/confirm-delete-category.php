<?php
	include_once('connect_database.php'); 
?>

<div id="content">
	<?php 
		
		if(isset($_POST['btnDelete'])){
			if(isset($_GET['id'])){
				$ID = $_GET['id'];
			}else{
				$ID = "";
			}
			// get image file from table
			$sql_query = "SELECT Category_image 
					FROM tbl_category 
					WHERE Category_ID = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$stmt->store_result();
				$stmt->bind_result($category_image);
				$stmt->fetch();
				$stmt->close();
			}
			
			// delete image file from directory
			$delete = unlink("$category_image");
			
			// delete data from menu table
			$sql_query = "DELETE FROM tbl_category 
					WHERE Category_ID = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$delete_category_result = $stmt->store_result();
				$stmt->close();
			}
			
			// get image file from table
			$sql_query = "SELECT Menu_image 
					FROM tbl_menu 
					WHERE Category_ID = ?";
			
			// create array variable to store menu image
			$image_data = array();
			
			$stmt_menu = $connect->stmt_init();
			if($stmt_menu->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt_menu->bind_param('s', $ID);
				// Execute query
				$stmt_menu->execute();
				// store result 
				$stmt_menu->store_result();
				$stmt_menu->bind_result($image_data['Menu_image']);
			}
			
			// delete all menu image files from directory
			while($stmt_menu->fetch()){
				$menu_image = $image_data[Menu_image];
				$delete_image = unlink("$menu_image");
			}
			
			$stmt_menu->close();
			
			// delete data from menu table
			$sql_query = "DELETE FROM tbl_menu 
					WHERE Category_ID = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$delete_menu_result = $stmt->store_result();
				$stmt->close();
			}
				
			// if delete data success back to reservation page
			if($delete_category_result && $delete_menu_result){
				header("location: category.php");
			}
			
		}		
	?>
	<h1>Confirm Action</h1>
	<hr />
	<form method="post">
		<p>
			By deleting this category, all menu inside this category will also be deleted. 
			Are you sure want to delete this category?
		</p>
		<input type="submit" value="Delete" name="btnDelete"/>
	</form>
	<div class="separator"> </div>
</div>
			
<?php 
	include_once('close_database.php'); ?>