<?php
	$count++;
	echo "<h1>" . seoFriendlyUrls($post['post_title'], $post['post_id']) . "</h1>";
	echo "<p class=\"text-center\"><img class=\"img-thumbnail\" src=\"" . getFeaturedImageToUse($post['post_image']) . "\" alt=\"" . $post['post_image_alt_text'] . "\"></p>";
	echo "<p>" . strip_tags(truncateArticle($post['post_body'], 350));
	echo "<hr><a href=\"" .xmlFriendlyUrls($post['post_title'], $post['post_id']) ."/\" class=\"btn btn-success btn-sm\"><i class=\"fa-solid fa-book-open-reader\"></i> Read More</a>";
?>