<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>
	
	<div class="card">
	  <div class="card-body">
		<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= urlFull(); ?>" class="text-decoration-none"><i class="fas fa-home"></i></a></li>
			<li class="breadcrumb-item" aria-current="page"><a href="<?= urlFull(); ?>category.php?categoryId=<?= $_GET['categoryId']; ?>" class="text-decoration-none"><?= getPostersCategory($_GET['categoryId']); ?></a></li>
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
			WHERE `post_category_id`='{$_GET['categoryId']}'
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
		<div class="col-md-9">
		
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
			  <div class="card-footer mt-3"><?= ($count == $max) ? pagination($page, $total, $max, $pagination) : "<small><span class=\"float-end\"><i class=\"fas fa-eye\"></i> {$post['post_views']}</span></small>"; ?></div>
			</div>
			
			<?php } ?>	
			
		</div>
		
		<!-- categories / sidebars -->
		<div class="col-md-3">
			
			<?php 
			
			if (getValue("homepage_show_categories")) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-categories.php");  
				
			}
			
			?>	
			
			<?php 
			
			if (!empty(getValue("sidebar_cta_1"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-cta-1.php");  
				
			}
			
			?>	
			
			<?php 
			
			if (!empty(getValue("sidebar_cta_2"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-cta-2.php");  
				
			}
			
			?>

		</div>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
