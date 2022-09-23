<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sessions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>


<main>

	<?php $user = getUsersDetails($member); ?>
	
	<?php
	
	if ($user['member_is_admin'] != "yes") {
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
		</div>
		
		<div class="col-md-9">
			<div class="card">
			  <div class="card-header"><i class="fas fa-upload"></i> Update Category</div>
                <div class="card-body">
				
				<?php
				 
				$errors = [];
				 
				if (isset($_POST['submitEditCategory'])) {
					 
					$dupe = DB::getInstance()->selectOneByField('categories', 'category_name', $_POST['category_name']);
					
					if (!empty($dupe)) {
			            $errors[] = 'That <strong>category</strong> is already in use please choose another.';
		            }
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo "<div class=\"alert alert-danger\" role=\"alert\"><i class=\"fas fa-exclamation-triangle\"></i> {$error}</div>";
						}
						
					} else {
						
						$u = DB::getInstance()->update(
							'categories',
							'category_id',
							$_POST['updateId'],
						[
							'category_name' => $_POST['category_name']
						]);
						
						stdmsg("Your <strong>category</strong> has been <strong>updated</strong>.");
					
					}
					 
				}
				 
				?>
				
				<?php
				
				    $category = DB::getInstance()->selectValues("SELECT `category_name` FROM `categories` WHERE `category_id`='{$_GET['categoryId']}'");
				
				?>
				
				<form action="edit-category.php?categoryId=<?= $_GET['categoryId']; ?>" method="post">
				 
				  <div class="mb-3">
					<label for="category_name" class="form-label"><strong>Name:</strong></label>
					<input type="text" class="form-control" id="category_name" name="category_name" value="<?= $category['category_name'];; ?>" required>
				  </div>
				  
				  <input type="hidden" name="updateId" value="<?= $_GET['categoryId']; ?>">				  
				  <button type="submit" name="submitEditCategory" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>
				
				</form>               
				
                </div>
				<div class="card-footer">&nbsp;</div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>