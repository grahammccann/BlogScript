<div class="card-header bg-primary text-white"><i class="fas fa-chart-pie"></i> <a href="dashboard.php" class="text-decoration-none text-white">Analytics</a></div>
<ul class="list-group list-group-flush">                      
    <li class="list-group-item list-group-item-light"><small>Word Count:</small> <span class="badge rounded-pill bg-success float-end"><?= !empty(getSiteWordCount()) ? getSiteWordCount() : "0"; ?></span></li>
    <li class="list-group-item list-group-item-light"><small>Clicks Count:</small> <span class="badge rounded-pill bg-success float-end"><?= doTableCount("clicks"); ?></span></li>
    <ul class="list-group">
      <li class="list-group-item list-group-item-light d-flex justify-content-around">
        <a href="analytics.php" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View Analytics">
            <i class="fas fa-eye"></i>
        </a>
        <a href="maintenance.php" class="btn btn-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Site Maintenance">
            <i class="fas fa-hands-wash"></i>
        </a>
      </li>
    </ul>
</ul>