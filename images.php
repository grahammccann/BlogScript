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
			  <div class="card-header"><i class="fas fa-image"></i> Images <span class="badge bg-success float-end"><a href="new-image.php" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Upload a new image">New Image</a></span></div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						$delete = DB::getInstance()->remove('images', 'image_name', $_GET['imageName']);
						if ($delete == null) {
							@unlink("uploads/" . $_GET['imageName']);
							stdmsg("The <strong>image</strong> has been <strong>deleted</strong>.");
						}
						
					} catch (Exception $e) {
						stderr($e->getMessage());
					}	
					
				}
				
				?>
                
				<?php $images = DB::getInstance()->select("SELECT * FROM `images`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="imageTable" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>Image</th>
					  <th>ALT Text</th>
					  <th>&nbsp;</th>
					</tr>
				  </thead>
				  <tfoot>
					<tr>
					  <th>Image</th>
					  <th>ALT Text</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	  
						<?php foreach ($images as $image) { ?>					
							<tr>
								<td class="text-center"><img src="<?= urlFull(); ?>uploads/<?= $image['image_name']; ?>" class="img-thumbnail" alt="<?= $image['image_alt_text']; ?>"></td>
								<td><?= $image['image_alt_text']; ?></td>
								<td class="text-center"><a href="images.php?delete=1&amp;imageName=<?= $image['image_name']; ?>" onClick="return confirm('Delete the image?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
							</tr>				
						<?php } ?>	
				  </tbody>	  
				</table>
				</div>    							
                </div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>