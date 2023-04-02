<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
  include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
  
  if (isset($_POST['contact_name'])) {
    
    if (empty($_POST['contact_name']) || empty($_POST['contact_subject']) || empty($_POST['contact_email']) || empty($_POST['contact_message'])) {
      stdErr("Please fill in <strong>all</strong> fields.");
    } else {
      $recaptcha_secret_key = getValue("recaptcha2_secret_key");
      $recaptcha_response = $_POST['g-recaptcha-response'];
      $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
      
	  $recaptcha_data = array(
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
      );
      $recaptcha_options = array(
        'http' => array(
          'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
          'method' => 'POST',
          'content' => http_build_query($recaptcha_data)
        )
      );
	  
      $recaptcha_context = stream_context_create($recaptcha_options);
      $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
      $recaptcha_response_data = json_decode($recaptcha_result);
	  
      if ($recaptcha_response_data->success) {
        stdMsg("Thank you <strong>{$_POST['contact_name']}</strong>, we will be in touch soon!");
        sendEmail(getValue("site_admin_email"), $_POST['contact_email'], $_POST['contact_subject'], $_POST['contact_message']);
      } else {
        stdErr("reCAPTCHA verification failed. Please try again.");
      }
    }
	
    
  }
  
}


?>