<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
	
	if (isset($_POST['chatGPTSubmit'])) {

		$api_key = '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';
		$post_fields = array(
			"model" => "gpt-3.5-turbo",
			"messages" => array(
				array(
					"role" => "user",
					"content" => $_POST['textInput']
				)
			),
			"max_tokens" => (int)$_POST['maxTokens'],
			"temperature" => (int)$_POST['temperatureToken']
		);

		$header  = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $api_key
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error: ' . curl_error($ch);
		}
		curl_close($ch);

		$response = json_decode($result);
		echo $response->choices[0]->message->content;
		
    }

}

?>
