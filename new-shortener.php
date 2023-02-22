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
			  <div class="card-header"><i class="fas fa-external-link"></i> Shortener</div>
                <div class="card-body">
				
				<script>
				function copyToClipboard(text) {
				  navigator.clipboard.writeText(text).then(function() {
					alert("Copied to clipboard: " + text);
				  }, function(err) {
					console.error("Could not copy text: ", err);
				  });
				}
				</script>				
				
				<?php

				$errors = [];

				if (isset($_POST['submitShortener'])) {
					
					$maxCharacters = 50;
					
					$dupe = DB::getInstance()->selectOneByField('shorteners', 'shortener_original_url', $_POST['shortener_original_url']);

					if (!empty($dupe)) {
						$errors[] = 'The <strong>Original URL</strong> is already in use please choose another.';
					}
					
					if (!empty($_POST['shortener_short'])) {
						if (strlen($_POST['shortener_short']) > $maxCharacters) {
							$errors[] = "The <strong>Short Code</strong> cannot be longer than <strong>{$maxCharacters}</strong> characters.";
						}
					}

					if (!empty($errors) > 0) {
						
						foreach ($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> ' . $error . '</div>';
						}
						
					} else {
						
						$short = "";
						if (!empty($_POST['shortener_short'])) {
							$short = $_POST['shortener_short'] = str_replace(' ', '-', $_POST['shortener_short']);
						} else {
							$short = $_POST['shortener_short'] = substr(md5(time()), 0, 7);
						}

						DB::getInstance()->insert(
							'shorteners',
							[
								'shortener_short' => strtolower($short),
								'shortener_original_url' => $_POST['shortener_original_url'],
								'shortener_clicks_count' => 0,
								'shortener_date' => date('Y-m-d H:i:s')
							]
						);

						stdmsg("Your new <strong>shortener</strong> has been <strong>added</strong>.");
						stdmsg("Shortened URL: <strong>" . strtolower(urlFull() . "recommends/" . $short . "/") . "</strong>");
						
					}
				}

				?>
				
				<form action="new-shortener.php" method="post">
				 
					<div class="mb-3">
						<label for="shortener_original_url" class="form-label"><strong>Original URL:</strong></label>
						<input type="text" class="form-control" id="shortener_original_url" name="shortener_original_url" required>
					</div>
					
					<div class="mb-3">
						<label for="shortener_short" class="form-label"><strong>Short Code (optional):</strong></label>
						<input type="text" class="form-control" id="shortener_short" name="shortener_short">
					</div>

					<button type="submit" name="submitShortener" class="btn btn-success float-end"><i class="fas fa-plus"></i> New Shortener</button>
				
				</form>               
				
                </div>
			</div>	
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>