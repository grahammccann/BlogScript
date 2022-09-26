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
			WHERE   `post_status`='published'
			and     `post_sticky`='0'
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
			 <div class="card-header"><small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($sticky[0]['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($sticky[0]['post_member_id']); ?></span></strong>.</small><span class="float-end"><span class="badge bg-success"><i class="fa-solid fa-note-sticky"></i> STICKY POST</span></span></div>
			  <div class="card-body">	
		
				<?php	  			  

					echo "<h1>" . seoFriendlyUrls($sticky[0]['post_title'], $sticky[0]['post_id']) . "</h1>";
					if (!empty($post['post_image'])) { 
	                    echo "<p class=\"text-center\"><img src='" . getFeaturedImageToUse($sticky[0]['post_image']) . "' alt='" . getImageAltText($sticky[0]['post_image']) . "'></p>";
					}
					echo str_replace("\n\r", "<br /><br />", $sticky[0]['post_body']);
				
				?>
				
			  </div>
			  <div class="card-footer mt-3"><small><span class="float-end"><i class="fas fa-eye"></i> <?= $sticky[0]['post_views']; ?></span></small></div>
			</div>
			
			&nbsp;
			
			<?php } ?>	
		
			<?php 
			
			$count = 0;
			foreach($posts as $post) {	
			
			?>
		
			<div class="card">
			 <div class="card-header"><small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($post['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($post['post_member_id']); ?></span></strong>.</small></div>
			  <div class="card-body">	
		
				<?php	  			  

                    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-post-structure.php");
				
				?>
				
			  </div>
			  <div class="card-footer mt-3"><?= ($count == $max) ? pagination($page, $total, $max) : "<small><span class=\"float-end\"><i class=\"fas fa-eye\"></i> {$post['post_views']}</span></small>"; ?></div>
			</div>
			
			&nbsp;
			
			<?php } ?>				
			
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
