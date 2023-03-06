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
			  <div class="card-header"><i class="fas fa-newspaper"></i> Newsletters <span class="badge bg-success float-end"><a href="#" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Send a newsletter">Send Newsletter</a></span></div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						$delete = DB::getInstance()->remove('newsletters', 'newsletter_id', $_GET['newsletterId']);
						if ($delete == null) {
							stdmsg("The <strong>email</strong> has been <strong>deleted</strong>.");
						}
						
					} catch (Exception $e) {
						stdErr($e->getMessage());
					}	
					
				}
				
				?>
                
				<?php $newsletters = DB::getInstance()->select("SELECT * FROM `newsletters`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="tableNewsletters" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>ID</th>
					  <th>Email</th>
					  <th>Added</th>
					  <th>&nbsp;</th>
					</tr>
				  </thead>
				  <tfoot>
					<tr>
					  <th>ID</th>
					  <th>Email</th>
					  <th>Added</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	  
						<?php foreach ($newsletters as $newsletter) { ?>	
						
							<tr>
							    <td><?= $newsletter['newsletter_id']; ?></td>
								<td><a href="mailto:<?= $newsletter['newsletter_email']; ?>" class="text-decoration-none" target="_blank"><?= $newsletter['newsletter_email']; ?></span></td>
								<td><?= date("m.d.y", strtotime($newsletter['newsletter_date'])); ?></td>
								<td class="text-center"><a href="newsletters.php?delete=1&amp;newsletterId=<?= $newsletter['newsletter_id']; ?>" onClick="return confirm('Delete the newsletter email?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
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