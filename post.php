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
	
	<div class="row">
	
		<div class="col-md-9">
			<div class="card">
			 <div class="card-header"><small><i class="fas fa-pencil-alt"></i> Posted on <strong><?= date("F j, Y", strtotime($post['post_date'])); ?></strong> by <strong><span class="text-success"><?= getPostersUsername($post['post_member_id']); ?></span></strong>.</small></div>
			  <div class="card-body">
				<h1><?= $post['post_title']; ?></h1>
				<?php if (!empty($post['post_image'])) { ?>
				    <p class="text-center"><img class="img-thumbnail" src="<?= getFeaturedImageToUse($post['post_image']) ?>" alt="<?= $post['post_image_alt_text']; ?>"></p>
				<?php } ?>
				<p class="text-center"><?= !empty(getValue("ads_post_top")) ? html_entity_decode(getValue("ads_post_top")) : "&nbsp;"; ?></p>
				<div id="post-content"><?= generateTableOfContents(addImageToArticle($post['post_body'])); ?></div>
                <br>
				<div class="astrodivider"><div class="astrodividermask"></div><span><i>&#10038;</i></span></div>
				<p style="font-size: 1.2em; text-align: center;">Share this Article</p>
				<p class="text-center">
				   <a href="https://twitter.com/intent/tweet?text=<?= $post['post_title']; ?>&amp;url=<?= sharingSocialMediaUrls($post['post_id'], $post['post_title']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Share on Twitter" target="_blank"><i class="fab fa-twitter fa-2x me-3" style="color: #55acee;"></i></a>
				   <a href="https://www.facebook.com/sharer/sharer.php?u=<?= sharingSocialMediaUrls($post['post_id'], $post['post_title']); ?>&amp;quote=Check this out!" data-bs-toggle="tooltip" data-placement="bottom" title="Share on Facebook" target="_blank"><i class="fab fa-facebook fa-2x me-3" style="color: #4267B2;"></i></a>              
				</p>
				<div id="post-content-videos"><?php if (startsWith($post['post_source_url'], "[") == true) { getSourceVideos($post['post_source_url']); } else { echo "&nbsp;"; } ?></div>
				<div id="post-content-source-urls"><?php if (startsWith($post['post_source_url'], "[") == true) { getSourceUrls($post['post_source_url']); } else { echo "&nbsp;"; } ?></div>
			  </div>
			  <div class="card-footer text-muted"><small><i class="fas fa-pencil-alt"></i> Updated on: <strong><?= date("F j, Y", strtotime($post['post_date_updated'])); ?></strong> <span class="float-end"><i class="fas fa-eye"></i> <?= $post['post_views']; ?></span></small></div>
			</div>
		</div>
		
		<!-- categories / sidebars -->
		<div class="col-md-3">
		
			<?php 
									
			    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sidebar-recent-posts.php");  
		
			?>	
			
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
	
	<!--<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header bg-secondary text-white text-center py-2"><i class="fas fa-link"></i> <strong>Related Articles</strong></div>
				<div class="card-body">
					<?php 
						$relatedArticles = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_category_id`='{$categoryId}' ORDER BY RAND() LIMIT 3");
						foreach($relatedArticles as $relatedArticle) { 
					?>
					<div class="card mb-3" style="padding-top: 10px;">
						<div class="card-body">
							<img src="<?= getFeaturedImageToUse($relatedArticle['post_image']) ?>" alt="<?= $relatedArticle['post_image_alt_text']; ?>" class="img-thumbnail mx-auto position-relative" width="150" height="150">
						        <?= seoFriendlyUrls($relatedArticle['post_title'], $relatedArticle['post_id']) ?>
							</a>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>-->
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
