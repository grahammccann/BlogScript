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
	
		<div class="col-md-3" style="padding-bottom: 15px;">
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard.php"); ?>
			</div>
		</div>
		
		<div class="col-md-9">
			<div class="card">
			  <div class="card-header"><i class="fas fa-pencil-alt"></i> Pages <span class="badge bg-success float-end"><a href="new-page.php" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create a new page">New Page</a></span></div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						$delete = DB::getInstance()->remove('pages', 'page_id', $_GET['pageId']);
						if ($delete == null) {
							stdmsg("The <strong>page</strong> has been <strong>deleted</strong>.");
						}
						
					} catch (Exception $e) {
						stdErr($e->getMessage());
					}	
					
				}
				
				?>
                
				<?php $pages = DB::getInstance()->select("SELECT * FROM `pages`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="postTable" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>Name</th>
					  <th>Added</th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </thead>
				  <tfoot>
					<tr>
					  <th>Name</th>
					  <th>Added</th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	  
						<?php foreach ($pages as $page) { ?>
						
							<tr>
								<td><span class="text-success"><?= $page['page_name']; ?></span></td>
								<td><?= date("m.d.y", strtotime($page['page_date'])); ?></td>
								<td class="text-center"><a href="edit-page.php?pageId=<?= $page['page_id']; ?>" class="btn btn-warning btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fas fa-edit"></i></a></td>
								<td class="text-center"><a href="pages.php?delete=1&amp;pageId=<?= $page['page_id']; ?>" onClick="return confirm('Delete the page?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
							</tr>				
						<?php } ?>	
				  </tbody>	  
				</table>
				</div>    								
                </div>
				<div class="card-footer">&nbsp;</div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>