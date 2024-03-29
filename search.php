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
	
		<!-- posts -->
		<div class="col-md-<?= getValue("hide_all_sidebars") == 0 ? "12" : "9"; ?>">
		
			<?php
			
			$count = 0;
			foreach($posts as $post) {	
			
			?>
		

			<div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
				<div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; text-transform: uppercase; text-align: center; background-color: transparent; padding: 1rem;">
					<small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($post['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($post['post_member_id']); ?></span></strong>.</small>
				</div>
				<div class="card-body">
					<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-post-structure.php"); ?>
				</div>
				<div class="card-footer mt-3" style="font-size: 14px; background-color: transparent;"><?= ($count == $max) ? pagination($page, $total, $max) : "<small><span class=\"float-end\"><i class=\"fas fa-eye\"></i> {$post['post_views']}</span></small>"; ?></div>
			</div>
			
			<?php } ?>	
			
		</div>
		
        <?php if (getValue("hide_all_sidebars") != 0) { ?>
			
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
		
		<?php } ?>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
