	<div class="card mb-3">
	  <div class="card-header"><i class="fas fa-list-ol" style="color:green"></i> Categories</div>
	  <ul class="list-group list-group-flush">
	  
		<?php $categories = DB::getInstance()->select("SELECT * FROM `categories` ORDER BY `category_name` ASC"); ?>
		
		<?php foreach($categories as $category) { ?>
			<li class="list-group-item"><i class="fa-sharp fa-solid fa-caret-right"></i> <a href="<?= urlFull(); ?>category.php?categoryId=<?= $category['category_id']; ?>" class="text-decoration-none"><?= $category['category_name']; ?></a></li>				
		<?php } ?>	
		
	  </ul>
	  <div class="card-footer">&nbsp;</div>
	</div>	