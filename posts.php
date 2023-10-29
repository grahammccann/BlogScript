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
	
	<?php

	if (isset($_GET['index']) && isset($_GET['url']) && isset($_GET['keywords'])) {
		echo stdmsg(indexNow("", $_GET['url'], $_GET['keywords']));
	}

	?>

	<div class="row">
	
		<div class="col-md-3">
		
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard.php"); ?>
			</div>
			
			<div class="card">
			<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard-extended.php"); ?>
			</div>
			
		</div>
		
		<div class="col-md-9">
			<div class="card">
			  <div class="card-header"><i class="fas fa-pencil-alt"></i> Posts <span class="badge bg-success float-end"><a href="new-post.php" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create a new post">New Post</a></span></div>
                <div class="card-body">
				
				<?php

				if (isset($_GET['delete'])) {
					try {
						// Fetch post details
						$post = DB::getInstance()->selectOneByField('posts', 'post_id', $_GET['postId']);

						if (!empty($post)) {
							// Delete post image
							$postImagePath = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $post['post_image'];
							if (file_exists($postImagePath)) {
								unlink($postImagePath);
							}

							// Delete the image record from the 'images' table that has the same name as 'post_image'
							DB::getInstance()->remove('images', 'image_name', $post['post_image']);

							// Delete post from the database
							$delete = DB::getInstance()->remove('posts', 'post_id', $_GET['postId']);
							if ($delete) {
								stdmsg("The <strong>post</strong> and its image have been <strong>deleted</strong>.");
							}
						}
					} catch (Exception $e) {
						stderr($e->getMessage());					
					}
				}

				?>
                
				<?php $posts = DB::getInstance()->select("SELECT * FROM `posts`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="tablePosts" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>ID</th>
					  <th>Title</th>
					  <th>Sticky</th>
					  <th>Added</th>
					  <th><i class="fas fa-eye"></i></th>
					  <th><i class="fa fa-arrow-up-right-from-square"></i></th>
					  <th><i class="fa-solid fa-dollar-sign"></i></th>
					  <th><i class="fa-solid fa-list-ol"></i></th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </thead>
				  <tfoot>
					<tr>
					  <th>ID</th>
					  <th>Title</th>
					  <th>Sticky</th>
					  <th>Added</th>
					  <th><i class="fas fa-eye"></i></th>
					  <th><i class="fa fa-arrow-up-right-from-square"></i></th>
					  <th><i class="fa-solid fa-dollar-sign"></i></th>
					  <th><i class="fa-solid fa-list-ol"></i></th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	  
						<?php foreach ($posts as $post) { ?>
						
							<tr>
						    	<td><?= $post['post_id']; ?></td>
								<td><?= seoFriendlyUrls($post['post_id'], $post['post_title'], false, false); ?><br><small><?= getPublishedStatus($post['post_status']); ?> | <?= seoFriendlyUrls($post['post_category_id'], getCategoryname($post['post_category_id']), true, false); ?> | <a href="posts.php?index=1&amp;url=&amp;keywords=" class="text-decoration-none">Index</a></small></td>
								<td><?= ($post['post_sticky'] == 1) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
								<td><?= date("m.d.y", strtotime($post['post_date'])); ?></td>
								<td><?= $post['post_views']; ?></td>
								<td><?= (doesPostContainAnInternalLink($post['post_body']) == true) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
								<td><?= (doesPostContainMonetizationLinks($post['post_body']) == true) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
								<td><?= getPostWordCount($post['post_id'], $post['post_body']); ?></td>
								<td class="text-center"><a href="edit-post.php?postId=<?= $post['post_id']; ?>" class="btn btn-warning btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fas fa-edit"></i></a></td>
								<td class="text-center"><a href="posts.php?delete=1&amp;postId=<?= $post['post_id']; ?>" onClick="return confirm('Delete the post?')" class="btn btn-danger btn-sm" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="far fa-trash-alt"></i></a></td>
							</tr>	
							
						<?php } ?>							
				  </tbody>	  
				</table>
				</div>    								
                </div>
				
			</div>	
		</div>
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>