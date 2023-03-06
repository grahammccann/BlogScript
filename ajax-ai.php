<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
	
	if (isset($_POST['chatGPTSubmit'])) {
		
		$data = array(
			"prompt" => $_POST['textInput'],
			"temperature" => intval($_POST['temperatureToken']),
			"max_tokens" => intval($_POST['maxTokens'])
		);
		
		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer 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"
		);	

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.openai.com/v1/engines/davinci-codex/completions",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => $headers,
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$json_response = json_decode($response, true);
			//print_r($json_response );
			if (isset($json_response["choices"][0]["text"])) {
				echo $json_response["choices"][0]["text"];
			} else {
				echo "Error: No text returned from API.";
			}
		}
		
	} else if (isset($_POST['textInput'])) {
		echo $_POST['textInput'];
	}

}

?>
