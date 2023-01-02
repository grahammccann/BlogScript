<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>
	
	<?php

		if (isset($_GET['page'])) {
			$page = $_GET['page'];
		} else {
			$page = 1;
		}
		$max = (int)getValue("homepage_pagination");
		$from = ($page * $max) - $max;
		$posts = DB::getInstance()->select("
			SELECT  *
			FROM    `posts`
			WHERE   `post_title` LIKE '%{$_GET['s']}%'
			ORDER   BY `post_date` DESC
			LIMIT   :from, :max_results",
		[
			'from' => [
				'type' => PDO::PARAM_INT,
				'value' => $from
			],
			'max_results' => [
				'type' => PDO::PARAM_INT,
				'value' => $max
			]
		]);
		
		$total = DB::getInstance()->selectValue("SELECT count(*) FROM `posts` WHERE `post_title` LIKE '%{$_GET['s']}%'");
		
		if (!count($posts)) {
			stderr('Sorry, there are <strong>no</strong> results for that keyword.');	
			include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
			die();				
		} else {
			stdmsg("Found you <strong>{$total}</strong> post(s) for the search <strong>{$_GET['s']}</strong>.");	
		}

	?>
	
	<?php
	
	if (isset($_GET['s'])) {
		$params = [];
		$pagination = [];
		$params['s'] = $_GET['s'];
		$pagination['s'] = $_GET['s'];		
	}
	
	?>
	
	<div class="row">
	
		<div class="col-md-9">
		
			<div class="card">
			 <div class="card-header bg-success text-white"><i class="fa-solid fa-magnifying-glass"></i> RESULT(S) ...</div>
			  <div class="card-body">			  			  
				<?php
				
				$count = 0;
				foreach($posts as $post) {
					$count++;
					echo "<h1><i class=\"fa-solid fa-hand-point-right\"></i> " . seoFriendlyUrls($post['post_title'], $post['post_id']) . "</h1>";
					echo "<p class=\"text-center\"><img class=\"img-thumbnail\" src=\"" . getFeaturedImageToUse($post['post_image']) . "\" alt=\"" . $post['post_image_alt_text'] . "\"></p>";
					echo "<p>" . str_replace("\n\r", "<br><br>", $post['post_body']) ."</p>";
					echo ($count == $max) ? "" : "<hr>";
				}
				
				?>
			  </div>
			  <div class="card-footer mt-3"><?= pagination($page, $total, $max, $pagination); ?></div>
			</div>
			
		</div>
		
		<div class="col-md-3">
		
			<?php 
			
			if (getValue("homepage_show_categories")) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-category-sidebar.php");  
				
			}
			
			?>	
			
		</div>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
