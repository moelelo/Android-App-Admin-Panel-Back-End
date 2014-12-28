<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
	
?>

<div id="content">
	<?php 
		// create object of functions class
		$function = new functions;
		
		// create array variable to store data from database
		$data = array();
		
		if(isset($_GET['keyword'])){	
			// check value of keyword variable
			$keyword = $function->sanitize($_GET['keyword']);
			$bind_keyword = "%".$keyword."%";
		}else{
			$keyword = "";
			$bind_keyword = $keyword;
		}
		
		// get all data from reservation table
		if(empty($keyword)){
			$sql_query = "SELECT ID, Name, Number_of_people, Date_n_Time, Phone_number, Status
				FROM tbl_reservation  
				ORDER BY Date_n_Time DESC";
		}else{
			$sql_query = "SELECT ID, Name, Number_of_people, Date_n_Time, Phone_number, Status
				FROM tbl_reservation 
				WHERE Name LIKE ? 
				ORDER BY Date_n_Time DESC";
		}
			
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			if(!empty($keyword)){
				$stmt->bind_param('s', $bind_keyword);
			}
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['ID'], 
					$data['Name'], 
					$data['Number_of_people'], 
					$data['Date_n_Time'], 
					$data['Phone_number'],
					$data['Status']
					);
					
			// get total records
			$total_records = $stmt->num_rows;
		}
		
		// check page parameter
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		
		// number of data that will be display per page
		$offset = 10;
							
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $offset) - $offset;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}
		
		// get all data from reservation table
		if(empty($keyword)){
			$sql_query = "SELECT ID, Name, Number_of_people, Date_n_Time, Phone_number, Status 
				FROM tbl_reservation 
				ORDER BY Date_n_Time DESC 
				LIMIT ?, ?";
		}else{
			$sql_query = "SELECT ID, Name, Number_of_people, Date_n_Time, Phone_number, Status 
				FROM tbl_reservation 
				WHERE Name LIKE ? 
				ORDER BY Date_n_Time ASC 
				LIMIT ?, ?";
		}
		
		$stmt_paging = $connect->stmt_init();
		if($stmt_paging ->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if(empty($keyword)){
				$stmt_paging ->bind_param('ss', $from, $offset);
			}else{
				$stmt_paging ->bind_param('sss', $bind_keyword, $from, $offset);
			}
			// Execute query
			$stmt_paging ->execute();
			// store result 
			$stmt_paging ->store_result();
			
			$stmt_paging ->bind_result($data['ID'], 
					$data['Name'], 
					$data['Number_of_people'], 
					$data['Date_n_Time'], 
					$data['Phone_number'],
					$data['Status']
					);
			
			// for paging purpose
			$total_records_paging = $total_records; 
		}
						
		// if no data on database show "No Reservation is Available"
		if($total_records_paging == 0){
	?>
	<h1>No Reservation is Available</h1>
	<hr />
	
	<?php
		// otherwise, show data
		}else{ $row_number = $from + 1;?>
	
	<h1>Reservation List</h1>
	<!-- search form -->
	<form class="list_header" method="get">
		<p class="pholder">Search by name: </p>
		<input type="text" name="keyword" />
		<input type="submit" name="btnSearch" value="Search"/>
	</form>
	<!-- end of search form -->
	<hr />
	<table>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>No. of people</th>
			<th>Date & Time</th>
			<th>Phone number</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
		<?php
			// get all data using while loop
			while ($stmt_paging->fetch()){ ?>
			<tr class="row">
				<td><?php echo $data['ID'];?></td>
				<td><?php echo $data['Name'];?></td>
				<td><?php echo $data['Number_of_people'];?></td>
				<td><?php echo $data['Date_n_Time'];?></td>
				<td><?php echo $data['Phone_number'];?></td>
				<td><?php echo $data['Status'] == 1 ? "Processed" : "Not Processed";?></td>
				<td><a href="reservation-detail.php?id=<?php echo $data['ID'];?>">View Detail</a> | <a href="delete-reservation.php?id=<?php echo $data['ID'];?>">Delete</a></td>
			</tr>
		<?php } }?>
	</table>
	
	<?php 
		// for pagination purpose
		$function->doPages($offset, 'reservation.php', '', $total_records, $keyword);?>
	<div class="separator"> </div>
</div> 

<?php 
	$stmt->close();
	include_once('close_database.php'); ?>
					
				