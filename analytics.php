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
	
	<?php if (!is_writable("uploads")) { stderr('Your <strong>uploads</strong> folder is not writable, please make the permissions <strong>777</strong> before we can upload new images.'); } ?>

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
		
			<?php
			
			if (isset($_POST['submitIPRange'])) {
				
					
				$i = DB::getInstance()->insert(
					'ips',
				[
					'ip_range' => $_POST['ip_range'],
					'ip_date' => date('Y-m-d H:i:s')
				]);
				
				stdmsg("A new <strong>IP Range</strong> has been <strong>added</strong> and will be blocked.");
				
			}
			
			?>
			
			<div class="card">
			  <div class="card-header"><i class="fa-solid fa-computer-mouse"></i> IP Range</div>
                <div class="card-body">
			
				<form action="analytics.php" method="post">
				 
				  <div class="mb-3">
					<label for="category_name" class="form-label"><strong>IP Range:</strong></label>
					<input type="text" class="form-control" id="ip_range" name="ip_range" required>
				  </div>
				  
				  <button type="submit" name="submitIPRange" class="btn btn-success float-end"><i class="fas fa-plus"></i> IP Range</button>
				
				</form>  	

				</div>	
		    </div>		
	          	
			<div class="card">
			  <div class="card-header"><i class="fa-solid fa-computer-mouse"></i> Clicks</div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						if (isset($_GET['clickId'])) {
							
							$delete = DB::getInstance()->remove('clicks', 'click_id', $_GET['clickId']);
							if ($delete == null) {
								stdmsg("The <strong>click</strong> has been <strong>deleted</strong>.");
							}						
							
						}
						
						if (isset($_GET['ip'])) {
							
							$delete = DB::getInstance()->remove('clicks', 'click_ip', $_GET['ip']);
							if ($delete == null) {
								stdmsg("The <strong>IP</strong> has been <strong>deleted</strong>.");
							}						
							
						}
						
					} catch (Exception $e) {
						stdErr($e->getMessage());
					}	
					
				}
				
				?>
                
				<?php $clicks = DB::getInstance()->select("SELECT * FROM `clicks`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="tableClicks" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>ID</th>
					  <th>Product</th>
					  <th>IP</th>
					  <th>Country</th>
					  <th>Added</th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </thead>
				  <tfoot>
					<tr>
					  <th>ID</th>
					  <th>Product</th>
					  <th>IP</th>
					  <th>Country</th>
					  <th>Added</th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	  
						<?php foreach ($clicks as $click) { ?>
						
							<tr>
						    	<td><?= $click['click_id']; ?></td>
								<td><span class="text-primary"><?= $click['click_page']; ?></span></td>
								<td><?= $click['click_ip']; ?></td>
								<td>&nbsp;</td>
								<td><?= date("m.d.y", strtotime($click['click_date'])); ?></td>
								<td class="text-center"><a href="analytics.php?delete=1&amp;clickId=<?= $click['click_id']; ?>" onClick="return confirm('Delete the click?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
								<td class="text-center"><a href="analytics.php?delete=1&amp;ip=<?= $click['click_ip']; ?>" onClick="return confirm('Delete the IP?')" class="btn btn-warning btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
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