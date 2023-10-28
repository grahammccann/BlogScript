<div class="card recent-posts-card">
    <div class="card-header">
        <i class="fas fa-newspaper"></i> Recent Posts
    </div>
    <ul class="list-group list-group-flush">  
        <?php 
        $posts = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_status`='published' AND `post_sticky`='0' ORDER BY `post_date` DESC  LIMIT 10");
        foreach($posts as $post) { 
            list($imageUrl, $imageWidth, $imageHeight) = getFeaturedImageToUse($post['post_image']);
            $displayWidth = 25;
            $displayHeight = (25/$imageWidth)*$imageHeight; 
        ?>
            <li class="list-group-item">
				<img src="<?= $imageUrl ?>" alt="<?= $post['post_image_alt_text']; ?>" style="width: <?= $displayWidth ?>px; height: <?= $displayHeight ?>px; margin-right: 10px;"> <?= seoFriendlyUrls($post['post_id'], $post['post_title'], false, false); ?>
            </li>    
        <?php } ?>           
    </ul>
</div>
