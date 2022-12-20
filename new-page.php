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
			  <div class="card-header"><i class="fas fa-plus"></i> New Page</div>
                <div class="card-body">
				
				 <?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitPage'])) {
					 
					$dupe = DB::getInstance()->selectOneByField('pages', 'page_slug', $_POST['page_name']);
					
					if (!empty($dupe)) {
			            $errors[] = 'That <strong>page</strong> is already in use please choose another.';
		            }
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
						}
						
					} else {
						
						$i = DB::getInstance()->insert(
							'pages',
						[
						    'page_slug' => empty($_POST['page_slug']) ? strtolower(str_replace(' ', '-', $_POST['page_name'])) : $_POST['page_slug'],
						    'page_name' => $_POST['page_name'],
						    'page_body' => nl2br($_POST['page_body']),
							'page_date' => date('Y-m-d H:i:s')
						]);
						
						stdmsg("Your new <strong>page</strong> has been <strong>added</strong>.");
					
					}
					 
				 }
				 
				 ?>
				
				<form action="new-page.php" method="post" enctype="multipart/form-data">
				 
					<div class="mb-3">
						<label for="page_name" class="form-label"><strong>Name:</strong></label>
						<input type="text" class="form-control" id="page_name" name="page_name" required>
					</div>

					<div class="mb-3">
						<label for="page_slug" class="form-label"><strong>Slug:</strong></label>
						<input type="text" class="form-control" id="page_slug" name="page_slug">
					</div>

					<script>
					$(document).ready(function() {
						$('#summernote').summernote({
						  height: 300,       // set editor height
						  minHeight: null,   // set minimum height of editor
						  maxHeight: null,   // set maximum height of editor
						  focus: true        // set focus to editable area after initializing summernote
						});
					});
					</script>

					<div class="mb-3">
						<label for="page_body" class="form-label"><strong>Body:</strong></label>
						<textarea class="form-control" id="summernote" name="page_body" rows="8"></textarea>
					</div>

					<button type="submit" name="submitPage" class="btn btn-success float-end"><i class="fas fa-plus"></i> New Page</button>
				
				</form>               
				
                </div>
			</div>	
			
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>