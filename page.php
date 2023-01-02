<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

    <?php
	
	    $slug = explode('=', $_SERVER['REQUEST_URI']);
		
		$page = DB::getInstance()->selectValues("SELECT * FROM `pages` WHERE `page_slug`='{$slug[1]}'"); 
	
	?>

	<h1 class="text-center"><?= $page['page_name']; ?></h1>
	
	<p><?= htmlspecialchars_decode($page['page_body']); ?></p>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
