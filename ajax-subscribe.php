<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
	
	if (isset($_POST['newsletter-email'])) {
		$email = $_POST['newsletter-email'];

		if (strlen($email) > 150) {
			stderr("Your <strong>email</strong> address cannot be longer than 150 characters.");
		}

		$dupe = DB::getInstance()->selectOneByField('newsletters', 'newsletter_email', $email);

		if (!empty($dupe)) {
			stderr("Your <strong>email</strong> address is already on the subscribed list!");
		} else {
			$i = DB::getInstance()->insert(
				'newsletters',
				[
					'newsletter_email' => $email,
					'newsletter_date' => date('Y-m-d H:i:s')
				]
			);
			stdmsg("Your <strong>email</strong> address <strong>{$email}</strong> has been successfully subscribed.");
		}
	}

}

?>