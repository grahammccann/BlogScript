<div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="card-header" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; text-transform: uppercase; text-align: center; background-color: #f5f5f5; padding: 1rem;">
        <small>
            <i class="fas fa-list-ol" style="color: green; font-size: 1.2em;"></i> Categories
        </small>
    </div>
    <ul class="list-group list-group-flush" style="font-family: 'Helvetica Neue', sans-serif; font-size: 16px; color: #333;">    
        <?php $categories = DB::getInstance()->select("SELECT * FROM `categories` ORDER BY `category_name` ASC"); ?>      
        <?php foreach($categories as $category) { ?>
            <li class="list-group-item" style="display: flex; align-items: center;">
                <?= getValue("category_style_icon"); ?> <a href="<?= urlFull(); ?>category.php?categoryId=<?= $category['category_id']; ?>" class="text-decoration-none" style="margin-left: 10px;"><?= $category['category_name']; ?></a>
            </li>        
        <?php } ?>        
    </ul>
</div>