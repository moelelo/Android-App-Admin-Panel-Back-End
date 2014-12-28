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
			
			// delete data from reservation table
			$sql_query = "DELETE FROM tbl_reservation 
					WHERE ID = ?";
			
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$delete_result = $stmt->store_result();
				$stmt->close();
			}
			
			// if delete data success back to reservation page
			if($delete_result){
				header("location: reservation.php");
			}
				
		}		
	?>
	<h1>Confirm Action</h1>
	<hr />
	<form method="post">
		<p>Are you sure want to delete this data?</p>
		<input type="submit" value="Delete" name="btnDelete"/>
	</form>
	<div class="separator"> </div>
</div>
			
<?php include_once('close_database.php'); ?>