<?php
    $postData = [
        'post_title' => $post['post_title'],
        'post_id' => $post['post_id'],
        'post_image' => $post['post_image'],
        'post_image_alt_text' => $post['post_image_alt_text'],
        'post_body' => $post['post_body']
    ];

    $count++;
	
    echo createPostTitle($postData);
    echo createPostImage($postData);
    echo createPostBody($postData);
    echo createReadMoreButton($postData);
?>
