<?php
	$count++;
	echo "<h1>" . seoFriendlyUrls($post['post_title'], $post['post_id']) . "</h1>";
	echo "<p class=\"text-center\"><img class=\"img-thumbnail\" src=\"" . getFeaturedImageToUse($post['post_image']) . "\" alt=\"" . $post['post_image_alt_text'] . "\"></p>";
	echo "<p>" . $post['post_body'];
?>