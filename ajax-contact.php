<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
	
	if (isset($_POST['contact_name'])) {
		
		if (empty($_POST['contact_name']) || empty($_POST['contact_subject']) || empty($_POST['contact_email']) || empty($_POST['contact_message'])) {

            stdErr("Please fill in <strong>all</strong> fields.");
			
		} else {
			
            stdMsg("Thank you <strong>{$_POST['contact_name']}</strong>, we will be in touch soon!");
			
			sendEmail(getValue("site_admin_email"), $_POST['contact_email'], $_POST['contact_subject'], $_POST['contact_message']);
			
		}
		
	}
	
}

?>