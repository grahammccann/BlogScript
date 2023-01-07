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
							
                            $imageName = uploadImage(strtolower($_FILES['post_image']['name']), $_FILES['post_image']['tmp_name']);	
							DB::getInstance()->insert(
								'images',
							[
								'image_name' => $imageName,
								'image_alt_text' => $_POST['post_image_alt_text'],
								'image_is_header' => "no",
								'image_date' => date('Y-m-d H:i:s')
							]);
						
						} else {
							$imageName = "img-post-generic.png";
						}
						
						$i = DB::getInstance()->insert(
							'posts',
						[
						    'post_category_id' => $_POST['post_category'],
						    'post_member_id' => getLoggedInUserId($_SESSION['member']),
							'post_title' => htmlspecialchars(strip_tags($_POST['post_title'])),
							'post_body' => $_POST['post_body'],
							'post_seo_title' => $_POST['post_seo_title'],
							'post_seo_description' => $_POST['post_seo_description'],
							'post_image' => $imageName,
							'post_image_alt_text' => (empty($_POST['post_image_alt_text'])) ? "generic alt text" : $_POST['post_image_alt_text'],
							'post_status' => $_POST['post_status'],
							'post_source_url' => urlFull(),
							'post_date' => date('Y-m-d H:i:s')
						]);
						

						if (file_exists("sitemap.xml")) {
							createSitemap();
							stdmsg('Sitemap <strong>updated</strong>.');
						} else {
							createSitemap();
							stdmsg('Sitemap <strong>created</strong>.');
						}
						
						stdmsg("Your new <strong>post</strong> has been <strong>added</strong>.");
					
					}
					 
				 }
				 
				 ?>
				
				<form action="new-post.php" method="post" enctype="multipart/form-data">

					<div class="mb-3">
						<label for="post_title" class="form-label"><strong>Title:</strong></label>
						<input type="text" class="form-control" id="post_title" name="post_title" required>
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
						<textarea class="form-control" id="summernote" name="post_body" rows="15" required></textarea>
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
							  $categories = DB::getInstance()->select("SELECT * FROM `categories` ORDER BY `category_name` ASC");
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
					    <label for="post_image" class="form-label"><strong>Featured Image:</strong></label>
					    <input class="form-control" type="file" id="post_image" name="post_image">
					</div>

					<div class="mb-3">
					    <label for="post_image_alt_text" class="form-label"><strong><span class="text-success">Featured Image ALT Text:</span></strong></label>
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