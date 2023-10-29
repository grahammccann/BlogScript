<?php
    if (isset($_GET['postId'])) { $thePostId = $_GET['postId']; } else { $thePostId = 0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
if (!empty(getValue("google_analytics_property_id"))) {
    $propertyId = getValue("google_analytics_property_id");
    echo '<script async src="https://www.googletagmanager.com/gtag/js?id='.$propertyId.'"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag("js", new Date());
      gtag("config", "'.$propertyId.'");
    </script>
    ';
}
?>
<meta charset="utf-8">
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@<?= getValue("twitter_username"); ?>">
<meta name="twitter:title" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?>">
<meta name="twitter:description" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?>">
<meta name="twitter:image" content="<?= isset($_GET['postId']) ? urlFull() . "uploads/" . getTwitterImage($_GET['postId']) : urlFull() . getHeaderImage(); ?>">
<meta property="og:title" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= isset($_GET['postId']) ? rawUrls($thePostId, getPostTitleOnly($thePostId), false) : $_SERVER['REQUEST_URI']; ?>">
<meta property="og:image" content="<?= isset($_GET['postId']) ? urlFull() . "uploads/" . getTwitterImage($_GET['postId']) : urlFull() . getHeaderImage(); ?>">
<meta property="og:description" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?>">
<meta property="og:site_name" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?>">
<meta property="og:locale" content="en_US">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_description") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "description"); ?>">
<?php
if (($_SERVER['PHP_SELF'] == checkUrl()) || $_SERVER['PHP_SELF'] == "/login.php" || $_SERVER['PHP_SELF'] == "/recovery.php") {
    echo "<meta name='robots' content='follow, noindex'/>\n";
}
?>
<title><?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?></title>
<link href="<?= urlFull(); ?>favicon.ico" rel="icon">
<link href="<?= urlFull(); ?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?= urlFull(); ?>css/style.css" rel="stylesheet">
<?php 
if (isset($_GET['postId'])) { 
    echo '<link rel="canonical" href="'.rawUrls($thePostId, getPostTitleOnly($thePostId), false).'">'; 
} 
?>
<?php 
if (!empty(getValue("google_adsense"))) { 
    $googleClientId = getValue("google_adsense");
    echo "<script async src=\"https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={$googleClientId}\" crossorigin=\"anonymous\"></script>\n"; 
} 
?>
</head>
<body> 

<nav class="navbar navbar-expand-lg bg-dark border-bottom" id="mainNav">
  
  <div class="container-fluid">     
  
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-label="Toggle navigation">
        <i class="fas fa-bars" style="color: #fff"></i>
    </button>    
	
    <div class="collapse navbar-collapse w-100" id="collapsibleNavbar">
	
	
	    <?php list($imagePath, $imageWidth, $imageHeight) = getHeaderImage(); ?>
        <a class="navbar-brand" href="<?= urlFull(); ?>"><img src="<?= urlFull(); ?><?= $imagePath ?>" alt="<?= urlFull(); ?>" width="<?= $imageWidth; ?>" height="<?= $imageHeight; ?>" class="d-block d-sm-inline-block mw-100 mx-auto"></a>
	  
		<ul class="nav nav-pills nav-fill ms-auto flex-column flex-sm-row mb-3" style="display: flex; gap: 10px;">   
		<li class="nav-item"><a href="<?= urlFull(); ?>" class="nav-link <?= ($_SERVER['PHP_SELF'] == "/index.php") ? "active" : ""; ?>">Home</a></li>
		
		<li class="nav-item dropdown">
		  <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
			Categories
		  </a>
		  <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
			<?php $categories = DB::getInstance()->select("SELECT * FROM `categories` ORDER BY `category_name` ASC"); ?>      
			<?php foreach($categories as $category) { ?>
			  <li>
				<?= seoFriendlyUrls($category['category_id'], $category['category_name'], true, true); ?>
				<span style="color: #555; font-size: 1.2em;"><?= getValue("category_style_icon"); ?></span>
			  </li> 
			<?php } ?>
		  </ul>
		  
		</li>
		
		<?php $pages = DB::getInstance()->select("SELECT * FROM `pages` ORDER BY `page_name` ASC"); ?>
		<?php foreach($pages as $page) { ?>
		  <li class="nav-item"><a href="<?= urlFull(); ?>page.php?page=<?= $page['page_slug']; ?>" class="nav-link <?= ($_SERVER['REQUEST_URI'] == "/page.php?page={$page['page_slug']}") ? "active" : ""; ?>"><?= $page['page_name']; ?></a></li>    
		<?php } ?>
		  <li class="nav-item"><a href="<?= urlFull(); ?>contact.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == "/contact.php") ? "active" : ""; ?>">Contact</a></li>  
		
		</ul>

		<ul class="nav navbar-nav ms-auto">
			<li>    
				<form class="d-flex mb-3" action="<?= urlFull(); ?>search.php" method="get">
					<div class="input-group">
						<input type="search" name="s" class="form-control rounded-start" placeholder="Search site ..." aria-label="Search" aria-describedby="search-addon" required>
							<button class="btn btn-success rounded-end" type="submit" id="search-addon" aria-label="Search">
								<i class="fas fa-search"></i>
							</button>
					</div>
				</form>    
			</li>           
		</ul>
	  
    </div>
	
  </div>
  
</nav>

<div class="page-container col-lg-8 mx-auto p-3 py-md-5">
<div class="content-container">