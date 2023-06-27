<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

    <?php
	
	    $categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : ''; 
        $category = DB::getInstance()->selectValues("SELECT * FROM `categories` WHERE `category_id`='{$categoryId}'"); 

	?>
	
	<div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
	  <div class="card-body">
		<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
			<ol class="breadcrumb" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px;">
			    <li class="breadcrumb-item"><a href="<?= urlFull(); ?>" style="color: #0d6efd;"><i class="fas fa-home"></i></a></li>
			    <li class="breadcrumb-item active" aria-current="page"><?= seoFriendlyUrls($category['category_id'], getCategoryname($category['category_id']), true, false); ?></li>
		    </ol>
		</nav>
	  </div>
	</div>
	
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
			WHERE `post_category_id`='{$categoryId}'
			AND `post_status`='published'
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
			stderr('There are <strong>no</strong> posts made in this category yet!');
			include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
			exit;
		}	

	?>
	
	<?php
	
	if (isset($_GET['categoryId'])) {
		$params = [];
		$pagination = [];
		$params['categoryId'] = $_GET['categoryId'];
		$pagination['categoryId'] = $_GET['categoryId'];		
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
				<div class="card-footer mt-3" style="font-size: 14px; background-color: transparent;"><small><span class="float-end"><i class="fas fa-eye"></i><?= $post['post_views']; ?></span></small></div>
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
