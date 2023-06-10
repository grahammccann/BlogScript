<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sessions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>


<main>

	<?php $user = getUsersDetails($member); ?>
	
	<?php
	
	if (!$user || $user['member_is_admin'] != "yes") {
		stderr("<strong>Protected</strong> page.");
		include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php");
		die();		
	}
	
	?>

	<div class="row">
	
		<div class="col-md-3">
		
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard.php"); ?>
			</div>
			
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard-analytics.php"); ?>
			</div>
			
		</div>
		
		<div class="col-md-9">
		
			<div class="card">
			  <div class="card-header"><i class="fas fa-plus"></i> New Category</div>
                <div class="card-body">
				
				 <?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitCategory'])) {
					 
					$dupe = DB::getInstance()->selectOneByField('categories', 'category_name', $_POST['category_name']);
					
					if (!empty($dupe)) {
			            $errors[] = 'That <strong>category</strong> is already in use please choose another.';
		            }
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
						}
						
					} else {
						
						$i = DB::getInstance()->insert(
							'categories',
						[
							'category_name' => $_POST['category_name'],
							'category_date' => date('Y-m-d H:i:s')
						]);
						
						stdmsg("Your new <strong>category</strong> has been <strong>added</strong>.");
					
					}
					 
				 }
				 
				 ?>
				
				<form action="new-category.php" method="post">
				 
				  <div class="mb-3">
					<label for="category_name" class="form-label"><strong>Name:</strong></label>
					<input type="text" class="form-control" id="category_name" name="category_name" required>
				  </div>
				  
				  <button type="submit" name="submitCategory" class="btn btn-success float-end"><i class="fas fa-plus"></i> New Category</button>
				
				</form>               
				
                </div>
			</div>	
			
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>