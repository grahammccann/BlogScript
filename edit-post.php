<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sessions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

	<?php $user = getUsersDetails($member); ?>
	
	<?php
	
	if (!$user || $user['member_is_admin'] != "yes") {
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
			
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard-analytics.php"); ?>
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
							if ($_POST['updateId'] != $sticky[0]['post_id']) {
								$errors[] = 'A <strong>sticky</strong> post is already in use. Un-sticky that one first.';
							}
						}

					}

					if (count($errors) > 0) {
						
						foreach ($errors as $error) {
							echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> ' . $error . '</div>';
						}
						
					} else {

						if (!empty($_FILES['post_image']['name'])) {

							$imageName = uploadImage(strtolower($_FILES['post_image']['name']), $_FILES['post_image']['tmp_name'], strtolower($_POST['post_image_alt_text']), true);

							if (!$imageName) {
								echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> Failed to upload the image.</div>';
								return;
							}

							$i = DB::getInstance()->insert(
								'images',
								[
									'image_name' => $imageName,
									'image_alt_text' => $_POST['post_image_alt_text'],
									'image_is_header' => "no",
									'image_date' => date('Y-m-d H:i:s')
								]
							);

							if (!$i) {
								echo '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong> Failed to insert image into database.</div>';
								return;
							}

							$u = DB::getInstance()->update(
								'posts',
								'post_id',
								$_POST['updateId'],
								[
									'post_image' => $imageName,
									'post_image_alt_text' => $_POST['post_image_alt_text']
								]
							);
						}

						// Removed redundant comments

						$u = DB::getInstance()->update(
							'posts',
							'post_id',
							$_POST['updateId'],
							[
								'post_category_id' => $_POST['post_category'],
								'post_member_id' => getLoggedInUserId($_SESSION['member']),
								'post_title' => htmlspecialchars(strip_tags($_POST['post_title'])),
								'post_body' => $_POST['post_body'],
								'post_seo_title' => $_POST['post_seo_title'],
								'post_seo_description' => $_POST['post_seo_description'],
								'post_image_alt_text' => strtolower($_POST['post_image_alt_text']),
								'post_status' => $_POST['post_status'],
								'post_date_updated' => date('Y-m-d H:i:s'),
								'post_sticky' => $_POST['post_sticky'],
								'post_affiliate_url' => !empty($_POST['post_affiliate_url']) ? $_POST['post_affiliate_url'] : "...",
								'post_will_show_ads' => $_POST['post_will_show_ads']
							]
						);

						if (file_exists("sitemap.xml")) {
							createSitemap();
							stdmsg('Sitemap <strong>updated</strong>.');
						} else {
							createSitemap();
							stdmsg('Sitemap <strong>created</strong>.');
						}

						stdmsg("Your <strong>post</strong> has been <strong>updated</strong>.");

					}

				}

				?>
				 
				<?php
				
				    $post = DB::getInstance()->selectValues("SELECT * FROM `posts` WHERE `post_id`='{$_GET['postId']}'");
				
				?>
				
				<form action="edit-post.php?postId=<?= $_GET['postId']; ?>" method="post" enctype="multipart/form-data">

					<div class="mb-3">
						<label for="post_title" class="form-label"><strong>Title:</strong></label>
						<input type="text" class="form-control" id="post_title" name="post_title" value="<?= $post['post_title']; ?>" required>
					</div>				
					
					<script>
					$(document).ready(function() {
						$('#summernote').summernote({
						  height: 300,       // set editor height
						  minHeight: null,   // set minimum height of editor
						  maxHeight: null,   // set maximum height of editor
						  focus: true        // set focus to editable area after initializing summernote
						});
					});
					</script>
					
					<div class="mb-3">
						<label for="post_body" class="form-label"><strong>Body:</strong></label>
						<textarea class="form-control" id="summernote" name="post_body" rows="15" required>
						<?= displayArticle($post['post_body']); ?>
						</textarea>
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
					    <label for="post_image_alt_text" class="form-label"><strong><span class="text-success">SEO Featured Image ALT Text:</span></strong></label>
					    <input type="text" class="form-control" id="post_image_alt_text" name="post_image_alt_text" value="<?= $post['post_image_alt_text']; ?>" required>
					</div>

					<div class="mb-3">
						<label for="post_category" class="form-label"><strong>Category:</strong></label>
						<select id="post_category" name="post_category" class="form-select" required>
							<?php 					  
								$categories = DB::getInstance()->select("SELECT * FROM `categories` ORDER BY `category_name` ASC");
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
							  foreach($sticky as $key => $value) {
								  echo "<option value='{$key}' ";
								  if ($key == $post['post_sticky']) { 
									  echo " selected"; 
								  } 							  										  
								  echo ">{$value}</option>";
							  } 
						  ?>
						</select>
					</div>
				  

					<div class="mb-3">
					    <label for="post_image" class="form-label"><strong>Featured Image:</strong></label>
					    <input class="form-control" type="file" id="post_image" name="post_image">
					</div>
					
					<div class="mb-3">
						<label for="post_will_show_ads" class="form-label"><strong>Show Ads:</strong></label>
						<select id="post_will_show_ads" name="post_will_show_ads" class="form-select" required>
						  <?php 
							  $showAds = array("no" => "No", "yes" => "Yes");
							  foreach($showAds as $key => $value) {
								  echo "<option value='{$key}' ";
								  if ($key == $post['post_will_show_ads']) { 
									  echo " selected"; 
								  } 							  										  
								  echo ">{$value}</option>";
							  } 
						  ?>
						</select>
					</div>
					
					<div class="mb-3">
						<label for="post_affiliate_url" class="form-label"><strong>Affiliate URL:</strong></label>
						<input type="text" class="form-control" id="post_affiliate_url" name="post_affiliate_url" value="<?= $post['post_affiliate_url']; ?>">
					</div>
				  
					<input type="hidden" name="updateId" value="<?= $_GET['postId']; ?>">	
					<button type="submit" name="submitEditPost" class="btn btn-success float-end"><i class="fas fa-upload"></i> Update</button>
				
				</form>               
				
                </div>
				
			</div>	
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>