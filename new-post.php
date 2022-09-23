<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sessions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

	<?php $user = getUsersDetails($member); ?>
	
	<?php
	
	if ($user['member_is_admin'] != "yes") {
		stderr("<strong>Protected</strong> page.");
        include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
        die();		
	}
	
	?>

	<div class="row">
		<div class="col-md-3">
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard.php"); ?>
			</div>
		</div>
		
		<div class="col-md-9">
			<div class="card">
			  <div class="card-header"><i class="fas fa-plus"></i> New Post</div>
                <div class="card-body">
				
				 <?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitPost'])) {
					 
					$dupe = DB::getInstance()->selectOneByField('posts', 'post_title', $_POST['post_title']);
					
					if (!empty($dupe)) {
			            $errors[] = 'That <strong>post</strong> is already in use please choose another.';
		            }
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
						}
						
					} else {
						
						if (!empty($_FILES['post_image']['name'])) {
							
                            $imageName = uploadImage('post_image', $_FILES['post_image']['name']);	
							DB::getInstance()->insert(
								'images',
							[
								'image_name' => $imageName,
								'image_alt_text' => $_POST['post_image_alt_text'],
								'image_is_header' => "no",
								'image_date' => date('Y-m-d H:i:s')
							]);
						
						} else {
							$imageName = "image.jpg";
						}
						
						// IMPORTANT: check for any images in the post body, if found insert.
						$postBody = checkForAndReplaceAnyImages(htmlspecialchars(strip_tags(nl2br($_POST['post_body']))));
						
						DB::getInstance()->insert(
							'posts',
						[
						    'post_category_id' => $_POST['post_category'],
						    'post_member_id' => getLoggedInUserId($_SESSION['member']),
							'post_title' => htmlspecialchars(strip_tags($_POST['post_title'])),
							'post_body' => $postBody,
							'post_seo_title' => $_POST['post_seo_title'],
							'post_seo_description' => $_POST['post_seo_description'],
							'post_image' => $imageName,
							'post_status' => $_POST['post_status'],
							'post_date' => date('Y-m-d H:i:s')
						]);
						
						stdmsg("Your new <strong>post</strong> has been <strong>added</strong>.");
					
					}
					 
				 }
				 
				 ?>
				
				 <form action="new-post.php" method="post" enctype="multipart/form-data">
			  
				  <div class="mb-3">
					<label for="post_quick_tags" class="form-label"><strong>Quick Tags:</strong></label>
					<select id="post_quick_tags" name="post_quick_tags" class="form-select">
					  <option value='-- SELECT --'>-- SELECT --</option>
					  <?php 
						  $quickTags = array("<a href=\"URL\" class=\"text-decoration-none\">ANCHOR</a>", 
						                     "<strong>TEXT</strong>",
											 "<h1>HEADER1</h1>");
						  foreach($quickTags as $value) {
							  echo "<option value='" . htmlspecialchars($value) . "'>" . htmlspecialchars($value) . "</option>";
						  } 
					  ?>
					</select>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_image_tags" class="form-label"><strong>Quick Image:</strong></label>
					<select id="post_image_tags" name="post_image_tags" class="form-select">
					  <option value='-- SELECT --'>-- SELECT --</option>
					  <?php 
                          $images = DB::getInstance()->select("SELECT * FROM `images` WHERE `image_is_header`='no'");               
						  foreach($images as $image) {
							  echo "<option value='IMID{$image['image_id']}'>IMID{$image['image_id']} - {$image['image_alt_text']}</option>";
						  } 
					  ?>
					</select>
				  </div>
				 
				  <div class="mb-3">
					<label for="post_title" class="form-label"><strong>Title:</strong></label>
					<input type="text" class="form-control" id="post_title" name="post_title" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_body" class="form-label"><strong>Body:</strong></label>
					<textarea class="form-control" id="post_body" name="post_body" rows="15" required></textarea>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_seo_title" class="form-label"><strong><span class="text-success">SEO META Title:</span></strong></label>
					<input type="text" class="form-control" id="post_seo_title" name="post_seo_title" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_seo_description" class="form-label"><strong><span class="text-success">SEO META Description:</span></strong></label>
					<textarea class="form-control" id="post_seo_description" name="post_seo_description" rows="3" required></textarea>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_category" class="form-label"><strong>Category:</strong></label>
					<select id="post_category" name="post_category" class="form-select" required>
					  <?php 
						  $categories = DB::getInstance()->select("SELECT * FROM `categories`");
						  foreach($categories as $category) {
							  echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
						  } 
					  ?>
					</select>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_status" class="form-label"><strong>Status:</strong></label>
					<select id="post_status" name="post_status" class="form-select" required>
					  <?php 
						  $status = array("published" => "Published", "draft" => "Draft", "archived" => "Archived");
						  foreach($status as $key => $value) {
							  echo "<option value='{$key}'>{$value}</option>";
						  } 
					  ?>
					</select>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_image" class="form-label"><strong>Image:</strong></label>
					<input class="form-control" type="file" id="post_image" name="post_image">
				  </div>
				  
				  <div class="mb-3">
					<label for="post_image_alt_text" class="form-label"><strong><span class="text-success">Image ALT Text:</span></strong></label>
					<input type="text" class="form-control" id="post_image_alt_text" name="post_image_alt_text">
				  </div>
				  
				  <button type="submit" name="submitPost" class="btn btn-success float-end"><i class="fas fa-plus"></i> New Post</button>
				
				</form>               
				
                </div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>