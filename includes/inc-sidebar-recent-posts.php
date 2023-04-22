<div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; text-transform: uppercase; text-align: center; background-color: transparent; padding: 1rem;">
        <i class="fas fa-newspaper" style="color: green; font-size: 1.2em;"></i> Recent Posts
    </div>
    <ul class="list-group list-group-flush" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; color: #333;">  
        <?php $posts = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_status`='published' AND `post_sticky`='0' ORDER BY `post_date` DESC  LIMIT 10"); ?>    
        <?php foreach($posts as $post) { ?>
            <li class="list-group-item" style="display: flex; align-items: center; font-family: 'Helvetica Neue', sans-serif; font-size: 16px; color: #333;">
                <img src="<?= getFeaturedImageToUse($post['post_image']) ?>" alt="<?= $post['post_image_alt_text']; ?>" style="width: 25px; height: 25px; margin-right: 10px;"> <?= seoFriendlyUrls($post['post_id'], $post['post_title'], false, false); ?>
            </li>    
        <?php } ?>        
    </ul>
</div>