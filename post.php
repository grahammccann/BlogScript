<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

    <?php 
	
	    $postId = isset($_GET['postId']) ? $_GET['postId'] : ''; 
        $update = isset($_GET['postId']) ? updatePostViews($_GET['postId']) : ''; 		
		$post = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$postId}'"); 
		$categoryId = $post['post_category_id'];
		
		$pagesArray = getAllPages();
		$categoriesArray = getAllCategories();
		$shortenersArray = getAllShorteners();
        //print_r($shortenersArray);
	?>
	
		<div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
			<div class="card-body">
				<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
					<ol class="breadcrumb" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px;">
						<li class="breadcrumb-item"><a href="<?= urlFull(); ?>" style="color: #0d6efd;"><i class="fas fa-home"></i></a></li>
						<li class="breadcrumb-item" aria-current="page"><?= seoFriendlyUrls($post['post_category_id'], getCategoryName($post['post_category_id']), true, false); ?></li>
						<li class="breadcrumb-item active" aria-current="page" style="color: #6c757d;"><?= $post['post_title']; ?></li>
					</ol>
				</nav>
			</div>
		</div>
		
		<div class="row">
		
		<div class="col-md-9">
		
			<div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">           
				<div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; text-transform: uppercase; text-align: center; background-color: transparent; padding: 1rem;">
					<small>
						<i class="fas fa-pencil-alt"></i> Posted <strong><?= date("F j, Y", strtotime($post['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($post['post_member_id']); ?></span></strong>.
					</small>

					<?php 
					if (checkUsersIpToEdit(getRealIp())) { 
					?>
					<span class="float-end">
						<a href="<?= urlFull(); ?>posts.php?delete=1&amp;postId=<?= $post['post_id']; ?>" onClick="return confirm('Delete the post?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Quick delete"><i class="far fa-trash-alt"></i></a>      

						<form action="<?php echo urlFull().'edit-post.php'; ?>" method="GET" style="display: inline;">
							<input type="hidden" name="postId" value="<?php echo trim($postId); ?>">
							<button type="submit" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Quick edit"><i class="fas fa-edit"></i></button>
						</form>
					</span>
					<?php } ?>

				</div>
				
				<div class="card-body">
					<h1 class="text-center" style="font-family: 'Helvetica Neue', sans-serif; font-size: 24px;"><?= $post['post_title']; ?></h1>
					<?php if (!empty($post['post_image'])) { ?>
						<p class="text-center">
							<img class="img-thumbnail" src="<?= getFeaturedImageToUse($post['post_image']) ?>" alt="<?= $post['post_image_alt_text']; ?>">
						</p>
					<?php } ?>
					<div id="post-cta"><?= displayCTAImage($post['post_affiliate_url']); ?></div>
					<div id="post-content"><?= generateTableOfContents($post['post_body'], $pagesArray, $categoriesArray, $post['post_image_alt_text'], $shortenersArray); ?></div>
					<div id="post-cta"><?= displayCTAImage($post['post_affiliate_url']); ?></div>
					<p style="font-size: 1.2em; text-align: center; font-family: 'Helvetica Neue', sans-serif;">Share this Article</p>
					<p class="text-center">
						<a href="https://twitter.com/intent/tweet?text=<?= $post['post_title']; ?>&amp;url=<?= sharingSocialMediaUrls($post['post_id'], $post['post_title']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Share on Twitter" target="_blank"><i class="fab fa-twitter fa-2x me-3" style="color: #55acee; transition: 0.3s;"></i></a>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?= sharingSocialMediaUrls($post['post_id'], $post['post_title']); ?>&amp;quote=Check this out!" data-bs-toggle="tooltip" data-placement="bottom" title="Share on Facebook" target="_blank"><i class="fab fa-facebook fa-2x me-3" style="color: #4267B2; transition: 0.3s;"></i></a>
					</p>
					<div id="post-content-videos">
						<?php if (startsWith($post['post_source_url'], "[") == true) {
							getSourceVideos($post['post_source_url']);
						} else {
							echo "&nbsp;";
						} ?>
					</div>
					<div id="post-content-source-urls">
						<?php if (startsWith($post['post_source_url'], "[") == true) {
							getSourceUrls($post['post_source_url']);
						} else {
							echo "&nbsp;";
						} ?>
					</div>
				</div>
				<div class="card-footer text-muted" style="font-size: 14px; background-color: transparent;">
					<small>
						<i class="fas fa-pencil-alt"></i> Updated on: <strong><?= date("F j, Y", strtotime($post['post_date_updated'])); ?></strong>
						<span class="float-end"><i class="fas fa-eye"></i> <?= $post['post_views']; ?></span>
					</small>
				</div>
			</div>
	  
		</div>
		
		<!-- categories / sidebars -->
		<div class="col-md-3">
		
			<?php 
			
			if (!empty(getValue("about_us_text"))) { 
						
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
			
			if (!empty(getValue("sidebar_cta_1_header"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-cta-1.php");  
				
			}
			
			?>	
			
			<?php 
			
			if (!empty(getValue("sidebar_cta_2_header"))) { 
						
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-cta-2.php");  
				
			}
			
			?>

		</div>
	
	</div>
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
