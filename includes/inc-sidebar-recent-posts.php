	<div class="card">
	  <div class="card-header"><i class="fas fa-newspaper" style="color:green"></i> Recent Posts</div>
		  <ul class="list-group list-group-flush">	
		    <?php $posts = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_status`='published' AND `post_sticky`='0' ORDER BY `post_date` DESC  LIMIT 10"); ?>	  
			<?php foreach($posts as $post) { ?>
				<li class="list-group-item">
                    <img src="<?= getFeaturedImageToUse($post['post_image']) ?>" style="width: 25px; height: 25px"> <?= seoFriendlyUrls($post['post_title'], $post['post_id']); ?>
                </li>		
			<?php } ?>				
		  </ul>
	</div>