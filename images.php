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
			  <div class="card-header"><i class="fas fa-image"></i> Images <span class="badge bg-success float-end"><a href="new-image.php" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Upload a new image">New Image</a></span></div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						$delete = DB::getInstance()->remove('images', 'image_name', $_GET['imageName']);
						if ($delete) {
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
					<table class="table table-striped" id="tableImages" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th>ID</th>
								<th>Image</th>
								<th>ALT Text</th>
								<th>Copy</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>ID</th>
								<th>Image</th>
								<th>ALT Text</th>
								<th>Copy</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
						<tbody>
							<?php foreach ($images as $image) { ?>
								<tr>
									<td><?= $image['image_id']; ?></td>
									<td class="text-center"><img src="<?= urlFull(); ?>uploads/<?= $image['image_name']; ?>" class="img-thumbnail" alt="<?= $image['image_alt_text']; ?>"></td>
									<td><?= $image['image_alt_text']; ?></td>
									<td class="text-center">
										<button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyImageURL('<?= $image['image_name']; ?>')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy image location to clipboard"><i class="fa-solid fa-copy"></i></button>
									</td>
									<td class="text-center">
										<a href="images.php?delete=1&amp;imageName=<?= $image['image_name']; ?>" onClick="return confirm('Delete the image?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>   							
			</div>	
		</div>
	</div>
	<script>
		function copyImageURL(imageName) {
			var url = "<?= urlFull(); ?>uploads/" + imageName;
			copyToClipboard(url);
		}

		function copyToClipboard(text) {
			var input = document.createElement('textarea');
			input.style.position = 'fixed';
			input.style.opacity = 0;
			input.value = text;
			document.body.appendChild(input);
			input.select();
			document.execCommand('copy');
			document.body.removeChild(input);
			alert("Copied to clipboard: " + text);
		}
	</script>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>