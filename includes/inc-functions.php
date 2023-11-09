<?php

function anchorTagSafeReplace($pattern, $callback, $content, &$count) {
    $regex = '/(?:<a [^>]*>[^<]*<\/a>(*SKIP)(*FAIL))|(?:\b' . $pattern . '\b)/i';
    return preg_replace_callback($regex, $callback, $content, -1, $count);
}

function interlinkArticles($content, $pagesArray, $categoriesArray, $excludeHeadings, $moneyKeyword, $shortenersArray) {
    $linkedPages = array();
    $linkedShorteners = array();
    
    // Split content into header and non-header blocks
    $splitContent = preg_split("/(<h[1-6][^>]*>.*?<\/h[1-6]>)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    $processedContent = '';

    // Iterate through each block
    foreach($splitContent as $section) {
        // Check if the block is a header
        if (!preg_match("/^<h[1-6][^>]*>.*?<\/h[1-6]>$/", $section)) {
            // Process non-header sections
            // Bold the money keyword
            if (!empty($moneyKeyword)) {
                $section = preg_replace('/\b' . preg_quote($moneyKeyword) . '\b/i', '<strong>$0</strong>', $section, 1);
            }

            foreach ($pagesArray as $page) {
                $pageId = getPageIdFromUrl($page);
                if (!empty($pageId)) {
                    $pageTitle = getPageTitleFromUrl($page);
                    $commonSequences = findFilteredCommonSequences($section, $pageTitle);

                    $callback = function ($matches) use ($page, &$linkedPages) {
                        $text = $matches[0];
                        $lowerText = strtolower($text);
                        if (!in_array($lowerText, $linkedPages)) {
                            $linkedPages[] = $lowerText;
                            return '<a href="' . $page . '" class="text-decoration-none" style="font-weight: bold;">' . $text . '</a>';
                        } else {
                            return $text;
                        }
                    };

                    $count = 0;
                    foreach ($commonSequences as $commonsequence) {
                        if (in_array(strtolower($commonsequence), $excludeHeadings)) {
                            continue;
                        }
                        $section = anchorTagSafeReplace(preg_quote($commonsequence), $callback, $section, $count);
                    }
                }
            }

            foreach ($categoriesArray as $category) {
                $categoryId = getCategoryIdFromUrl($category);
                if (!empty($categoryId)) {
                    $categoryTitle = getCategoryTitleFromUrl($category);
                    $commonSequences = findFilteredCommonSequences($section, $categoryTitle);

                    $callback = function ($matches) use ($category, &$linkedPages) {
                        $text = $matches[0];
                        $lowerText = strtolower($text);
                        if (!in_array($lowerText, $linkedPages)) {
                            $linkedPages[] = $lowerText;
                            return '<a href="' . $category . '" class="text-decoration-none" style="font-weight: bold;">' . $text . '</a>';
                        } else {
                            return $text;
                        }
                    };

                    $count = 0;
                    foreach ($commonSequences as $commonsequence) {
                        if (in_array(strtolower($commonsequence), $excludeHeadings)) {
                            continue;
                        }
                        $section = anchorTagSafeReplace(preg_quote($commonsequence), $callback, $section, $count);
                    }
                }
            }
    
            foreach ($shortenersArray as $shortener) {
                $shortenerTitle = getShortenerTitleFromUrl($shortener);
                $commonSequences = findExactCommonSequences($section, $shortenerTitle);

                $callback = function ($matches) use ($shortener, &$linkedShorteners) {
                    $text = $matches[0];
                    $lowerText = strtolower($text);
                    if (!in_array($lowerText, $linkedShorteners)) {
                        $linkedShorteners[] = $lowerText;
                        return '<a href="' . $shortener . '" class="text-decoration-none" style="color: red;"><strong>' . $text . '</strong> <i class="fas fa-external-link" aria-hidden="true"></i></a>';
                    } else {
                        return $text;
                    }
                };

                $count = 0;
                foreach ($commonSequences as $commonsequence) {
                    if (in_array(strtolower($commonsequence), $excludeHeadings)) {
                        continue;
                    }
                    $section = anchorTagSafeReplace(preg_quote($commonsequence), $callback, $section, $count);
                }
            }
        }
        // Add the section to the processed content
        $processedContent .= $section;
    }
    
    return $processedContent;
}

function findExactCommonSequences($content, $title) {
    $commonSequences = array();
    $words1 = preg_split('/\s+/', $content);
    $words2 = preg_split('/\s+/', $title);
    $len1 = count($words1);
    $len2 = count($words2);
    for ($i = 0; $i < $len1; $i++) {
        for ($j = 0; $j < $len2; $j++) {
            $k = 0;
            while ($i + $k < $len1 && $j + $k < $len2 && strcasecmp($words1[$i + $k], $words2[$j + $k]) == 0) {
                $k++;
            }
            if ($k == $len2) { // Only accept exact matches
                $commonSequences[] = implode(' ', array_slice($words1, $i, $k));
            }
        }
    }
    return $commonSequences;
}

function findFilteredCommonSequences($str1, $str2) {
    $commonSequences = array();
    $words1 = preg_split('/\s+/', $str1);
    $words2 = preg_split('/\s+/', $str2);
    $len1 = count($words1);
    $len2 = count($words2);
    for ($i = 0; $i < $len1; $i++) {
        for ($j = 0; $j < $len2; $j++) {
            $k = 0;
            while ($i + $k < $len1 && $j + $k < $len2 && strcasecmp($words1[$i + $k], $words2[$j + $k]) == 0) {
                $k++;
            }
            if ($k > 0) {
                $commonSequences[] = implode(' ', array_slice($words1, $i, $k));
            }
        }
    }

    $filteredSequences = array();
    foreach ($commonSequences as $sequence) {
        $sequenceWithoutStopWords = removeStopWords($sequence);
        if (atleastXWords($sequenceWithoutStopWords) != '') {
            $isSubsequence = false;
            foreach ($filteredSequences as $prevSequence) {
                if (strpos($prevSequence, $sequenceWithoutStopWords) !== false) {
                    $isSubsequence = true;
                    break;
                }
            }
            if (!$isSubsequence) {
                $filteredSequences[] = $sequenceWithoutStopWords;
            }
        }
    }
    return $filteredSequences;
}

function getCategoryIdFromUrl($url) {
    preg_match('/\/category\/(\d+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

function getCategoryTitleFromUrl($url) {
    $parts = explode('/', rtrim($url, '/'));
    $lastPart = end($parts);
    $categoryTitle = str_replace('-', ' ', $lastPart);
    return urldecode($categoryTitle);
}

function getPageIdFromUrl($url) {
    preg_match('/\/(\d+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

function getPageTitleFromUrl($url) {
    $parts = explode('/', rtrim($url, '/'));
    $lastPart = end($parts);
    $pattern = '/^.*\/(\d+)-(.*)\/$/';
    preg_match($pattern, $url, $matches);
    $pageTitle = $matches[2];
    $pageTitle = str_replace('-', ' ', $pageTitle);
    return urldecode($pageTitle);
}

function getShortenerTitleFromUrl($url) {
    $pattern = '/recommends\/(.*)\//';
    preg_match($pattern, $url, $matches);

    if (!empty($matches[1])) {
        $shortenerTitle = str_replace('-', ' ', $matches[1]);
        return urldecode($shortenerTitle);
    }

    return null;
}

function atleastXWords($text) {
    $words = preg_split('/\s+/', $text);
    return count($words) < 2 ? '' : $text;
}

function removeStopWords($text) {
	$stopWords = array(
		'a',
		'about',
		'above',
		'after',
		'again',
		'against',
		'all',
		'am',
		'an',
		'and',
		'any',
		'are',
		'as',
		'at',
		'be',
		'because',
		'been',
		'before',
		'being',
		'below',
		'between',
		'both',
		'but',
		'by',
		'can',
		'cannot',
		'could',
		'did',
		'do',
		'does',
		'doing',
		'don',
		'down',
		'during',
		'each',
		'few',
		'for',
		'from',
		'further',
		'had',
		'has',
		'have',
		'having',
		'he',
		'her',
		'here',
		'hers',
		'herself',
		'him',
		'himself',
		'his',
		'how',
		'i',
		'if',
		'in',
		'into',
		'is',
		'it',
		'its',
		'itself',
		'just',
		'me',
		'more',
		'most',
		'must',
		'my',
		'myself',
		'no',
		'nor',
		'not',
		'now',
		'of',
		'off',
		'on',
		'once',
		'only',
		'or',
		'other',
		'our',
		'ours',
		'ourselves',
		'out',
		'over',
		'own',
		's',
		'same',
		'shan',
		'she',
		'should',
		'so',
		'some',
		'such',
		't',
		'than',
		'that',
		'the',
		'their',
		'theirs',
		'them',
		'themselves',
		'then',
		'there',
		'these',
		'they',
		'this',
		'those',
		'through',
		'to',
		'too',
		'under',
		'until',
		'up',
		'very',
		'was',
		'we',
		'were',
		'what',
		'when',
		'where',
		'which',
		'while',
		'who',
		'whom',
		'why',
		'with',
		'would',
		'you',
		'your',
		'yours',
		'yourself',
		'yourselves'
	);
    $words = preg_split('/\s+/', $text);
    $filteredWords = array_diff($words, $stopWords);
    return implode(' ', $filteredWords);
}

function checkUrl() {
    $currentUrl = $_SERVER['REQUEST_URI'];
    if (strpos($currentUrl, 'index.php?page=') !== false) {
        return true;
    } else {
        return false;
    }
}

function checkUsersIpToEdit($ipFromUser) {
    $allowedIps = getValue("ip_edit");
    if (strpos($allowedIps, '|') !== false) {
        // If there are multiple allowed IPs, split the string into an array
        $allowedIps = explode('|', $allowedIps);
        foreach ($allowedIps as $allowedIp) {
            if ($allowedIp === $ipFromUser) {
                return true;
            }
        }
    } else {
        // If there is only one allowed IP, compare it with the user's IP directly
        if ($allowedIps === $ipFromUser) {
            return true;
        }
    }
    return false;
}

function cleanUpImages() {
	return DB::getInstance()->select("SELECT * FROM `images`");
}

function countPostsInCategories($categoryId) {
    try {
        $count = DB::getInstance()->select("SELECT COUNT(*) as `count` FROM `posts` WHERE `post_category_id`='{$categoryId}'");
        return (int)$count[0]['count'];
    } catch (Exception $e) {
        stderr($e->getMessage());
    }   
}

function createSitemap() {
    try {
        $xml = DB::getInstance()->select("SELECT * FROM `posts` ORDER BY `post_date` DESC");
        
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>';
        $xmlString .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" ";
        $xmlString .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
        $xmlString .= "xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 ";
        $xmlString .= "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";
        
        foreach ($xml as $row) {
            $xmlString .= "    <url>\n";
            $xmlString .= "        <loc><![CDATA[" . rawUrls($row['post_id'], $row['post_title'], false) . "]]></loc>\n";
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

function createPostTitle($postData) {
    return "<h1>".seoFriendlyUrls($postData['post_id'], $postData['post_title'], false, false)."</h1>";
}

function createPostImage($postData) {
    $postName = $postData['post_title'];
    $postId = $postData['post_id'];
    $imageUrl = getFeaturedImageToUse($postData['post_image']);
    $imageAltText = $postData['post_image_alt_text'];

    return "<p class='text-center'>" . mobileUrls($postName, $postId, $imageUrl, $imageAltText) . "</p>";
}

function createPostBody($postData) {
    return "<p>".strip_tags(truncateArticle($postData['post_body'], 650))."</p>";
}

function createReadMoreButton($postData) {
    // Limit the title length for brevity
    $shortTitle = substr($postData['post_title'], 0, 30) . '...';
    
    return "<a href='" . rawUrls($postData['post_id'], $postData['post_title'], false) . "' class='btn btn-success btn-md w-100'><i class='fas fa-arrow-right'></i> Read more about: " . $shortTitle . "</a>";
}

function createRobotsFile() {
  try {
    $robots = fopen("robots.txt", "a") or die("Unable to open file!");
    $lines = ["User-agent: *", "Disallow:", "Disallow: /images/", "Disallow: /includes/", "Disallow: /uploads/", "Sitemap: ".urlFull()."sitemap.xml"];
    foreach ($lines as $line) {
		fwrite($robots, $line . "\n");
	}
    fclose($robots);
  } catch (Exception $e) {
    stderr($e->getMessage());
  }
}

function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function deleteAnyImages($postId) {
	try {
		$image = DB::getInstance()->selectValues("SELECT `post_image` FROM `posts` WHERE `post_id`='{$postId}'");
		@unlink("uploads/" . $image['post_image']);
	} catch(Exception $e) {
        stderr($e->getMessage());
	}		
}

function displayArticle($article) {
	try {
		//libxml_use_internal_errors(true);
		return $article;
	} catch(Exception $e) {
        stderr($e->getMessage());
	}		
}

function displayCTAImage($affiliateUrl) {
    try {
        if ($affiliateUrl != "...") {
            return '
            <div style="text-align:center; margin:auto; max-width:300px; border: 2px solid #28a745; border-radius: 15px; box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2); transition: 0.3s; background-color: #28a745; margin-bottom: 20px;">
                <a href="'.$affiliateUrl.'" style="display: block; padding: 15px; text-decoration: none; color: white; font-weight: bold; text-transform: uppercase;">
                    Check Prices & Stock Availability
                </a>
            </div>';
        } else {
            return '&nbsp;';
        }
    } catch(Exception $e) {
        stderr($e->getMessage());
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
        stderr($e->getMessage());
	}	
}

function doesPostContainMonetizationLinks($postBody) {
	try {
		if (strpos($postBody, "/recommends/") !== false) {
			return true;
		} else {
			return false;
		}   
	} catch(Exception $e) {
        stderr($e->getMessage());
	}	
}

function doTableCount($table) {
	try {
	    $c = DB::getInstance()->selectAll($table);
	    return count($c);	
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}	
}

function generateTableOfContents($htmlContent, $pagesArray, $categoriesArray, $moneyKeyword, $shortenersArray) {
    preg_match_all("/<h[1-6]>(.*?)<\/h[1-6]>/i", $htmlContent, $matches);

	$tableOfContents = array();
	foreach ($matches[1] as $heading) {
		$tableOfContents[] = $heading;
	}
	$htmlContent = interlinkArticles($htmlContent, $pagesArray, $categoriesArray, $tableOfContents, $moneyKeyword, $shortenersArray);

    $i = 1;
    foreach($matches[0] as $heading) {
        $new_heading = preg_replace("/<h[1-6]>/", "<h2 id='$i'>", $heading);
        $htmlContent = str_replace($heading, $new_heading, $htmlContent);
        $i++;
    }
	if (count($tableOfContents) > 0) {
		echo "<div class='table-of-contents-container'>";
        echo "<h2>Table of Contents</h2>";
		echo "<ol class='table-of-contents'>";
		$i=1;
		foreach ($tableOfContents as $item) {
			echo "<li><span class='number'>".$i."</span>. <a href='#".$i."'>".$item."</a></li>";
			$i++;
		}
		echo "</ol>";		
        echo "</div>";
	}
    echo $htmlContent;
}

function getAllCategories() {
	try {
	    $pages = [];
		$query = DB::getInstance()->select("SELECT * FROM `categories`");
		foreach ($query as $page) {		
			$pages[] = rawUrls($page['category_id'], $page['category_name'], true);
		}
	    return $pages;	
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}		
}

function getAllPages() {
	try {
	    $pages = [];
		$query = DB::getInstance()->select("SELECT * FROM `posts`");
		foreach ($query as $page) {
			$pages[] = rawUrls($page['post_id'], $page['post_title'], false);
		}
	    return $pages;	
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}		
}

function getAllShorteners() {
	try {
	    $pages = [];
		$query = DB::getInstance()->select("SELECT * FROM `shorteners`");
		foreach ($query as $page) {
			$pages[] = urlFull() . "recommends/" . $page['shortener_short'] . "/";
		}
	    return $pages;	
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}		
}

function getArticleData($count) {
	try {
		return DB::getInstance()->select("SELECT * FROM (SELECT * FROM `posts`) sub WHERE LENGTH(`post_body`) - LENGTH(REPLACE(`post_body`, ' ', '')) + 1 <= {$count};"); 
	} catch(Exception $e) {
        stderr($e->getMessage());
	}	
}

function getCategoryname($categoryId) {
	try {
		$category = DB::getInstance()->selectValues("SELECT `category_name` FROM `categories` WHERE `category_id`='{$categoryId}'");
		return $category['category_name'];
	} catch(Exception $e) {
        stderr($e->getMessage());
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
        stderr($e->getMessage());
	}
}

function getFeaturedImageToUse($imageName) {
    try {
        $fullUrl = $imageName == "img-post-generic.png" ? urlFull() . "images/{$imageName}" : urlFull() . "uploads/{$imageName}";
        list($width, $height, $type, $attr) = getimagesize($fullUrl);
        return [$fullUrl, $width, $height];
    } catch(Exception $e) {
        stderr($e->getMessage());
    }        
}

function getGenericMeta($page, $postId, $metaType) {
    try {
        $replace = array("/", ".php");
        if ($postId == false && $page != "/page.php" && $page != "/category.php" ) {
            if ($metaType == 'title' || $metaType == 'description') {
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
            $meta = urlFull() . " | " . ucwords($page['page_name']);
        } else {
            $meta = urlFull();
        }
        return $meta;
    } catch(Exception $e) {
        stderr($e->getMessage());
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
        stderr($e->getMessage());		
	}	
}

function getHeaderImage() {
    try {
        $headerImage = DB::getInstance()->select("SELECT * FROM `images` WHERE `image_is_header`='yes' ORDER BY `image_date` DESC");
        $imagePath = count($headerImage) < 1 ? "images/img-original-header.png" : "uploads/" . $headerImage[0]['image_name'];
        list($width, $height) = getimagesize($_SERVER['DOCUMENT_ROOT'] . "/" . $imagePath);
        return [$imagePath, $width, $height];
    } catch(Exception $e) {
        stderr($e->getMessage());        
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
        stderr($e->getMessage());
	}
}

function getLoggedInUserId($sessionUsername) {
	try {
		$id = DB::getInstance()->selectValues("SELECT `member_id` FROM `members` WHERE `member_username`='{$sessionUsername}'");
		return $id['member_id'];
	} catch(Exception $e) {
        stderr($e->getMessage());
	}	
}

function getPostersUsername($userId) {
	try {
		$username = DB::getInstance()->selectValues("SELECT `member_username` FROM `members` WHERE `member_id`='{$userId}'");
		return $username['member_username'];
	} catch(Exception $e) {
        stderr($e->getMessage());
	}
}

function getPostersCategory($categoryId) {
	try {
		$category = DB::getInstance()->selectValues("SELECT `category_name` FROM `categories` WHERE `category_id`='{$categoryId}'");
		return $category['category_name'];
	} catch(Exception $e) {
        stderr($e->getMessage());
	}
}

function getPostTitleOnly($postId) {
	try {
		$title = DB::getInstance()->selectValues("SELECT `post_title` FROM `posts` WHERE `post_id`='{$postId}'");
		return $title['post_title'];
	} catch(Exception $e) {
        stderr($e->getMessage());
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
        stderr($e->getMessage());		
	}
}

function getPostWordCount($postId, $postBody) {
	try {
		return DB::getInstance()->selectValue("SELECT SUM(LENGTH(post_body) - LENGTH(REPLACE(post_body, ' ', '')) + 1) FROM `posts` WHERE `post_id`='{$postId}'"); 
	} catch(Exception $e) {
        stderr($e->getMessage());
	}	
}

function getSiteWordCount() {
	try {
		return DB::getInstance()->selectValue("SELECT SUM(LENGTH(post_body) - LENGTH(REPLACE(post_body, ' ', '')) + 1) FROM `posts`"); 
	} catch(Exception $e) {
        stderr($e->getMessage());
	}	
}

function getSiteArticlesToDelete($count) {
	try {
		return DB::getInstance()->selectValue("SELECT COUNT(*) as row_count FROM (SELECT post_body FROM posts) sub WHERE LENGTH(post_body) - LENGTH(REPLACE(post_body, ' ', '')) + 1 <= {$count};"); 
	} catch(Exception $e) {
        stderr($e->getMessage());
	}	
}

function getSourceUrls($sourceUrls) {
	try {
        echo "<hr><h1>Sources</h1>";
		foreach (json_decode($sourceUrls) as $value) { 
		    echo "<ul><li><a href=\"{$value}\" class=\"text-decoration-none\" target=\"_blank\">{$value}</a></li></ul>";
		}
	} catch(Exception $e) {
        stderr($e->getMessage());
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
        stderr($e->getMessage());
	}	
}

function getTwitterImage($postId) {
	try {
		$image = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$postId}'");
		return $image['post_image'];	
	} catch(Exception $e) {
        stderr($e->getMessage());
	}		
}

function getUsersDetails($member) {
	try {
        return DB::getInstance()->selectOneByField('members', 'member_username', $member);
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}
}

function getValue($optionValue) {
	try {
		$option = DB::getInstance()->selectValues("SELECT * FROM `options` WHERE `option_name`='{$optionValue}'");
		if (!empty($option)) {
			return $option['option_value'];
		} else {
			return null;
		}
	} catch(Exception $e) {
        stderr($e->getMessage());		
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
        list($width, $height) = getimagesize($imageUrl[0]);
        $replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($postName));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
        if (substr($replace, -1) === '-') {
            $replace = rtrim($replace, '-');
        }
        return "<a class=\"text-decoration-none\" href=\"{$rootUrl}{$postId}-{$replace}/\"><img class='img-thumbnail' src='" . $imageUrl[0] . "' alt='" . $imageAltText . "' width='" . $width . "' height='" . $height . "'></a>";
    } catch (Exception $e) {
        stderr($e->getMessage());
    }
}

function pagination($page, $totalResults, $maxResults, $params = array()) {
    $totalPages = 0;
    if (is_numeric($totalResults) && is_numeric($maxResults) && $maxResults > 0) {
        $totalPages = ceil($totalResults / $maxResults);
    }
    $urlTemplate = $_SERVER['PHP_SELF'] . '?page=%s';
    $paramsString = '';
    if (is_array($params)) {
        foreach ($params as $key => $value) {
            $paramsString .= sprintf('&%s=%s', $key, urlencode($value));
        }
    }

    $loopFrom = ($page > 3) ? $page - 3 : 1;
    $loopTo = ($page < $totalPages - 3) ? $page + 3 : $totalPages;

    if ($totalPages > 0) {
    ?>

    <nav aria-label="...">
        <ul class="pagination justify-content-center mt-3">
            <li class="page-item <?php if ($page == 1) echo 'disabled'; ?>">
                <?php if ($page == 1) { ?>
                    <span class="page-link">First</span>
                <?php } else { ?>
                    <a class="page-link" href="<?php echo sprintf($urlTemplate, 1) . $paramsString ?>">First</a>
                <?php } ?>
            </li>
            <li class="page-item <?php if ($page == 1) echo 'disabled'; ?>">
                <?php if ($page == 1) { ?>
                    <span class="page-link">Prev</span>
                <?php } else { ?>
                    <a class="page-link" href="<?php echo sprintf($urlTemplate, $page - 1) . $paramsString ?>">Prev</a>
                <?php } ?>
            </li>
            <?php if ($loopFrom > 1) { ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php } ?>
            <?php for ($i = $loopFrom; $i <= $loopTo; $i++) { ?>
                <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                    <?php if ($page == $i) { ?>
                        <span class="page-link"><?php echo $i ?></span>
                    <?php } else { ?>
                        <a class="page-link" href="<?php echo sprintf($urlTemplate, $i) . $paramsString ?>"><?php echo $i ?></a>
                    <?php } ?>
                </li>
            <?php } ?>
            <?php if ($loopTo < $totalPages) { ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php } ?>
            <li class="page-item <?php if ($page == $totalPages) echo 'disabled'; ?>">
                <?php if ($page == $totalPages) { ?>
                    <span class="page-link">Next</span>
                <?php } else { ?>
                    <a class="page-link" href="<?php echo sprintf($urlTemplate, $page + 1) . $paramsString ?>">Next</a>
                <?php } ?>
            </li>
            <li class="page-item <?php if ($page == $totalPages) echo 'disabled'; ?>">
                <?php if ($page == $totalPages) { ?>
                    <span class="page-link">Last</span>
                <?php } else { ?>
                    <a class="page-link" href="<?php echo sprintf($urlTemplate, $totalPages) .$paramsString ?>">Last</a>
                <?php } ?>
            </li>
        </ul>
    </nav>
<?php } }

function performArticlePurge($articleId, $articleImage, $imageName) {
    try {
		$delete1 = DB::getInstance()->remove('images', 'image_name', $imageName);
        $delete2 = DB::getInstance()->remove('posts', 'post_id', $articleId);
        if (!empty($articleImage)) {
            $imagePath = 'uploads/' . $articleImage;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    } catch(Exception $e) {
        stderr($e->getMessage());
    }
}

function rawUrls($id, $name, $category) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($name));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if (substr($replace, -1) === '-'){
           $replace = rtrim($replace, '-'); 
        }
		if ($category) {
			return "{$rootUrl}category/{$id}-{$replace}/";		
		} else {
		    return "{$rootUrl}{$id}-{$replace}/";		
		}
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}
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
        stderr($e->getMessage());		
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
        stderr($e->getMessage());		
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
        stderr($e->getMessage());		
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

function sharingSocialMediaUrls($id, $name) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($name));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if (substr($replace, -1) === '-'){
           $replace = rtrim($replace, '-'); 
        }
		return $rootUrl . $id . "-" . $replace . "/";
	} catch(Exception $e) {
        stderr($e->getMessage());
	}
}

function seoFriendlyUrls($id, $name, $category, $dropdown) {
	try {
		$rootUrl = urlFull();
		$replace = preg_replace("/[^A-Za-z0-9\-]/", "-", strtolower($name));
        $replace = preg_replace("~[^-\w]+~", "", $replace);
        $replace = preg_replace('~-+~', '-', $replace);
		if(substr($replace, -1) === '-') {
           $replace = rtrim($replace, '-'); 
        }
		if ($category && $dropdown) {
			return "<a class=\"dropdown-item text-decoration-none\" href=\"{$rootUrl}category/{$id}-{$replace}/\">{$name}</a>";
		}
		
		if ($category == true && $dropdown != true) {
			return "<a class=\"text-decoration-none\" href=\"{$rootUrl}category/{$id}-{$replace}/\">{$name}</a>";			
		} else {
			return "<a class=\"text-decoration-none\" href=\"{$rootUrl}{$id}-{$replace}/\">{$name}</a>";
		}
	} catch(Exception $e) {
        stderr($e->getMessage());
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
        stderr($e->getMessage());		
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
        stderr($e->getMessage());		
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
        stderr($e->getMessage());		
	}
}

function uploadImage($imageName, $imageTemp, $imageAltTextName, $addWatermark = true) {
    try {
        if (!isset($imageTemp) || !is_uploaded_file($imageTemp)) {
            stderr("Invalid <strong>file</strong> upload.");
            return;
        }

        $validFormats = array("jpg", "png", "gif", "jpeg");
        $imageNameFinal = "";
        if (strlen($imageName) > 0) {
            list($txt, $ext) = explode(".", $imageName);
            if (in_array(strtolower($ext), $validFormats)) {
                $size = filesize($imageTemp);
                $maxFileSize = 5000000; // maximum file size in bytes (5MB)
                if ($size > $maxFileSize) {
                    stderr("File size must be <strong>less</strong> than " . $maxFileSize / 1000000 . "MB.");
                    return;
                }
                $type = exif_imagetype($imageTemp);
                if (!$type || !in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
                    stderr("Invalid file type. Only <strong>images</strong> are allowed.");
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

                // Resize and compress the image
                $maxWidth = 1200; 
                $maxHeight = 800;
                $quality = 80; 

                $src = imagecreatefromstring(file_get_contents($imageTemp));
                list($width, $height) = getimagesize($imageTemp);
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = ceil($width * $ratio);
                $newHeight = ceil($height * $ratio);

                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Save the resized image
                switch ($type) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($dst, $imageTemp, $quality);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($dst, $imageTemp, $quality / 10);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($dst, $imageTemp);
                        break;
                }

                imagedestroy($src);
                imagedestroy($dst);

                if (move_uploaded_file($imageTemp, $imagePath)) {
                    if ($addWatermark) {
                        addWatermark($imagePath);
                    }
                    return $imageNameFinal;
                }
            }
        }
    } catch(Exception $e) {
        stderr($e->getMessage());      
    }
}

function addWatermark($imagePath) {
    $image = imagecreatefromstring(file_get_contents($imagePath));
    
    $font = "font/TiltWarp-Regular.ttf";
    $fontSize = 16;
    $text = urlFull();

    // Calculate text dimensions
    $textBox = imagettfbbox($fontSize, 0, $font, $text);
    $textWidth = $textBox[2] - $textBox[0];
    $textHeight = $textBox[3] - $textBox[5];
    
    // Position text to the bottom right with some padding
    $padding = 20;
    $textX = imagesx($image) - $textWidth - $padding;
    $textY = imagesy($image) - $textHeight - $padding;
    
    // Create a semi-transparent background rectangle for the text
    $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 63); // Semi-transparent black
    $textColor = imagecolorallocate($image, 255, 255, 255); // White text color
    
    imagefilledrectangle($image, $textX - 10, $textY - 10, $textX + $textWidth + 10, $textY + $textHeight + 10, $bgColor);
    
    // Add the watermark text
    imagettftext($image, $fontSize, 0, $textX, $textY + $textHeight, $textColor, $font, $text);

    imagepng($image, $imagePath);
    imagedestroy($image);
}

function urlFull() {
	try {
	    return sprintf("%s://%s/", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['SERVER_NAME']);
	} catch(Exception $e) {
        stderr($e->getMessage());		
	}
}

?>