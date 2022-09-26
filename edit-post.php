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
			  <div class="card-header"><i class="fas fa-upload"></i> Update Post</div>
                <div class="card-body">
				
				 <?php
				 
				 $errors = [];
				 
				 if (isset($_POST['submitEditPost'])) {
					
                    if ($_POST['post_sticky']) {

						$sticky = DB::getInstance()->select("SELECT * FROM `posts` WHERE `post_sticky`='1'");				
						
						if (count($sticky) > 0) {
							$errors[] = 'A <strong>sticky</strong> post is already in use un-sticky that one first.';
						}					
				
					}					
					
					if (!empty($errors) > 0) {
						
						foreach($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> '.$error.'</div>';
						}
						
					} else {
						
						$postBody = "";
						if (strpos($postBody, 'IMID') !== false) {
							$postBody = strip_tags(nl2br($_POST['post_body']), '<p><a><div><span><img><h1><h2>');						
						} else {	
							// IMPORTANT: check for any images in the post body, if found insert.
							$postBody = checkForAndReplaceAnyImages(strip_tags(nl2br($_POST['post_body']), '<p><a><div><span><img><h1><h2><h3><strong>'));						
						}
						
						$u = DB::getInstance()->update(
							'posts',
							'post_id',
							$_POST['updateId'],
						[
						    'post_category_id' => $_POST['post_category'],
						    'post_member_id' => getLoggedInUserId($_SESSION['member']),
							'post_title' => htmlspecialchars(strip_tags($_POST['post_title'])),
							'post_body' => $postBody,
							'post_seo_title' => $_POST['post_seo_title'],
							'post_seo_description' => $_POST['post_seo_description'],
							'post_status' => $_POST['post_status'],
							'post_date_updated' => date('Y-m-d H:i:s'),
							'post_sticky' => $_POST['post_sticky']						
						]);
						
						stdmsg("Your <strong>post</strong> has been <strong>updated</strong>.");
					
					}
					 
				 }
				 
				 ?>
				 
				<?php
				
				    $post = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$_GET['postId']}'");
				
				?>
				
				<form action="edit-post.php?postId=<?= $_GET['postId']; ?>" method="post">
				
				  <div class="mb-3">
					<label for="post_quick_tags" class="form-label"><strong>Quick Tags:</strong></label>
					<select id="post_quick_tags" name="post_quick_tags" class="form-select">
					  <option value='-- SELECT --'>-- SELECT --</option>
					  <?php 
						  $quickTags = array("<a href=\"URL\">ANCHOR</a>", 
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
					<input type="text" class="form-control" id="post_title" name="post_title" value="<?= $post['post_title']; ?>" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_body" class="form-label"><strong>Body:</strong></label>
					<textarea class="form-control" id="post_body" name="post_body" rows="15" required><?= $post['post_body']; ?></textarea>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_seo_title" class="form-label"><strong><span class="text-success">SEO META Title:</span></strong></label>
					<input type="text" class="form-control" id="post_seo_title" name="post_seo_title" value="<?= $post['post_seo_title']; ?>" required>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_seo_description" class="form-label"><strong><span class="text-success">SEO META Description:</span></strong></label>
					<textarea class="form-control" id="post_seo_description" name="post_seo_description" rows="3" required><?= $post['post_seo_description']; ?></textarea>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_category" class="form-label"><strong>Category:</strong></label>
					<select id="post_category" name="post_category" class="form-select" required>
					    <?php 					  
					        $categories = DB::getInstance()->select("SELECT * FROM `categories`");
					        foreach($categories as $category) {
					            echo "<option value='{$category['category_id']}'";
						        if ($category['category_id'] == $post['post_category_id']) { 
						            echo " selected"; 
						        } 
						        echo ">{$category['category_name']}</option>\n";
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
					            echo "<option value='{$key}'";
						        if ($key == $post['post_status']) { 
						            echo " selected"; 
						        } 						  
						        echo ">{$value}</option>\n";
                            } 
					    ?>
					</select>
				  </div>
				  
				  <div class="mb-3">
					<label for="post_sticky" class="form-label"><strong>Sticky:</strong></label>
					<select id="post_sticky" name="post_sticky" class="form-select" required>
					  <?php 
						  $sticky = array("0" => "No", "1" => "Yes");
						  foreach($sticky  as $key => $value) {
							  echo "<option value='{$key}' ";
						      if ($key == $post['post_sticky']) { 
						          echo " selected"; 
						      } 							  										  
							  echo ">{$value}</option>";
						  } 
					  ?>
					</select>
				  </div>
				  
				  <input type="hidden" name="updateId" value="<?= $_GET['postId']; ?>">	
				  <button type="submit" name="submitEditPost" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>
				
				</form>               
				
                </div>
				<div class="card-footer">&nbsp;</div>
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>