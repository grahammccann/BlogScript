<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

    <?php 
	
	    $postId = isset($_GET['postId']) ? $_GET['postId'] : ''; 
        $update = isset($_GET['postId']) ? updatePostViews($_GET['postId']) : ''; 
		
		$post   = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$postId}'"); 
	?>
	
	<div class="card">
	  <div class="card-body">
		<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= urlFull(); ?>"><i class="fas fa-home"></i></a></li>
			<li class="breadcrumb-item" aria-current="page"><a href="<?= urlFull(); ?>category.php?categoryId=<?= $post['post_category_id']; ?>" class="text-decoration-none"><?= getPostersCategory($post['post_category_id']); ?></a></li>
			<li class="breadcrumb-item active" aria-current="page"><?= $post['post_title']; ?></li>
		  </ol>
		</nav>
	  </div>
	</div>
	
	&nbsp;
	
	<div class="row">
	
		<div class="col-md-9">
			<div class="card">
			 <div class="card-header"><small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($post['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($post['post_member_id']); ?></span></strong>.</small></div>
			  <div class="card-body">
				<h1><?= $post['post_title']; ?></h1>
				<?php if (!empty($post['post_image'])) { ?>
				    <p class="text-center"><img src="<?= getFeaturedImageToUse($post['post_image']) ?>" alt="<?= $post['post_image_alt_text']; ?>"></p>
				<?php } ?>
				<div id="post-content"><?= $post['post_body']; ?></div>
			  </div>
			  <div class="card-footer text-muted"><small><i class="fas fa-pencil-alt"></i> Updated on: <strong><?= date("F j, Y", strtotime($post['post_date_updated'])); ?></strong> <span class="float-end"><i class="fas fa-eye"></i> <?= $post['post_views']; ?></span></small></div>
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
