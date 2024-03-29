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
	
	    <?php if (!is_writable("uploads")) { stderr('Your <strong>uploads</strong> folder is not writable, please make the permissions <strong>777</strong> before we can upload new images.'); } ?>
	
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
				<div class="card-header">
					<i class="fas fa-cog"></i> Dashboard
				</div>
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

					if (isset($_GET['robots'])) {

						if (file_exists("robots.txt")) {
							stderr('Robots file already <strong>exists</strong> delete it and then generate it again.');
						} else {
							createRobotsFile();
							stdmsg('Robots file <strong>created</strong>.');
						}

					}

					?>

					<?php

					if (isset($_POST['submitSettings'])) {
						try {
							foreach ($_POST as $key => $value) {
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

						<?php foreach ($settings as $setting) { ?>
							<div class="mb-3">
								<label for="<?= $setting['option_name']; ?>" class="form-label text-success"><?= htmlspecialchars($setting['option_description']); ?></label>
								<input class="form-control form-control" type="text" id="<?= $setting['option_name']; ?>" name="<?= $setting['option_name']; ?>" value="<?= htmlentities($setting['option_value']); ?>">
							</div>
						<?php } ?>

						<button type="submit" name="submitSettings" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>

					</form>

				</div>
			</div>
			
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>