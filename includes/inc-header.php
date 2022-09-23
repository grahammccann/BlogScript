<?php
    if (isset($_GET['postId'])) { $thePostId = $_GET['postId']; } else { $thePostId = 0; }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_description") : getGenericDescription($_SERVER['PHP_SELF'], $thePostId); ?>">
    <title><?= $_SERVER['PHP_SELF'] == "/index.php" ? getValue("homepage_title") : getGenericTitle($_SERVER['PHP_SELF'], $thePostId); ?></title>
    <link href="<?= urlFull(); ?>favicon.ico" rel="icon">
	<link href="<?= urlFull(); ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= urlFull(); ?>css/style.css" rel="stylesheet">
    <link href="<?= urlFull(); ?>assets/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= urlFull(); ?>assets/fontawesome/css/all.min.css" rel="stylesheet">
	<script src="<?= urlFull(); ?>js/jquery-3.6.0.js"></script> 
  </head>
<body> 

<nav class="navbar navbar-expand-lg border-bottom navbar-white bg-white padding-bottom" id="mainNav">
	
	<div class="container-fluid">       
		
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
			<i class="fas fa-bars"></i>
		</button>    

        <!-- mobile -->
		<div class="collapse navbar-collapse w-100 justify-content-center" id="collapsibleNavbar">
	  
			<a class="navbar-brand" href="<?= urlFull(); ?>"><img src="<?= urlFull(); ?><?= getHeaderImage(); ?>" alt="<?= urlFull(); ?>"></a>	
			
			<ul class="nav nav-pills nav-fill ms-auto">   
				<li class="nav-item"><a href="<?= urlFull(); ?>index.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == "/index.php") ? "active" : ""; ?>">Home</a></li>
				
				<?php $pages = DB::getInstance()->select("SELECT * FROM `pages`"); ?>
				<?php foreach($pages as $page) { ?>
				
				<li class="nav-item"><a href="<?= urlFull(); ?>page.php?page=<?= $page['page_slug']; ?>" class="nav-link <?= ($_SERVER['REQUEST_URI'] == "/page.php?page={$page['page_slug']}") ? "active" : ""; ?>"><?= $page['page_name']; ?></a></li>	
				
				<?php } ?>
				
				<li class="nav-item"><a href="<?= urlFull(); ?>contact.php" class="nav-link <?= ($_SERVER['PHP_SELF'] == "/contact.php") ? "active" : ""; ?>">Contact</a></li>	
			</ul>
			
			<ul class="nav navbar-nav ms-auto" style="padding-top: 10px;">
				<li>		
					<form class="d-flex" action="<?= urlFull(); ?>search.php" method="get">
						<input class="form-control me-2" type="search" name="s" placeholder="enter a search term ..." aria-label="Search" required>
						<button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
					</form>		
				</li>           
			</ul> 
	
	    </div>
	
	</div>              
	
</nav>

<div class="col-lg-8 mx-auto p-3 py-md-5">