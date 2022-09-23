<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
	
	if (isset($_POST['member_email'])) {
		
		if (empty($_POST['member_email'])) {
			
			stdErr("You never entered an <strong>email</strong> address!");
			
		} else {
			
			try {
				
				$email = DB::getInstance()->selectOneByField('members', 'member_email', trim($_POST['member_email']));
				
				if (!empty($email)) {		
					if (sendEmail($_POST['member_email'], getValue("site_admin_email"), "Account password recovery!", "Here is your password: <strong>{$email['member_password']}")) {
						stdMsg(sprintf('Password sent to <font style="color: red"><a href="mailto:%s" class="text-decoration-none">%s</font></a>.', trim($_POST['member_email']), trim($_POST['member_email'])));
					} 
				} else {
					stdErr("That email was <strong>not</strong> found! are you sure it's correct?");
				}

			} catch (Exception $e) {
				stdErr($e->getMessage());
			}
			
		}
		
	}
	
}

?>