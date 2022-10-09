<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php"); 	
?>

<?php

if (isset($_POST['uploadPAA'])) {
	
	try
	{	

		$i = DB::getInstance()->insert(
			'posts',
		[
			'post_category_id' => 16,
			'post_member_id' => 2,
			'post_title' => htmlspecialchars(strip_tags($_GET['seed'])),
			'post_body' => $_GET['body'],
			'post_seo_title' => "",
			'post_seo_description' => "",
			'post_image' => "",
			'post_image_alt_text' => "",
			'post_status' => "published",
			'post_source_url' => "",
			'post_date' => date('Y-m-d H:i:s')
		]);

    } catch (Exception $e) {
        echo $e->getMessage();
    }	
	
}

?>
