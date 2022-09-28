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
			  <div class="card-header"><i class="fas fa-image"></i> New Image</div>
                <div class="card-body">
				
				 <?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitImage'])) {

						if (!empty($_FILES['post_image']['name']) && empty($_POST['post_image_header'])) {
							
                            $imageName = uploadImage($_FILES['post_image']['name'], $_FILES['post_image']['tmp_name']);	
                            if (!empty($imageName)) {
								
								$i = DB::getInstance()->insert(
									'images',
								[
									'image_name' => $imageName,
									'image_alt_text' => $_POST['post_image_alt_text'],
									'image_is_header' => "no",
									'image_date' => date('Y-m-d H:i:s')
								]);
								
								stdmsg("Your new <strong>image</strong> has been <strong>uploaded</strong>.");
								
							}							
							
						} 	

                        if (!empty($_FILES['post_image']['name']) && !empty($_POST['post_image_header'])) {
							
                            $imageName = uploadImage($_FILES['post_image']['name'], $_FILES['post_image']['tmp_name']);
                            if (!empty($imageName)) {
								
								// Quality for .png = 0 - 9 9 is the worst.
								$resizedImage = resizeImage("uploads/" . $imageName, "uploads/" . $imageName, [150, 100], 0);
								
								$i = DB::getInstance()->insert(
									'images',
								[
									'image_name' => $imageName,
									'image_alt_text' => $_POST['post_image_alt_text'],
									'image_is_header' => "yes",
									'image_date' => date('Y-m-d H:i:s')
								]);
								
								stdmsg("Your new <strong>image</strong> has been <strong>uploaded</strong> and <strong>resized</strong>.");
								
							}
							
						} 				
					 
				 }
				 
				 ?>
				
				<form action="new-image.php" method="post" enctype="multipart/form-data">
				 
				    <div class="mb-3">
					  <label for="post_image" class="form-label"><strong>Image:</strong></label>
					  <input class="form-control" type="file" id="post_image" name="post_image" required>
				    </div>
					
				    <div class="mb-3">
					  <label for="post_image_alt_text" class="form-label"><strong><span class="text-success">Image ALT Text:</span></strong></label>
					  <input type="text" class="form-control" id="post_image_alt_text" name="post_image_alt_text" required>
				    </div>
				  
					<div class="form-check form-switch">
					  <input class="form-check-input" type="checkbox" id="post_image_header" name="post_image_header">
					  <label class="form-check-label" for="flexSwitchCheckChecked"><small>Use this as a header image. will be resized to <strong>150</strong> width x <strong>150</strong> height.</small></label>
					</div>
					
					<hr />
				  
				  <button type="submit" name="submitImage" class="btn btn-success float-end"><i class="fas fa-plus"></i> New Image</button>
				
				</form>               
				
                </div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>