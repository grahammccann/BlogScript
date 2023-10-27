<?php
    $postData = [
        'post_title' => $post['post_title'],
        'post_id' => $post['post_id'],
        'post_image' => $post['post_image'],
        'post_image_alt_text' => $post['post_image_alt_text'],
        'post_body' => $post['post_body']
    ];

    $count++;
?>

<div class="row">
    <div class="col-md-4">
        <?= createPostImage($postData); ?>
    </div>
    <div class="col-md-8">
        <?= createPostTitle($postData); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= createPostBody($postData); ?>
        <?= createReadMoreButton($postData); ?>
    </div>
</div>
