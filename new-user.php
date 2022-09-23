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
			  <div class="card-header"><i class="fas fa-plus"></i> New User</div>
                <div class="card-body">
				
				 <?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitUser'])) {
					 
					$dupe = DB::getInstance()->selectOneByField('members', 'member_email', $_POST['user_email']);
					
					if (!empty($dupe)) {
			            $errors[] = 'That <strong>email</strong> is already in use please choose another.';
		            }
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
						}
						
					} else {
						
						DB::getInstance()->insert(
							'members',
						[
							'member_username' => $_POST['user_username'],
							'member_password' => $_POST['user_password'],
							'member_password_md5' => $_POST['user_password'],
							'member_email' => $_POST['user_email'],
							'member_is_admin' => $_POST['user_status'],
							'member_date' => date('Y-m-d H:i:s')
						]);
						
						stdmsg("Your new <strong>user</strong> has been <strong>added</strong>.");
					
					}
					 
				 }
				 
				 ?>
				
				 <form action="new-user.php" method="post">
				 
				  <div class="mb-3">
					<label for="user_username" class="form-label"><strong>Username:</strong></label>
					<input type="text" class="form-control" id="user_username" name="user_username" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="user_password" class="form-label"><strong>Password:</strong></label>
					<input type="text" class="form-control" id="user_password" name="user_password" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="user_email" class="form-label"><strong>Email:</strong></label>
					<input type="email" class="form-control" id="user_email" name="user_email" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="user_status" class="form-label"><strong>Administrator:</strong></label>
					<select id="user_status" name="user_status" class="form-select" required>
					  <?php 
					      $admin = array("no" => "No", "yes" => "Yes");
					      foreach($admin as $key => $value) {
					          echo "<option value='{$key}'>{$value}</option>";
                          } 
					  ?>
					</select>
				  </div>
				  
				  <button type="submit" name="submitUser" class="btn btn-success float-end"><i class="fas fa-plus"></i> New User</button>
				
				</form>               
				
                </div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>