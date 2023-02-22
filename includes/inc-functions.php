<?php

function addImageToArticle($article) {	
	try {
		$pattern = '/<a href="([^#][^"]*\/recommends\/[^"]*)"|<a href=\'([^#][^\']*\/recommends\/[^\']*)\'/';
		preg_match($pattern, $article, $match);
		if (empty($match)) {
			return $article;
		}
		$link = $match[1] ?? $match[2];

		$image_html = '';
		if (strpos($article, $match[0]) < strpos($article, '</h2>')) {
			//$image_html = '<div style="text-align:center;"><a href="'.$link.'"><img class="img-fluid" src="'.urlFull().'images/img-check-price.png" alt="Check stock availability"></a></div>';		
		} else {
			$image_html = '<div style="text-align:center;"><a href="'.$link.'"><img class="img-fluid" src="'.urlFull().'images/img-visit-official-website.png" alt="Visit the official website"></a></div>';
		}

		preg_match('/<h2.*?>/', $article, $matches, PREG_OFFSET_CAPTURE);
		$h2_offset = $matches[0][1];
		
		if (empty($matches)) {
			return $article;
		}

		$new_article = substr_replace($article, $image_html, $h2_offset, 0);

		return $new_article;
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function checkUrl() {
    $currentUrl = $_SERVER['REQUEST_URI'];
    if (strpos($currentUrl, 'index.php?page=') !== false) {
        return true;
    } else {
        return false;
    }
}

function createSitemap() {
    try {
        $xml = DB::getInstance()->select("SELECT * FROM `posts` ORDER BY `post_date` ASC");
        
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" ";
        $xmlString .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
        $xmlString .= "xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 ";
        $xmlString .= "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";
        
        foreach ($xml as $row) {
            $xmlString .= "    <url>\n";
            $xmlString .= "        <loc><![CDATA[" . xmlUrls($row['post_title'], $row['post_id']) . "]]></loc>\n";
            $xmlString .= "        <lastmod>" . date(DATE_ATOM, time()) . "</lastmod>\n";
            $xmlString .= "        <changefreq>weekly</changefreq>\n";
            $xmlString .= "        <priority>1.0</priority>\n";
            $xmlString .= "    </url>\n";
        }
        
        $xmlString .= "</urlset>\n";
        
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = false;
		$dom->loadXML($xmlString);
		$dom->formatOutput = true;
		$dom->save('sitemap.xml');
    } catch (Exception $e) {
        stderr($e->getMessage());
    }
}

function createPostBody($postData)
{
    return "<p>".strip_tags(truncateArticle($postData['post_body'], 650))."</p>";
}

function createPostImage($postData)
{
    $postName = $postData['post_title'];
    $postId = $postData['post_id'];
    $imageUrl = getFeaturedImageToUse($postData['post_image']);
    $imageAltText = $postData['post_image_alt_text'];

    return "<p class='text-center'>" . mobileUrls($postName, $postId, $imageUrl, $imageAltText) . "</p>";
}

function createPostTitle($postData)
{
    return "<h1>".seoFriendlyUrls($postData['post_title'], $postData['post_id'])."</h1>";
}

function createReadMoreButton($postData)
{
    return "<div class='astrodivider'><div class='astrodividermask'></div><span><i>&#10038;</i></span></div><a href='".xmlUrls($postData['post_title'], $postData['post_id'])."' class='btn btn-success btn-md'>Read more</a>";
}

function createRobotsFile() {
  try {
    $robots = fopen("robots.txt", "a") or die("Unable to open file!");
    $lines = ["User-agent: *", "Disallow:", "Disallow: images/", "Disallow: includes/", "Disallow: uploads/", "Sitemap: ".urlFull()."sitemap.xml"];
    foreach ($lines as $line) {
		fwrite($robots, $line . "\n");
	}
    fclose($robots);
  } catch (Exception $e) {
    stderr($e->getMessage());
  }
}

function deleteAnyImages($postId) {
	try {
		$image = DB::getInstance()->selectValues("SELECT `post_image` FROM `posts` WHERE `post_id`='{$postId}'");
		@unlink("uploads/" . $image['post_image']);
	} catch(Exception $e) {
        echo $e->getMessage();
	}		
}

function displayArticle($article) {
	try {
		libxml_use_internal_errors(true);
		// This removes the awesomefont icons on update!
		//$article = preg_replace('/<[^\/>]*>([\s]?)*<\/[^>]*>/', '', $article);
/* 		$dom = new DOMDocument();
		$dom->loadHTML($article);
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$formattedArticle = $dom->saveHTML(); */
		return $article;
	} catch(Exception $e) {
        echo $e->getMessage();
	}		
}


function doesPostContainAnInternalLink($postBody) {
	try {
		if (strpos($postBody, urlFull()) !== false) {
			return true;
		} else {
			return false;
		}   
	} catch(Exception $e) {
        echo $e->getMessage();
	}	
}

function doTableCount($table) {
	try {
	    $c = DB::getInstance()->selectAll($table);
	    return count($c);	
	} catch(Exception $e) {
        echo $e->getMessage();		
	}	
}

function generateTableOfContents($htmlContent) {
    preg_match_all("/<h[1-6]>(.*?)<\/h[1-6]>/i", $htmlContent, $matches);

    $tableOfContents = array();
    foreach ($matches[1] as $heading) {
        $tableOfContents[] = $heading;
    }
    $i = 1;
    foreach($matches[0] as $heading) {
        $new_heading = preg_replace("/<h[1-6]>/", "<h2 id='$i'>", $heading);
        $htmlContent = str_replace($heading, $new_heading, $htmlContent);
        $i++;
    }
	if (count($tableOfContents) > 0) {
		echo "<ol class='table-of-contents'>";
		$i=1;
		foreach ($tableOfContents as $item) {
			echo "<li><span class='number'>".$i."</span>. <a href='#".$i."'>".$item."</a></li>";
			$i++;
		}
		echo "</ol>";		
	}
    echo $htmlContent;
}

function getCategoryname($categoryId) {
	try {
		$category = DB::getInstance()->selectValues("SELECT `category_name` FROM `categories` WHERE `category_id`='{$categoryId}'");
		return $category['category_name'];
	} catch(Exception $e) {
        echo $e->getMessage();
	}	
}

function getCountryFromIP($ip) {
	try {
		$country = geoip_country_code_by_name($ip);
		if ($country) {
			return $country;
		} else {
			return 'Unknown';
		}
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}


function getFeaturedImageToUse($imageName) {
	try {
		$fullUrl = "";
        if ($imageName == "img-post-generic.png") {
	        $fullUrl = urlFull() . "images/img-post-generic.png";
		} else {
		    $image = DB::getInstance()->selectValues("SELECT `post_image` FROM `posts` WHERE `post_image`='{$imageName}'");
	        $fullUrl = urlFull() . "uploads/" . $image['post_image'];			
		}
		return $fullUrl;
	} catch(Exception $e) {
        echo $e->getMessage();
	}		
}

function getGenericMeta($page, $postId, $metaType) {
    try {
        $replace = array("/", ".php");
        if ($postId == false && $page != "/page.php" && $page != "/category.php" ) {
            if ($metaType == 'title') {
                $meta = urlFull() . " | " . ucwords(str_replace($replace, "", $page));
            } else {
                $meta = urlFull();
            }
        } else if ($page == "/post.php") {
            $post = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$postId}'");
            if ($metaType == 'title') {
                $meta = $post['post_seo_title'];
            } elseif ($metaType == 'description') {
                $meta = $post['post_seo_description'];
            }
        } else if ($page == "/category.php") {
			$category = DB::getInstance()->selectValues("SELECT * FROM `categories` WHERE `category_id`='{$_GET['categoryId']}'");
			$meta = urlFull() . " | " . ucwords($category['category_name']);
			if (isset($_GET['page'])) {
				$meta .= " | Page {$_GET['page']}";
			}
        } else if ($page == "/page.php") {
            $slug = explode("=", $_SERVER['REQUEST_URI']);
            $page = DB::getInstance()->selectValues("SELECT * FROM `pages` WHERE `page_slug`='{$slug[1]}'");
            if ($metaType == 'title') {
                $meta = urlFull() . " | " . ucwords($page['page_name']);
            } else {
                $meta = urlFull() . " | " . ucwords($page['page_name']);
            }
        } else {
            if ($metaType == 'title') {
                $meta = urlFull();
            } else {
                $meta = urlFull();
            }
        }
        return $meta;
    } catch(Exception $e) {
        echo $e->getMessage();
    }
}

function getPublishedStatus($status) {
	try {
        if ($status == "published") {
			return '<span class="badge rounded-pill bg-success">Published</span>';
		}	
        if ($status == "draft") {
			return '<span class="badge rounded-pill bg-primary">Draft</span>';
		}	
        if ($status == "archived") {
			return '<span class="badge rounded-pill bg-danger">Archived</span>';
		}	
	} catch(Exception $e) {
        echo $e->getMessage();		
	}	
}

function getHeaderImage() {
	try {
		$image = "";
		$headerImage = DB::getInstance()->select("SELECT * FROM `images` WHERE `image_is_header`='yes' ORDER BY `image_date` DESC");
		if (count($headerImage) < 1) {
			$image = "images/img-original-header.png";
		} else {
			$image = "uploads/" . $headerImage[0]['image_name'];
		}
		return $image;	
	} catch(Exception $e) {
        echo $e->getMessage();		
	}	
}

function getImageAltText($imageName) {	
	try {
		$image = "";
		if ($imageName == "img-post-generic.png") {
			$image = "generic blog post alt text";
		} else {
		    $imageAltText = DB::getInstance()->selectValues("SELECT `image_alt_text` FROM `images` WHERE `image_name`='{$imageName}'");
		    $image = $imageAltText['image_alt_text'];			
		}
		return $image;
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}

function getLoggedInUserId($sessionUsername) {
	try {
		$id = DB::getInstance()->selectValues("SELECT `member_id` FROM `members` WHERE `member_username`='{$sessionUsername}'");
		return $id['member_id'];
	} catch(Exception $e) {
        echo $e->getMessage();
	}	
}

function getPostersUsername($userId) {
	try {
		$username = DB::getInstance()->selectValues("SELECT `member_username` FROM `members` WHERE `member_id`='{$userId}'");
		return $username['member_username'];
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}

function getPostersCategory($categoryId) {
	try {
		$category = DB::getInstance()->selectValues("SELECT `category_name` FROM `categories` WHERE `category_id`='{$categoryId}'");
		return $category['category_name'];
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}

function getPostTitleOnly($postId) {
	try {
		$title = DB::getInstance()->selectValues("SELECT `post_title` FROM `posts` WHERE `post_id`='{$postId}'");
		return $title['post_title'];
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}


function getRealIp() {
	try {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
		{
		  $ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		{
		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
		{
		  $ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function getSiteWordCount() {
	try {
		return DB::getInstance()->selectValue("SELECT SUM(LENGTH(post_body) - LENGTH(REPLACE(post_body, ' ', '')) + 1) FROM posts"); 
	} catch(Exception $e) {
        echo $e->getMessage();
	}	
}

function getSourceUrls($sourceUrls) {
	try {
        echo "<hr><h1>Sources</h1>";
		foreach (json_decode($sourceUrls) as $value) { 
		    echo "<ul><li><a href=\"{$value}\" class=\"text-decoration-none\" target=\"_blank\">{$value}</a></li></ul>";
		}
	} catch(Exception $e) {
        echo $e->getMessage();
	}	
}

function getSourceVideos($sourceUrls) {
	try {
	echo "<hr><h1>Videos</h1>";
		foreach (json_decode($sourceUrls) as $value) { 
		    if (startsWith($value, "https://www.youtube.com/")) { ?>
                <span class="d-flex justify-content-center align-items-center"><iframe width="560" height="315" src="<?= getYoutubeEmbedUrl($value); ?>" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></span>
			    <br>
			<?php 
			    break; // Break after we find 1 video, or Google gives us shit with indexing lol
			} 
		}
	} catch(Exception $e) {
        echo $e->getMessage();
	}	
}

function getTwitterImage($postId) {
	try {
		$image = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$postId}'");
		return $image['post_image'];	
	} catch(Exception $e) {
        echo $e->getMessage();
	}		
}

function getUsersDetails($member) {
	try {
        return DB::getInstance()->selectOneByField('members', 'member_username', $member);
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function getValue($optionValue) {
	try {
		$option = DB::getInstance()->selectValues("SELECT * FROM `options` WHERE `option_name`='{$optionValue}'");
		return $option['option_value'];	
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function getYoutubeEmbedUrl($url)
{
     $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
     $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($longUrlRegex, $url, $matches)) {
        $youtubeId = $matches[count($matches) - 1];
    }

    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtubeId = $matches[count($matches) - 1];
    }
    return 'https://www.youtube.com/embed/' . $youtubeId;
}

function indexNow($apiKey, $url, $keywords) {
    // Endpoint URL
    $endpoint = "https://api.indexnow.io/v1/index";

    // Data to be sent to the API
    $data = [
        "url" => $url,
        "keywords" => $keywords
    ];

    // Convert data to JSON
    $dataJson = json_encode($data);

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $apiKey,
        "Content-Type: application/json"
    ]);

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        return "cURL Error: " . curl_error($ch);
    } else {
        // Decode JSON response
        $responseData = json_decode($response, true);

        // Check for API errors
        if (isset($responseData["error"])) {
            return "API Error: " . $responseData["error"];
        } else {
            // Successful API response
            return "Indexed Successfully!";
        }
    }

    // Close cURL connection
    curl_close($ch);
}

function mobileUrls($postName, $postId, $imageUrl, $imageAltText) {
    try {
        $rootUrl = urlFull();
        $replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($postName));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
        if (substr($replace, -1) === '-') {
            $replace = rtrim($replace, '-');
        }
        return "<a class=\"text-decoration-none\" href=\"{$rootUrl}{$postId}-{$replace}/\"><img class='img-thumbnail' src='" . $imageUrl . "' alt='" . $imageAltText . "'></a>";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function pagination($page, $totalResults, $maxResults, $params = array())
{
    $totalPages = ceil($totalResults / $maxResults);
    $urlTemplate = $_SERVER['PHP_SELF'] . '?page=%s';
    $paramsString = '';
    if (count($params)) {
        foreach ($params as $key => $value) {
            $paramsString .= sprintf('&%s=%s', $key, urlencode($value));
        }
    }

    $loopFrom = ($page > 3)? $page - 3: 1;
    $loopTo = ($page < $totalPages -3)? $page + 3: $totalPages;

    ?>

    <nav aria-label="...">
		<ul class="pagination justify-content-center mt-3">
			<li<?php if ($page == 1) { ?> class="page-item disabled"<?php } ?>>
				<a class="page-link" href="<?php echo sprintf($urlTemplate, 1) . $paramsString ?>">First</a>
			</li>
			<li<?php if ($page == 1) { ?> class="page-item disabled"<?php } ?>>
				<a class="page-link" href="<?php echo ($page == 1)? 'javascript:void(0)': sprintf($urlTemplate, $page - 1) . $paramsString ?>">Prev</a>
			</li>
			<?php if ($loopFrom > 1) { ?>
				<li class="page-item">
					<a class="page-link" href="<?php echo sprintf($urlTemplate, (int) round($page / 2)) . $paramsString ?>">...</a>
				</li>
			<?php } ?>
			<?php for ($i = $loopFrom; $i <= $loopTo; $i++) { ?>
				<li<?php if ($page == $i) { ?> class="page-item active"<?php } ?>>
					<a class="page-link" href="<?php echo ($page == $i)? 'javascript:void(0)': sprintf($urlTemplate, $i) . $paramsString ?>"><?php echo $i ?></a>
				</li>
			<?php } ?>
			<?php if ($loopTo < $totalPages) { ?>
				<li class="page-item">
					<a class="page-link" href="<?php echo sprintf($urlTemplate, (int) round($totalPages - $page / 2)) . $paramsString ?>">...</a>
				</li>
			<?php } ?>
			<li<?php if (($page == $totalPages) || !$totalPages) { ?> class="page-item disabled"<?php } ?>>
				<a class="page-link" href="<?php echo (($page == $totalPages) || !$totalPages)? 'javascript:void(0)': sprintf($urlTemplate, $page + 1) . $paramsString ?>">Next</a>
			</li>
			<li<?php if (($page == $totalPages) || !$totalPages) { ?> class="page-item disabled"<?php } ?>>
				<a class="page-link" href="<?php echo sprintf($urlTemplate, $totalPages) . $paramsString ?>">Last</a>
			</li>
		</ul>
	</nav>

    <?php
}

function recordClicks($page, $ip) {
    try {
		$i = DB::getInstance()->insert(
			'clicks',
		[
			'click_page' => $page,
			'click_ip' => $ip,
			'click_date' => date('Y-m-d H:i:s')
		]);		
	} catch(Exception $e) {
        echo $e->getMessage();		
	}	
}

function removeEmptyClasses($html) {
    return preg_replace('/class=""/', '', $html);
}


function resizeImage($source, $destination, $size, $quality = null) { 
    try {
		
		$ext = strtolower(pathinfo($source)['extension']);

		if (!in_array($ext, ["bmp", "gif", "jpg", "jpeg", "png", "webp"])) {
		    return false;
		}

		if (!file_exists($source)) {
		    return false;
		}

		$dimensions = getimagesize($source);
		$width      = $dimensions[0];
		$height     = $dimensions[1];

		if (is_array($size)) {
			$new_width  = $size[0];
			$new_height = $size[1];
		} else {
			$new_width  = ceil(($size/100) * $width);
			$new_height = ceil(($size/100) * $height);
		}

		$fnCreate = "imagecreatefrom" . ($ext == "jpg" ? "jpeg" : $ext);
		$fnOutput = "image" . ($ext == "jpg" ? "jpeg" : $ext);

		$original = $fnCreate($source);
		$resized  = imagecreatetruecolor($new_width, $new_height); 

		if ($ext == "png" || $ext == "gif") {
			imagealphablending($resized, false);
			imagesavealpha($resized, true);
			imagefilledrectangle($resized, 0, 0, $new_width, $new_height, imagecolorallocatealpha($resized, 255, 255, 255, 127));
		}

		imagecopyresampled($resized, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		if (is_numeric($quality)) {
		    $fnOutput($resized, $destination, $quality);
		} else {
		    $fnOutput($resized, $destination);
		}

		imagedestroy($original);
		imagedestroy($resized);
		
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function rssUrls($postName, $postId) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($postName));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if(substr($replace, -1) === '-'){
           $replace = rtrim($replace, '-'); 
        }
		return "{$rootUrl}{$postId}-{$replace}/";
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function sendEmail($emailTo, $emailFrom, $emailSubject, $emailMessage) {
	$emailBody = '
			<!doctype html>
			<html>
			  <head>
				<meta name="viewport" content="width=device-width">
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<title>'. urlFull() .'</title>
				<style>
				@media only screen and (max-width: 620px) {
				  table[class=body] h1 {
					font-size: 28px !important;
					margin-bottom: 10px !important;
				  }
				  table[class=body] p,
						table[class=body] ul,
						table[class=body] ol,
						table[class=body] td,
						table[class=body] span,
						table[class=body] a {
					font-size: 16px !important;
				  }
				  table[class=body] .wrapper,
						table[class=body] .article {
					padding: 10px !important;
				  }
				  table[class=body] .content {
					padding: 0 !important;
				  }
				  table[class=body] .container {
					padding: 0 !important;
					width: 100% !important;
				  }
				  table[class=body] .main {
					border-left-width: 0 !important;
					border-radius: 0 !important;
					border-right-width: 0 !important;
				  }
				  table[class=body] .btn table {
					width: 100% !important;
				  }
				  table[class=body] .btn a {
					width: 100% !important;
				  }
				  table[class=body] .img-responsive {
					height: auto !important;
					max-width: 100% !important;
					width: auto !important;
				  }
				}
				@media all {
				  .ExternalClass {
					width: 100%;
				  }
				  .ExternalClass,
						.ExternalClass p,
						.ExternalClass span,
						.ExternalClass font,
						.ExternalClass td,
						.ExternalClass div {
					line-height: 100%;
				  }
				  .apple-link a {
					color: inherit !important;
					font-family: inherit !important;
					font-size: inherit !important;
					font-weight: inherit !important;
					line-height: inherit !important;
					text-decoration: none !important;
				  }
				  #MessageViewBody a {
					color: inherit;
					text-decoration: none;
					font-size: inherit;
					font-family: inherit;
					font-weight: inherit;
					line-height: inherit;
				  }
				  .btn-primary table td:hover {
					background-color: #34495e !important;
				  }
				  .btn-primary a:hover {
					background-color: #34495e !important;
					border-color: #34495e !important;
				  }
				}
				</style>
			  </head>
			  <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
				<span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">' . nl2br($emailMessage) . '</span>
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
				  <tr>
					<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
					<td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
					  <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

						<!-- START CENTERED WHITE CONTAINER -->
						<table role="presentation" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">
						
						<!-- START MAIN CONTENT AREA -->
						  <tr>
							<td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
							  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
								<tr>
								  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
								    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><strong>Subject:</strong></p>
									<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">' . $emailSubject . '</p>
								    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><strong>Email:</strong></p>
									<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><a href="mailto:' . $emailFrom . '">' . $emailFrom . '</a></p>
								    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><strong>Message:</strong></p>
									<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">' . nl2br($emailMessage) . '</p>
								  </td>
								</tr>
							  </table>
							</td>
						  </tr>
						<!-- END MAIN CONTENT AREA -->			
						
						</table>

						<!-- START FOOTER -->
						<div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
						  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
							<tr>
							  <td class="content-block powered-by" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
								Powered by <a href="'. urlFull() .'" style="color: #999999; font-size: 12px; text-align: center; text-decoration: none;">'. urlFull() .'</a>.
							  </td>
							</tr>
						  </table>
						</div>
						<!-- END FOOTER -->

					  <!-- END CENTERED WHITE CONTAINER -->
					  </div>
					</td>
					<td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
				  </tr>
				</table>
			  </body>
			</html>
		';
		
	$emailHeaders  = 'MIME-Version: 1.0' . "\r\n";
	$emailHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$emailHeaders .=
		'From: ' . $emailFrom . "\r\n" .
		'Reply-To: ' . $emailFrom . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	return mail($emailTo, urlFull() . " - " . $emailSubject, $emailBody, $emailHeaders);
}

function sharingSocialMediaUrls($productId, $productName) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($productName));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if (substr($replace, -1) === '-'){
           $replace = rtrim($replace, '-'); 
        }
		return $rootUrl . $productId . "-" . $replace . "/";
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}

function seoFriendlyUrls($postName, $postId) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($postName));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if(substr($replace, -1) === '-'){
           $replace = rtrim($replace, '-'); 
        }
		return "<a class=\"text-decoration-none\" href=\"{$rootUrl}{$postId}-{$replace}/\">{$postName}</a>";
	} catch(Exception $e) {
        echo $e->getMessage();
	}
}

function stdmsg($text) { ?><div class="alert alert-success" role="alert"><i class="fas fa-check"></i> <?= $text; ?></div><?php }
function stderr($text) { ?><div class="alert alert-danger" role="alert"><i class="fas fa-times"></i> <?= $text; ?></div> <?php }

function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return substr($haystack, 0, $length) === $needle;
}

function truncateArticle($text, $chars = 120) {
	try {
		if (strlen($text) > $chars) {
			$text = $text . ' ';
			$text = substr($text, 0, $chars);
			$text = substr($text, 0, strrpos($text ,' '));
			$text = $text . ' ...';
		}
		return $text;
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function updatePostViews($postId) {
	try {	
		if (is_numeric($postId)) {
			$countUpdate   = DB::getInstance()->selectOneByField('posts', 'post_id', $postId);
			$countToUpdate = $countUpdate['post_views'];
			$u = DB::getInstance()->update(
				'posts',
				'post_id',
				$postId,
			[
				'post_views' => $countToUpdate + 1
			]);			
		}			
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function updateRedirectClicks($redirectId) {
	try {	
		if (is_numeric($redirectId)) {
			$countUpdate   = DB::getInstance()->selectOneByField('shorteners', 'shortener_id', $redirectId);
			$countToUpdate = $countUpdate['shortener_clicks_count'];
			$u = DB::getInstance()->update(
				'shorteners',
				'shortener_id',
				$redirectId,
			[
				'shortener_clicks_count' => $countToUpdate + 1
			]);			
		}			
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function uploadImage($imageName, $imageTemp, $imageAltTextName) {
    try {
        $validFormats = array("jpg", "png", "gif", "jpeg");
        $imageNameFinal = "";
        if (strlen($imageName) > 0) {
            list($txt, $ext) = explode(".", $imageName);
            if (in_array($ext, $validFormats)) {
                $size = filesize($imageTemp);
                if ($size > 5000000) {
                    stdmsg("File size must be <strong>less</strong> than 5MB.");
                    return;
                }
                $mime = mime_content_type($imageTemp);
                if (!strstr($mime, 'image')) {
                    stdmsg("Invalid file type. Only <strong>images</strong> are allowed.");
                    return;
                }
                $randomInt = rand();
                $imageAltText = str_replace(" ", "-", $imageAltTextName);
                $imageNameFinal = $imageAltText . "-" . $randomInt . "." . $ext;
                $imagePath = "uploads/" . $imageNameFinal;
                if (!is_dir("uploads")) {
                    mkdir("uploads");
                }
                if (file_exists($imagePath)) {
                    stdmsg("The <strong>image</strong> file already exists.");
                    return;
                }
                if (move_uploaded_file($imageTemp, $imagePath)) {
                    return $imageNameFinal;
                }
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function urlFull() {
	try {
	    return sprintf("%s://%s/", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME']);
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

function xmlUrls($postName, $postId) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($postName));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if(substr($replace, -1) === '-'){
           $replace = rtrim($replace, '-'); 
        }
		return "{$rootUrl}{$postId}-{$replace}/";
	} catch(Exception $e) {
        echo $e->getMessage();		
	}
}

?>