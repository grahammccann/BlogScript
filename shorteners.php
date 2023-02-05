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
			
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard-analytics.php"); ?>
			</div>
			
		</div>
		
		<div class="col-md-9">
			<div class="card">
			  <div class="card-header"><i class="fas fa-external-link"></i> Shorteners <span class="badge bg-success float-end"><a href="new-shortener.php" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create a new shortener">New Shortener</a></span></div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						$delete = DB::getInstance()->remove('shorteners', 'shortener_id', $_GET['shortenerId']);
						if ($delete == null) {
							stdmsg("The <strong>shortener</strong> has been <strong>deleted</strong>.");
						}
						
					} catch (Exception $e) {
						stdErr($e->getMessage());
					}	
					
				}
				
				?>
                
				<?php $shorteners = DB::getInstance()->select("SELECT * FROM `shorteners`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="userTable" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>ID</th>
					  <th>Short</th>
					  <th>Original Url</th>
					  <th>Clicks</th>
					  <th>Added</th>					  
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </thead>
				  <tfoot>
					<tr>
					  <th>ID</th>
					  <th>Short</th>
					  <th>Original Url</th>
					  <th>Clicks</th>
					  <th>Added</th>					  
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	
                        <?php $index = 0; ?>				  
						<?php foreach ($shorteners as $shortener) { ?>	
						    <?php $index++; ?>
							<tr>
							    <td><?= $shortener['shortener_id']; ?></td>
								<td><a href="<?= urlFull() . $shortener['shortener_short']; ?>" target="_blank" class="text-decoration-none"><?= $shortener['shortener_short']; ?></a></td>
								<td><a href="<?= $shortener['shortener_original_url']; ?>" target="_blank" class="text-decoration-none"><?= $shortener['shortener_original_url']; ?></a><br>
								  <small>
									<a href="#" class="copy-link" id="copy-link-<?= $index; ?>" class="text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy URL to clipboard"><i class="fa-solid fa-copy"></i></a> 
									<span class="text-primary copy-link-target" id="copy-link-target-<?= $index; ?>"><?= urlFull(); ?>recommends/<?= $shortener['shortener_short']; ?>/</span>
								  </small>
                                </td>
								<td><?= $shortener['shortener_clicks_count']; ?></td>
								<td><?= date("m.d.y", strtotime($shortener['shortener_date'])); ?></td>
								<td class="text-center"><a href="edit-shortener.php?shortenerId=<?= $shortener['shortener_id']; ?>" class="btn btn-warning btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fas fa-edit"></i></a></td>
								<td class="text-center"><a href="shorteners.php?delete=1&amp;shortenerId=<?= $shortener['shortener_id']; ?>" onClick="return confirm('Delete the shortener?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
							</tr>								
						<?php } ?>	
				  </tbody>	  
				</table>
				
				<script>
				  <?php $index = 0; ?>
				  <?php foreach ($shorteners as $shortener) { ?>
					<?php $index++; ?>
					const copyLink<?= $index; ?> = document.querySelector("#copy-link-<?= $index; ?>");
					const copyLinkTarget<?= $index; ?> = document.querySelector("#copy-link-target-<?= $index; ?>");
					copyLink<?= $index; ?>.addEventListener("click", function() {
					  navigator.clipboard.writeText(copyLinkTarget<?= $index; ?>.textContent.trim()).then(function() {
						alert("URL copied to clipboard");
					  }, function(e) {
						alert("Could not copy text: ", e);
					  });
					});
				  <?php } ?>
				</script>
				
				</div>    				
                </div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>