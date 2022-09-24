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
		$posts = DB::getInstance()->select('
			SELECT  *
			FROM    `posts`
			WHERE   `post_status`="published"
			AND     `post_sticky`="0"
			ORDER   BY `post_date` DESC
			LIMIT   :from, :max_results',
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
		
		$total = DB::getInstance()->selectValue('SELECT count(*) FROM `posts`');
		
		if (!count($posts)) {
			stderr('There are <strong>no</strong> posts to show yet!');
			include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
			exit;
		}	

	?>
	
	<div class="row">	
	
		<div class="col-md-9">
		
			<?php
			
            $sticky = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_sticky`='1'");
			
			if (count($sticky) > 0) {
			
			?>
		
			<div class="card">
			 <div class="card-header"><i class="fas fa-sticky-note" style="color:green"></i> STICKY POST</div>
			  <div class="card-body">	
		
				<?php	  			  

					echo "<h1>" . seoFriendlyUrls($sticky[0]['post_title'], $sticky[0]['post_id']) . "</h1>";
	                echo "<p class=\"text-center\"><img src='" . getFeaturedImageToUse($sticky[0]['post_image']) . "' alt='" . getImageAltText($sticky[0]['post_image']) . "'></p>";
					echo str_replace("\n\r", "<br /><br />", $sticky[0]['post_body']);
				
				?>
				
			  </div>
			  <div class="card-footer mt-3">&nbsp;</div>
			</div>
			
			&nbsp;
			
			<?php } ?>	
		
			<?php 
			
			$count = 0;
			foreach($posts as $post) {	
			
			?>
		
			<div class="card">
			 <div class="card-header"><i class="fas fa-pencil-alt" style="color:blue"></i> LATEST POSTS</div>
			  <div class="card-body">	
		
				<?php	  			  

                    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-post-structure.php");
				
				?>
				
			  </div>
			  <div class="card-footer mt-3"><?= ($count == $max) ? pagination($page, $total, $max) : "&nbsp;"; ?></div>
			</div>
			
			&nbsp;
			
			<?php } ?>			
			
		</div>
		
		<div class="col-md-3">
		
			<div class="card">
			  <div class="card-header"><i class="fa-sharp fa-solid fa-user-tie" style="color:red"></i> Welcome!</div>
			  <ul class="list-group list-group-flush">		  
				<li class="list-group-item"><?= getValue("homepage_about"); ?></li>		
			  </ul>
			  <div class="card-footer">&nbsp;</div>
			</div>	
			
			<?php if (getValue("homepage_show_categories")) { ?>
			
            <?php
			
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-category-sidebar.php");  
			
			?>
			
			<?php } ?>	
			
		</div>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
