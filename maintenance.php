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
		
		<?php

		if (isset($_POST['submitTheWordCount'])) {
			
			$count = getSiteArticlesToDelete($_POST['delete_words_count']);
			$value = $_POST['delete_words_count'];
			stdmsg("Found <strong>{$count}</strong> articles under or about <strong>{$value}</strong> words that can be deleted.");
			
			$articles = getArticleData($_POST['delete_words_count']);
			
			echo '<div class="card">';
			echo '<div class="card-header" style="background-color: #fff;"><strong>Articles to Delete</strong></div>';
			echo '<div class="card-body">';
			echo '<table class="table table-bordered" style="margin-bottom: 0;">';
			echo '<tr style="background-color: #ddd;"><th>ID</th><th>Image</th><th>Title</th><th>Words</th><th>Status</th></tr>';

			foreach ($articles as $article) {
				echo '<tr>';
				echo '<td>' . $article['post_id'] . '</td>';
				echo '<td><img src="uploads/' . $article['post_image'] . '" class="img-thumbnail" style="width: 100px; height: 100px; padding-bottom: 10px;"></td>';
				echo '<td style="padding: 10px; text-align: left;">' .seoFriendlyUrls($article['post_id'],$article['post_title'], false, false) . '</td>';
				echo '<td>' . getPostWordCount($article['post_id'], $article['post_body']) . '</td>';
				echo '<td style="color: green;">';
				echo '<strong>active</strong>';
				echo '</td>';
				echo '</tr>';
			}
			
			createSitemap();

			echo '</table>';
			echo '</div>';
			echo '</div>';
			
		} elseif (isset($_POST['submitWordCountDelete'])) {
			
			$articles = getArticleData($_POST['delete_words_count']);
			
			if (count($articles) < 1) {
				stderr("There are <strong>no</strong> articles matching that word count.");
			} else {
				
				echo '<div class="card">';
				echo '<div class="card-header" style="background-color: #fff;"><strong>Articles to Delete</strong></div>';
				echo '<div class="card-body">';
				echo '<table class="table table-bordered" style="margin-bottom: 0;">';
				echo '<tr style="background-color: #ddd;"><th>ID</th><th>Image</th><th>Title</th><th>Words</th><th>Status</th></tr>';

				foreach ($articles as $article) {
					echo '<tr>';
					echo '<td>' . $article['post_id'] . '</td>';
					echo '<td><img src="uploads/' . $article['post_image'] . '" class="img-thumbnail" style="width: 100px; height: 100px; padding-bottom: 10px;"></td>';
					echo '<td style="padding: 10px; text-align: left;">' .seoFriendlyUrls($article['post_id'],$article['post_title'], false, false) . '</td>';
					echo '<td>' . getPostWordCount($article['post_id'], $article['post_body']) . '</td>';
					echo '<td style="color: red;">';
					performArticlePurge($article['post_id'], $article['post_image'], $article['post_image']);
					echo '<strong>deleted</strong>';
					echo '</td>';
					echo '</tr>';
				}
				
				createSitemap();

				echo '</table>';
				echo '</div>';
				echo '</div>';
							
			}

		}
		

        if (isset($_POST['submitCleanUpImages'])) {
			
			$images = cleanUpImages();
			echo '<div class="card">';
			echo '<div class="card-header" style="background-color: #fff;"><strong>Images to Delete</strong></div>';
			echo '<div class="card-body">';
			echo '<table class="table table-bordered">';
			echo '<tr style="background-color: #ddd;"><th>ID</th><th>Image</th><th>Status</th></tr>';

			foreach ($images as $image) {
				$imagePath = 'uploads/' . $image['image_name'];
				echo '<tr>';
				echo '<td>' . $image['image_id'] . '</td>';
				echo '<td><img src="' . $imagePath . '" class="img-thumbnail" style="width: 100px; height: 100px;"></td>';
				echo '<td>';
				if (!file_exists($imagePath)) {
					$delete = DB::getInstance()->remove('images', 'image_id', $image['image_id']);
					echo '<strong style="color: red;">deleted</strong>';
				} else {
					echo '<strong style="color: green;">exists</strong>';
				}
				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
			echo '</div>';
			echo '</div>';
			
		}

		?>
	
		<div class="card">
		  <div class="card-body">
			<form action="maintenance.php" method="POST">
			  <div class="mb-3">
				<label for="inputField" class="form-label"><strong>Articles:</strong> - ...</label>
				<input type="text" class="form-control" id="inputField" name="delete_words_count" placeholder="Enter number values here only" pattern="[0-9]+" required>
				<div class="invalid-feedback">Please enter a number</div>
			  </div>
			  <div class="mb-3">
				<button type="submit" name="submitTheWordCount" class="btn btn-primary">Count Articles</button>
				<button type="submit" name="submitWordCountDelete" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete articles and images?');">Delete Articles</button>
			  </div>
			</form>
		  </div>
		</div>
		
		<div class="card">
		  <div class="card-body">
			<form action="maintenance.php" method="POST">
			  <div class="mb-3">
				<label for="inputField" class="form-label"><strong>Images:</strong> - ...</label>
				 <div class="mb-3">
				<button type="submit" name="submitCleanUpImages" class="btn btn-danger" onClick="return confirm('Are you sure you want to clean images?');">Clean Images</button>
				</div>
			  </div>
			</form>
		  </div>
		</div>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>