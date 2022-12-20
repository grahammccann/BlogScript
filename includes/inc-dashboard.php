<div class="card-header bg-primary text-white"><i class="fas fa-cog"></i> <a href="dashboard.php" class="text-decoration-none text-white">Dashboard</a> <span class="badge bg-success float-end"><a href="logout.php" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Logout" class="badge badge-primary badge-sm text-decoration-none"><i class="fas fa-sign-out-alt"></i></a></span></div>
	<ul class="list-group list-group-flush">		  
		<li class="list-group-item"><i class="fas fa-list-ol"></i> <a href="categories.php" class="text-decoration-none">Categories</a> <span class="badge rounded-pill bg-primary float-end"><?= doTableCount("categories"); ?></span></li>
		<li class="list-group-item"><i class="fas fa-image"></i> <a href="images.php" class="text-decoration-none">Images</a> <span class="badge rounded-pill bg-primary float-end"><?= doTableCount("images"); ?></span></li>			
		<li class="list-group-item"><i class="fas fa-file-word"></i> <a href="pages.php" class="text-decoration-none">&nbsp;Pages</a> <span class="badge rounded-pill bg-primary float-end"><?= doTableCount("pages"); ?></span></li>			
		<li class="list-group-item"><i class="fas fa-pencil-alt"></i> <a href="posts.php" class="text-decoration-none">Posts</a> <span class="badge rounded-pill bg-primary float-end"><?= doTableCount("posts"); ?></span></li>			
		<li class="list-group-item"><i class="fas fa-users"></i> <a href="users.php" class="text-decoration-none">Users</a> <span class="badge rounded-pill bg-primary float-end"><?= doTableCount("members"); ?></span></li>				
	    <li class="list-group-item list-group-item-light"><i class="fas fa-sort-numeric-down"></i> <small>Word Count:</small> <span class="badge rounded-pill bg-success float-end"><?= getSiteWordCount(); ?></span></li>
	</ul>
	