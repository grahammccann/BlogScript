<div class="card-header bg-primary text-white"><i class="fas fa-chart-pie"></i> <a href="dashboard.php" class="text-decoration-none text-white">Analytics</a></div>
	<ul class="list-group list-group-flush">		  				
	    <li class="list-group-item list-group-item-light"><small>Word Count:</small> <span class="badge rounded-pill bg-success float-end"><?= !empty(getSiteWordCount()) ? getSiteWordCount() : "0"; ?></span></li>
	    <li class="list-group-item list-group-item-light"><small>Clicks Count:</small> <span class="badge rounded-pill bg-success float-end"><?= !empty(getLinkCount()) ? getLinkCount() : "0"; ?></span></li>
	</ul>