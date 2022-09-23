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

					$count++;
					echo "<h1>" . seoFriendlyUrls($post['post_title'], $post['post_id']) . "</h1>";
					echo str_replace("\n\r", "<br /><br />", $post['post_body']);
				
				?>
				
			  </div>
			  <div class="card-footer mt-3"><?= ($count == $max) ? pagination($page, $total, $max) : "&nbsp;"; ?></div>
			</div>
			
			&nbsp;
			
			<?php } ?>			
			
		</div>
		
		<div class="col-md-3">
			<div class="card">
			  <div class="card-header"><i class="fas fa-cog" style="color:red"></i> Welcome!</div>
			  <ul class="list-group list-group-flush">		  
				<li class="list-group-item"><?= getValue("homepage_about"); ?></li>		
			  </ul>
			  <div class="card-footer">&nbsp;</div>
			</div>	
			
			<?php if (getValue("homepage_show_categories")) { ?>
			
			<div class="card mt-3">
			  <div class="card-header"><i class="fas fa-list-ol" style="color:green"></i> Categories</div>
			  <ul class="list-group list-group-flush">
			  
				<?php $categories = DB::getInstance()->select("SELECT * FROM `categories`"); ?>
				
			    <?php foreach($categories as $category) { ?>
				    <li class="list-group-item"><a href="<?= urlFull(); ?>category.php?categoryId=<?= $category['category_id']; ?>" class="text-decoration-none"><?= $category['category_name']; ?></a></li>				
				<?php } ?>	
				
			  </ul>
			  <div class="card-footer">&nbsp;</div>
			</div>	
			
			<?php } ?>	
			
		</div>
		
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
