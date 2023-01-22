<?php
    if (isset($_GET['postId'])) { $thePostId = $_GET['postId']; } else { $thePostId = 0; }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
    if (!empty(getValue("google_analytics_property_id"))) {
        $propertyId = getValue("google_analytics_property_id");
		echo '
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=' . $propertyId . '"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag("js", new Date());
		  gtag("config", "' . $propertyId . '");
		</script>';
	}
	?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_description") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "description"); ?>">
	<?php
	if ($_SERVER['PHP_SELF'] == "/category.php" || checkUrl() == true) {
		echo "<meta name='robots' content='follow, noindex'/>\n";
	}
	?>
    <title><?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericMeta($_SERVER['PHP_SELF'], $thePostId, "title"); ?></title>
    <link href="<?= urlFull(); ?>favicon.ico" rel="icon">
	<link href="<?= urlFull(); ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= urlFull(); ?>css/style.css" rel="stylesheet">
    <link href="<?= urlFull(); ?>assets/datatables/datatables.min.css" rel="stylesheet" type="text/css">
    <link href="<?= urlFull(); ?>assets/fontawesome-free-6.2.0-web/css/all.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
	<script src="<?= urlFull(); ?>js/jquery-3.6.0.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
	<?php if (isset($_GET['postId'])) { echo '<link rel="canonical" href="'.xmlFriendlyUrls(getPostTitleOnly($thePostId), $thePostId).'/" />'; } ?>
	<?php if (!empty(getValue("google_adsense"))) { echo getValue("google_adsense") . "\n"; } ?>
  </head>
<body> 

<nav class="navbar navbar-expand-lg bg-dark border-bottom" id="mainNav">
	
	<div class="container-fluid">       
		
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
			<i class="fas fa-bars" style="color: white;"></i>
		</button>    

        <!-- mobile -->
		<div class="collapse navbar-collapse w-100 justify-content-center" id="collapsibleNavbar">
	  
			<a class="navbar-brand" href="<?= urlFull(); ?>"><img src="<?= urlFull(); ?><?= getHeaderImage(); ?>" alt="<?= urlFull(); ?>" class="d- d-block d-sm-inline-block mw-100 mx-auto"></a>	
			
			<ul class="nav nav-pills nav-fill ms-auto">   
				<li class="nav-item"><a href="<?= urlFull(); ?>" class="nav-link <?= ($_SERVER['PHP_SELF'] == "/index.php") ? "active" : ""; ?>">Home</a></li>
				
				<?php $pages = DB::getInstance()->select("SELECT * FROM `pages`"); ?>
				<?php foreach($pages as $page) { ?>
				
				<li class="nav-item"><a href="<?= urlFull(); ?>page.php?page=<?= $page['page_slug']; ?>" class="nav-link <?= ($_SERVER['REQUEST_URI'] == "/page.php?page={$page['page_slug']}") ? "active" : ""; ?>"><?= $page['page_name']; ?></a></li>	
				
				<?php } ?>
				
				<li class="nav-item"><a href="<?= urlFull(); ?>contact.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == "/contact.php") ? "active" : ""; ?>">Contact</a></li>	
			</ul>
			
			<ul class="nav navbar-nav ms-auto" style="padding-top: 10px;">
				<li>		
					<form class="d-flex" action="<?= urlFull(); ?>search.php" method="get">
						<input class="form-control me-2" type="search" name="s" placeholder="Search ..." aria-label="Search" required>
						<button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
					</form>		
				</li>           
			</ul> 
	
	    </div>
	
	</div>              
	
</nav>

<div class="col-lg-8 mx-auto p-3 py-md-5">