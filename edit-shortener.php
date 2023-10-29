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
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard-extended.php"); ?>
			</div>
			
		</div>
		
		<div class="col-md-9">
		
			<div class="card">
			  <div class="card-header"><i class="fas fa-upload"></i> Update Shortener</div>
                <div class="card-body">
				
				<?php
				 
				$errors = [];
				 
				if (isset($_POST['submitEditShortener'])) {
					 
					$dupe = DB::getInstance()->selectOneByField('shorteners', 'shortener_original_url', $_POST['shortener_original_url']);
					
					if (!empty($dupe)) {
			            $errors[] = 'That <strong>Original URL</strong> is already in use please choose another.';
		            }
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo "<div class=\"alert alert-danger\" role=\"alert\"><i class=\"fas fa-exclamation-triangle\"></i> {$error}</div>";
						}
						
					} else {
						
						$u = DB::getInstance()->update(
							'shorteners',
							'shortener_id',
							$_POST['updateId'],
						[
							'shortener_original_url' => $_POST['shortener_original_url']
						]);
						
						stdmsg("Your <strong>Original URL</strong> has been <strong>updated</strong>.");
					
					}
					 
				}
				 
				?>
				
				<?php
				
				    $shortener = DB::getInstance()->selectValues("SELECT * FROM `shorteners` WHERE `shortener_id`='{$_GET['shortenerId']}'");
				
				?>
				
				<form action="edit-shortener.php?shortenerId=<?= $_GET['shortenerId']; ?>" method="post">
				 
					<div class="mb-3">
						<label for="shortener_original_url" class="form-label"><strong>Original URL:</strong></label>
						<input type="text" class="form-control" id="shortener_original_url" name="shortener_original_url" value="<?= $shortener['shortener_original_url']; ?>" required>
					</div>
					
					<div class="mb-3">
						<label for="shortener_short" class="form-label"><strong>Short Code (optional):</strong></label>
						<input type="text" class="form-control" id="shortener_short" name="shortener_short" value="<?= $shortener['shortener_short']; ?>" disabled>
					</div>
				  
					<input type="hidden" name="updateId" value="<?= $_GET['shortenerId']; ?>">				  
					<button type="submit" name="submitEditShortener" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>
				
				</form>               
				
                </div>
				
			</div>	
			
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>