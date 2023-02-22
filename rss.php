<?php

	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
	include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");

	function createRSSFeed($link, $language) {
		try {
			$rss = DB::getInstance()->select("SELECT * FROM `posts` ORDER BY `post_date` DESC");

			$rssFeed = '<?xml version="1.0" encoding="UTF-8" ?>';
			$rssFeed .= "\n<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
			$rssFeed .= "  <channel>\n";
			$rssFeed .= "    <title>" . getValue("homepage_title") . "</title>\n";
			$rssFeed .= "    <description>" . getValue("homepage_description") . "</description>\n";
			$rssFeed .= "    <link>" . $link . "</link>\n";
			$rssFeed .= "    <language>" . $language . "</language>\n";
			$rssFeed .= "    <atom:link href=\"" . $link . "\" rel=\"self\" type=\"application/rss+xml\" />\n";

			foreach ($rss as $row) {
				$rssFeed .= "    <item>\n";
				$rssFeed .= "      <title>" . $row['post_title'] . "</title>\n";
				$rssFeed .= "      <description>" . $row['post_seo_description'] . "</description>\n";
				$rssFeed .= "      <link>" . rssUrls($row['post_title'], $row['post_id']) . "</link>\n";
				$rssFeed .= "      <guid isPermaLink=\"false\">" . md5($row['post_id']) . "</guid>\n";
				$rssFeed .= "      <pubDate>" . date('r', strtotime($row['post_date'])) . "</pubDate>\n";
				$rssFeed .= "    </item>\n";
			}

			$rssFeed .= "  </channel>\n";
			$rssFeed .= "</rss>\n";

			return $rssFeed;
		} catch (Exception $e) {
			stderr($e->getMessage());
		}
	}

	$rssFeed = createRSSFeed(urlFull() . "rss.php", 'en-us');

	header('Content-Type: application/rss+xml; charset=utf-8');
	echo $rssFeed;

?>
