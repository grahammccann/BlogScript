	<div class="card">
	  <div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 24px; font-weight: bold; text-transform: capitalize; text-align: center; background-color: #f5f5f5; padding: 1rem;">
		<i class="fas fa-newspaper" style="color: green; font-size: 1.2em;"></i> Recent Posts
	  </div>
	  <ul class="list-group list-group-flush">  
		<?php $posts = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_status`='published' AND `post_sticky`='0' ORDER BY `post_date` DESC  LIMIT 10"); ?>    
		<?php foreach($posts as $post) { ?>
		  <li class="list-group-item">
			<img src="<?= getFeaturedImageToUse($post['post_image']) ?>" alt="<?= $post['post_image_alt_text']; ?>" style="width: 25px; height: 25px"> <?= seoFriendlyUrls($post['post_id'], $post['post_title'], false, false); ?>
		  </li>    
		<?php } ?>        
	  </ul>
	</div>