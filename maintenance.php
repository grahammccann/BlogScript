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
			
			$articles = getArticleData($_POST['delete_words_count']);
			
			if (count($articles) < 1) {
				stderr("There are <strong>no</strong> articles matching that word count.");
			} else {
				
			    stdmsg("Found <strong>{$count}</strong> articles under or about <strong>{$value}</strong> words that can be deleted.");
			
				echo '<div class="card" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
				echo '<div class="card-header" style="background-color: #fff; border-bottom: 1px solid #ddd;"><strong>Articles to Delete</strong></div>';
				echo '<div class="card-body">';
				echo '<table class="table table-bordered" style="border-collapse: collapse; margin-bottom: 0;">';
				echo '<tr style="background-color: #ddd;"><th>ID</th><th>Image</th><th>Title</th><th>Words</th><th>Status</th></tr>';

				foreach ($articles as $article) {
					echo '<tr>';
					echo '<td style="padding: 8px; border: 1px solid #ddd;">' . $article['post_id'] . '</td>';
					echo '<td style="padding: 8px; border: 1px solid #ddd;"><img src="uploads/' . $article['post_image'] . '" class="img-thumbnail" style="width: 100px; height: 100px; padding-bottom: 10px;"></td>';
					echo '<td style="padding: 10px; text-align: left; border: 1px solid #ddd;">' . seoFriendlyUrls($article['post_id'], $article['post_title'], false, false) . '</td>';
					echo '<td style="padding: 8px; border: 1px solid #ddd;">' . getPostWordCount($article['post_id'], $article['post_body']) . '</td>';
					echo '<td style="padding: 8px; border: 1px solid #ddd; color: green;">';
					echo '<strong>Live</strong>';
					echo '</td>';
					echo '</tr>';
				}
				
				createSitemap();

				echo '</table>';
				echo '</div>';
				echo '</div>';
				
			}
			
		} elseif (isset($_POST['submitWordCountDelete'])) {
			
			$articles = getArticleData($_POST['delete_words_count']);
			
			if (count($articles) < 1) {
				stderr("There are <strong>no</strong> articles matching that word count.");
			} else {
				
				stdmsg("Found <strong>{$count}</strong> articles under or about <strong>{$value}</strong> words that can be deleted.");
				
				echo '<div class="card" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
				echo '<div class="card-header" style="background-color: #fff; border-bottom: 1px solid #ddd;"><strong>Articles to Delete</strong></div>';
				echo '<div class="card-body">';
				echo '<table class="table table-bordered" style="border-collapse: collapse; margin-bottom: 0;">';
				echo '<tr style="background-color: #ddd;"><th>ID</th><th>Image</th><th>Title</th><th>Words</th><th>Status</th></tr>';

				foreach ($articles as $article) {
					echo '<tr>';
					echo '<td style="padding: 8px; border: 1px solid #ddd;">' . $article['post_id'] . '</td>';
					echo '<td style="padding: 8px; border: 1px solid #ddd;"><img src="uploads/' . $article['post_image'] . '" class="img-thumbnail" style="width: 100px; height: 100px; padding-bottom: 10px;"></td>';
					echo '<td style="padding: 10px; text-align: left; border: 1px solid #ddd;">' . seoFriendlyUrls($article['post_id'], $article['post_title'], false, false) . '</td>';
					echo '<td style="padding: 8px; border: 1px solid #ddd;">' . getPostWordCount($article['post_id'], $article['post_body']) . '</td>';
					echo '<td style="padding: 8px; border: 1px solid #ddd; color: red;">';
					performArticlePurge($article['post_id'], $article['post_image'], $article['post_image']);
					echo '<strong>Deleted</strong>';
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
			echo '<div class="card" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
			echo '<div class="card-header" style="background-color: #fff; border-bottom: 1px solid #ddd;"><strong>Images to Delete</strong></div>';
			echo '<div class="card-body">';
			echo '<table class="table table-bordered" style="border-collapse: collapse;">';
			echo '<tr style="background-color: #ddd;"><th>ID</th><th>Image</th><th>Status</th></tr>';

			foreach ($images as $image) {
				$imagePath = 'uploads/' . $image['image_name'];
				echo '<tr>';
				echo '<td style="padding: 8px; border: 1px solid #ddd;">' . $image['image_id'] . '</td>';
				echo '<td style="padding: 8px; border: 1px solid #ddd;"><img src="' . $imagePath . '" class="img-thumbnail" style="width: 100px; height: 100px;"></td>';
				echo '<td style="padding: 8px; border: 1px solid #ddd;">';
				if (!file_exists($imagePath)) {
					$delete = DB::getInstance()->remove('images', 'image_id', $image['image_id']);
					echo '<strong style="color: red;">Deleted</strong>';
				} else {
					echo '<strong style="color: green;">Exists</strong>';
				}
				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
			echo '</div>';
			echo '</div>';
			
		}

		?>	

		<div class="card" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
			<div class="card-body">
				<form action="maintenance.php" method="POST">
					<div class="mb-3">
						<label for="inputField" class="form-label"><strong>Articles:</strong></label>
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

		<div class="card" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
			<div class="card-body">
				<form action="maintenance.php" method="POST">
					<div class="mb-3">
						<label for="inputField" class="form-label"><strong>Images:</strong></label>
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