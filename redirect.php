<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");

    function isBot($user_agent) {
        $bot_list = array(
            'Googlebot',
            'Bingbot',
            'Slurp',
            'DuckDuckBot',
            'Baiduspider',
            'YandexBot',
            'Sogou',
            'Exabot',
            'Facebot',
            'PetalBot',
			'AhrefsBot',
			'SemrushBot'
        );

        foreach ($bot_list as $bot) {
            if (stripos($user_agent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
?>

<?php
	if (empty($_GET['redirect'])) {
		stderr("URL <strong>not</strong> provided.");
	} else {
		$shortUrl = $_GET['redirect'];
		$redirect = DB::getInstance()->selectValues("SELECT * FROM `shorteners` WHERE `shortener_short`='{$shortUrl}'");
	    if (count($redirect) > 0) {
			$update = isset($_GET['redirect']) ? updateRedirectClicks($redirect['shortener_id']) : ''; 	
			
            if (!isBot($_SERVER['HTTP_USER_AGENT'])) {
                $clicks = recordClicks($_SERVER['REQUEST_URI'], getRealIp());
            }
			
			header("Location: {$redirect['shortener_original_url']}");
			exit;
		} else {
			stderr("Invalid redirect <strong>URL</strong>.");
		}
	}

?>