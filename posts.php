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
			  <div class="card-header"><i class="fas fa-pencil-alt"></i> Posts <span class="badge bg-success float-end"><a href="new-post.php" class="badge badge-primary badge-sm text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create a new post">New Post</a></span></div>
                <div class="card-body">
				
				<?php
				
				if (isset($_GET['delete'])) {

					try {
						
						$images = deleteAnyImages($_GET['postId']);
						$delete = DB::getInstance()->remove('posts', 'post_id', $_GET['postId']);
						if ($delete == null) {
							stdmsg("The <strong>post</strong> has been <strong>deleted</strong>.");
						}
						
					} catch (Exception $e) {
						stdErr($e->getMessage());
					}	
					
				}
				
				?>
                
				<?php $posts = DB::getInstance()->select("SELECT * FROM `posts`"); ?>
				
				<!-- DataTables -->
				<div class="table-responsive">
				<table class="table table-striped" id="postTable" width="100%" cellspacing="0">	  
				  <thead>
					<tr>
					  <th>ID</th>
					  <th>Title</th>
					  <th>Sticky</th>
					  <th>Added</th>
					  <th><i class="fas fa-eye"></i></th>
					  <th>IL</th>
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
					  <th>IL</th>
					  <th>&nbsp;</th>
					  <th>&nbsp;</th>
					</tr>
				  </tfoot>
				  <tbody>	  
						<?php foreach ($posts as $post) { ?>
						
							<tr>
						    	<td><?= $post['post_id']; ?></td>
								<td><?= seoFriendlyUrls($post['post_title'], $post['post_id']); ?><br><small><?= getPublishedStatus($post['post_status']); ?> | <a href="category.php?categoryId=<?= $post['post_category_id']; ?>" class="text-decoration-none"><?= getCategoryname($post['post_category_id']); ?></a> | <a href="edit-category.php?categoryId=<?= $post['post_category_id']; ?>" class="text-decoration-none">Edit</a></small></td>
								<td><?= ($post['post_sticky'] == 1) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
								<td><?= date("m.d.y", strtotime($post['post_date'])); ?></td>
								<td><?= $post['post_views']; ?></td>
								<td><?= (doesPostContainAnInternalLink($post['post_body']) == true) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
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