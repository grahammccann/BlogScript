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
	
	<?php if (!is_writable("uploads")) { stderr('Your <strong>uploads</strong> folder is not writable, please make the permissions <strong>777</strong> before we can upload new images.'); } ?>

	<div class="row">
	
		<div class="col-md-3" style="padding-bottom: 15px;">
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard.php"); ?>
			</div>
		</div>
		
		<div class="col-md-9">		
		
			<div class="card">
			  <div class="card-header"><i class="fas fa-cog"></i> Dashboard <span class="badge bg-success float-end"><a href="dashboard.php?sitemap=1" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create an xml sitemap">Generate XML Sitemap</a></span></div>
			   <div class="card-body">
			   
			    <?php
				
				if (isset($_GET['sitemap'])) {
					
					if (file_exists("sitemap.xml")) {
						createSitemap();
						stdmsg('Sitemap <strong>updated</strong>.');
					} else {
						createSitemap();
						stdmsg('Sitemap <strong>created</strong>.');
					}
					
				}
				
				?>
			  
				<?php
				
				if (isset($_POST['submitSettings'])) {
					try {
						foreach($_POST as $key => $value) {
							$u = DB::getInstance()->update(
								'options',
								'option_name',
								$key,
							[
								'option_value' => $value
							]); 					
						}
						stdmsg('Settings <strong>updated</strong>.');
					} catch (Exception $e) {
						stderr("There was an <strong>error</strong> updating the settings!");
					}
				}
				
				?>

				<?php $settings = DB::getInstance()->select('SELECT * FROM `options`'); ?>
					
				<form action="dashboard.php" method="post">
					
					<?php foreach($settings as $setting) { ?>
					  <p><small><span class="text-success"><?= htmlspecialchars($setting['option_description']); ?></span></small></p>
						<input class="form-control form-control" type="text" name="<?= $setting['option_name']; ?>" value="<?= $setting['option_value']; ?>" />		  
					  <hr />
					<?php } ?>
					  
					  <button type="submit" name="submitSettings" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>
				
				</form>               
				
                </div>
				<div class="card-footer">&nbsp;</div>
			</div>	
			
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>