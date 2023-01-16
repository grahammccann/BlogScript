<?php
error_reporting(0);
$htmlDebug = true;

try {
	
    # json decode ...
    $json = json_decode($campaign['campaign_json'], true);	
   
    # random keyword ...  
    $randomKeyword = getRandomKeyword($json['module_amazon_scraper']);
 
    # no top 10 table ... 
    if ($json['amazon_top_10_table'] == "no") {
	   
		# create single page ...
		
    } 
   
    # create top 10 table ...
    if ($json['amazon_top_10_table'] == "yes") {
		
		# vars ...
		$counter = 1;
		$log = "";					   
	   
		# begin sending the query to amazon and getting the html ...
		$url = "https://www.amazon.co.uk/s?k=" . urlencode($randomKeyword) . "&ref=nb_sb_noss";						
		
		# fix: for amazon scraping ...
		$opts = array(
		  'http'=>array(
			'method'=> "GET",
			'header'=> "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Encoding: gzip\r\n"
		  )
		);
		$cont = stream_context_create($opts);			
		//$html = gzdecode(file_get_contents($url, false, $cont));
		$html = file_get_contents($url, false, $cont);

		# ...
		if ($html == false) {
			return;
		}
		
		# parse ...
		$amazon_scraper = parseHtmlAmazonScraper($html);

		# break here if the results are empty! debug why later ...
		if (empty($amazon_scraper)) {
			$log = "<span class='text-danger'><b>error</b></span> - empty results returned ...";						
			writeToLog("module_amazon_scraper", strtolower($log), $json['amazon_campaign_name']);
			return;
		}

		# generic post title and count ...
		$postTitle = "Top 10 {$randomKeyword} For " . date('Y');	
		
		# dupe check ...	
		$posts = DB::getInstance()->select("SELECT * FROM `blog_posts` WHERE `post_title`='{$postTitle}'"); 
		if (count($posts) > 0) {
			$log = "<span class='text-danger'><b>error</b></span> - an entry for: <b>{$postTitle}</b> already exists ...";						
			writeToLog("module_amazon_scraper", strtolower($log), $json['amazon_campaign_name']);
			return;
		} 

		# prepare html table ...
		$body = "<h2>The Best {$randomKeyword} Of " . date('Y') . " - <b>Ranked Best to Last</b></h2>";
		$body .= '<p>Click on the product image <b>or</b> the product description to view that product in more detail and get the best possible price. The top reviewed products are at the top of the table.</p><br />';
		$body .= '							
						<table class="table table-bordered table-responsive-lg">
							<thead>
								<tr>
									<th>SCORE</th>
									<th>IMAGE</th>
									<th>DESCRIPTION</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>';													
	    
		# loop the products ...
		foreach ($amazon_scraper as $node) {
				
			$cleanUrl = sortCleanUrls($node['productLink'], $json['amazon_id'], $json['amazon_location']);	
			$rank  = $counter++;
			$body .= '	
						<tr>
						  <td><img src="'.getRankImage($rank).'" alt="Rank No '.$rank.'" /></td>
						  <td><a href="'.$cleanUrl.'"><img src="'.downloadImagesToServer($node['productImage']).'" alt="We rank this product No '.$rank.'" /></a></td>
						  <td><a href="'.$cleanUrl.'">'.$node['productDescription'].'</a></td>
						  <td><a href="'.$cleanUrl.'"><img src="'.urlFull().'content/images/img-buy-now.png"></a></td>
						</tr>';
				
			# break the loop once we reach 10 ...		
			if ($counter == 11) {
				break;
			}
				
		}
		
		$body .= '	
							</tbody>
						</table>';

		$body .= "<p>We get a lot of emails per week asking us to list the top products from around the web (believe it or not we buy each top product before we list it to our readers!) questions like:</p>";									
		$body .= "- What is the best {$postTitle} to buy on the market at the lowest price!<br />";		
		$body .= "- What is the best <b>{$postTitle}</b> in " . date('Y') . "<br />";	
		$body .= "- What is the best <i>{$postTitle}</i> to buy?<br />";	
		$body .= "<p>The way we structure the results is the number <b>1</b> rank is the best product in that category, then we go down the list to the end, we try to show <b>10</b> results per product but if we feel a product doesn't deserve or even have 10 products we only show what we think is the best.</p>";						

		# debug on / off ...
		if ($htmlDebug) {
			echo "<hr style='border-top: 1px solid #ccc;'>";	
			echo "Debugging for module: <font color='green'>module_amazon_scraper</font>";
			echo "<hr style='border-top: 1px solid #ccc;'>";	
			echo $body;	
			echo "<hr style='border-top: 1px solid #ccc;'>";		
		}
		
		# clean up ...		
 		if (autoPost($json['amazon_category_id'], $json['amazon_member_id'], $postTitle, $body, getModuleId('module_amazon_scraper'), $json['amazon_campaign_name'])) {																	
			$log = "<span class='text-success'><b>success</b></span> - posted: <b>{$postTitle}</b>";
			# send admin an email ...
		    if (getValue('settings_send_admin_emails') == 'yes') {
			    sendAdminEmail($json['amazon_member_id'], "{$postTitle} was just posted!", "contact@".get_just_domain($_SERVER['SERVER_NAME'])."");
			}
		} 
			
		# write to the log outside the loop ...							
		writeToLog("module_amazon_scraper", strtolower($log), $json['amazon_campaign_name']);
							   
	} 

} catch (Exception $e) {
	stderr("Debugging for module: <font color='green'>module_amazon_scraper</font><br /><br />" . $e->getMessage());
}		

?>