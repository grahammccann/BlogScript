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
			AND     `post_sticky`='0'
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
	
		<div class="col-md-12">	    
		
			<?php 
			
			if (getValue("homepage_introduction_header")) { 
						
				include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header-introduction.php");  
				
			}
			
			?>	
			
		</div>
	 
	    <!-- posts -->
		<div class="col-md-9">
		
			<?php
			
            $sticky = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_sticky`='1' LIMIT 1");
			
			if (count($sticky) > 0) {
			
			?>
		
				<div class="card">
					<div class="card-header bg-success text-white" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; text-transform: uppercase; text-align: center; padding: 1rem;">
					  <small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($sticky[0]['post_date'])); ?></strong> by <strong><?= getPostersUsername($sticky[0]['post_member_id']); ?></strong>.</small>
					  <span class="float-end"><span class="badge bg-danger"><i class="fa-solid fa-note-sticky"></i></span></span>
					</div>
					<div class="card-body">	

					<?php	  			  

						echo "<h1>" . seoFriendlyUrls($sticky[0]['post_title'], $sticky[0]['post_id']) . "</h1>";
						echo "<p class=\"text-center\"><img class=\"img-thumbnail\" src=\"" . getFeaturedImageToUse($sticky[0]['post_image']) . "\" alt=\"" . $sticky[0]['post_image_alt_text'] . "\"></p>";
						echo $sticky[0]['post_body'];

					?>

					</div>
				  <div class="card-footer mt-3 bg-success text-white"><small><span class="float-end"><i class="fas fa-eye"></i> <?= $sticky[0]['post_views']; ?></span></small></div>
				</div>
			
			<?php } ?>	
		
			<?php 
			
			$count = 0;
			foreach($posts as $post) {	
			
			?>
		
				<div class="card">
				  <div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 24px; text-transform: uppercase; text-align: center; background-color: #f5f5f5; padding: 1rem;">
					<small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($post['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($post['post_member_id']); ?></span></strong>.</small>
				  </div>
				  <div class="card-body">    
					<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-post-structure.php"); ?>    
				  </div>
				  <div class="card-footer mt-3"><?= ($count == $max) ? pagination($page, $total, $max) : "<small><span class=\"float-end\"><i class=\"fas fa-eye\"></i> {$post['post_views']}</span></small>"; ?></div>
				</div>
			
			<?php } ?>				
			
		</div>
		
		<!-- categories / sidebars -->
		<div class="col-md-3">
		
			<?php 
			
			if (!empty(getValue("about_us_header")) && !empty(getValue("about_us_text"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-about-us.php");  
				
			}
			
			?>
		
			<?php 
									
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-recent-posts.php");  
		
			?>	
			
			<?php 
			
			if (getValue("homepage_show_categories")) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-categories.php");  
				
			}
			
			?>	
			
			<?php 
			
			if (!empty(getValue("sidebar_cta_1_header")) && !empty(getValue("sidebar_cta_1_text"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-cta-1.php");  
				
			}
			
			?>	
			
			<?php 
			
			if (!empty(getValue("sidebar_cta_2_header")) && !empty(getValue("sidebar_cta_2_text"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-cta-2.php");  
				
			}
			
			?>

		</div>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
