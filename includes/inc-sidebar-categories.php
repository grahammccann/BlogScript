	<div class="card">
	  <div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 24px; font-weight: bold; text-transform: capitalize; text-align: center; background-color: #f5f5f5; padding: 1rem;">
		<i class="fas fa-list-ol" style="color: green; font-size: 1.2em;"></i> Categories
	  </div>
	  <ul class="list-group list-group-flush">    
		<?php $categories = DB::getInstance()->select("SELECT * FROM `categories` ORDER BY `category_name` ASC"); ?>      
		<?php foreach($categories as $category) { ?>
		  <li class="list-group-item"><?= getValue("category_style_icon"); ?> <a href="<?= urlFull(); ?>category.php?categoryId=<?= $category['category_id']; ?>" class="text-decoration-none"><?= $category['category_name']; ?></a></li>        
		<?php } ?>        
	  </ul>
	</div>