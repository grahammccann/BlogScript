<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>
	
	<?php //$posts = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_category_id`='{$_GET['categoryId']}' AND `post_status`='published' ORDER BY `post_date` ASC"); ?>

	<div class="card">
	  <div class="card-body">
		<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= urlFull(); ?>"><i class="fas fa-home"></i></a></li>
			<li class="breadcrumb-item" aria-current="page"><a href="<?= urlFull(); ?>category.php?categoryId=<?= $_GET['categoryId']; ?>"><?= getPostersCategory($_GET['categoryId']); ?></a></li>
		  </ol>
		</nav>
	  </div>
	</div>
	
	&nbsp;
	
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
			stderr('There is <strong>no</strong> posts made in this category yet!');
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
	
		<div class="col-md-9">
		
			<?php
			
			$count = 0;
			foreach($posts as $post) {	
			
			?>
		
			<div class="card">
			 <div class="card-header"><i class="fas fa-list-ol"></i> <?= getPostersCategory($_GET['categoryId']); ?></div>
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
