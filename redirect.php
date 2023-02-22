<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<?php
	// 
	if (empty($_GET['redirect'])) {
		stderr("URL <strong>not</strong> provided.");
	} else {
		$shortUrl = $_GET['redirect'];
		$redirect = DB::getInstance()->selectValues("SELECT * FROM `shorteners` WHERE `shortener_short`='{$shortUrl}'");
	    if (count($redirect) > 0) {
			$update = isset($_GET['redirect']) ? updateRedirectClicks($redirect['shortener_id']) : ''; 	
			$clicks = recordClicks($_SERVER['REQUEST_URI'], getRealIp()); 	
			header("Location: {$redirect['shortener_original_url']}");
			exit;
		} else {
			stderr("Invalid redirect <strong>URL</strong>.");
		}
	}

?>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
