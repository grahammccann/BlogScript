<div class="card-header bg-primary text-white"><i class="fas fa-chart-pie"></i> <a href="dashboard.php" class="text-decoration-none text-white">Analytics &amp; Operations</a></div>
<ul class="list-group list-group-flush">                      
    <li class="list-group-item list-group-item-light"><small>Word Count:</small> <span class="badge rounded-pill bg-success float-end"><?= !empty(getSiteWordCount()) ? getSiteWordCount() : "0"; ?></span></li>
    <li class="list-group-item list-group-item-light"><small>Clicks Count:</small> <span class="badge rounded-pill bg-success float-end"><?= doTableCount("clicks"); ?></span></li>
    <li class="list-group-item list-group-item-light">
        <a href="analytics.php" class="btn btn-info w-100" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View Clicks">
            <i class="fas fa-eye"></i> View Clicks
        </a>
    </li>
    <li class="list-group-item list-group-item-light">
        <a href="maintenance.php" class="btn btn-warning w-100" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Site Maintenance">
            <i class="fas fa-hands-wash"></i> Perform Maintenance
        </a>
    </li>
    <li class="list-group-item list-group-item-light">
        <a href="dashboard.php?robots=1" class="btn btn-danger w-100" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create Robots .txt File">
            <i class="fas fa-robot"></i> Generate Robots .txt File
        </a>
    </li>
    <li class="list-group-item list-group-item-light">
        <a href="dashboard.php?sitemap=1" class="btn btn-success w-100" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Create an XML sitemap">
            <i class="fas fa-sitemap"></i> Generate XML Sitemap
        </a>
    </li>
</ul>