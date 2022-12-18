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
			  <div class="card-header"><i class="fas fa-upload"></i> Update Post</div>
                <div class="card-body">
				
				<?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitEditPage'])) {				
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
						}
						
					} else {
						
						$u = DB::getInstance()->update(
							'pages',
							'page_id',
							$_POST['updateId'],
						[
						    'page_slug' => $_POST['page_slug'],
						    'page_name' => $_POST['page_name'],
						    'page_body' => $_POST['page_body']					
						]);
						
						stdmsg("Your <strong>page</strong> has been <strong>updated</strong>.");
					
					}
					 
				 }
				 
				?>
				 
				<?php 
				
				    $page = DB::getInstance()->selectValues("SELECT * FROM `pages` WHERE `page_id`='{$_GET['pageId']}'"); 
					
				?>
				
  				<form action="edit-page.php?pageId=<?= $_GET['pageId']; ?>" method="post">
				 
					<div class="mb-3">
						<label for="page_name" class="form-label"><strong>Name:</strong></label>
						<input type="text" class="form-control" id="page_name" name="page_name" value="<?= $page['page_name']; ?>" required>
					</div>

					<div class="mb-3">
						<label for="page_slug" class="form-label"><strong>Slug:</strong></label>
						<input type="text" class="form-control" id="page_slug" name="page_slug" value="<?= $page['page_slug']; ?>" required>
					</div>
				  
					<script>
					$(document).ready(function() {
						$('#summernote').summernote({
						  height: 300,                 // set editor height
						  minHeight: null,             // set minimum height of editor
						  maxHeight: null,             // set maximum height of editor
						  focus: true                  // set focus to editable area after initializing summernote
						});
					});
					</script>

					<div class="mb-3">
						<label for="page_body" class="form-label"><strong>Body:</strong></label>
						<textarea class="form-control" id="summernote" name="page_body" rows="8"><?= $page['page_body']; ?></textarea>
					</div>
				  
					<input type="hidden" name="updateId" value="<?= $_GET['pageId']; ?>">	
					<button type="submit" name="submitEditPage" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>
				
				</form>            
				
                </div>
				
			</div>	
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>