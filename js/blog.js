$(document).ready(function() {
  $('#tableCategories').DataTable();
  $('#tableClicks').DataTable();
  $('#tableImages').DataTable();
  $('#tableNewsletters').DataTable();
  $('#tablePages').DataTable();
  $('#tablePosts').DataTable();
  $('#tableShorteners').DataTable();
  $('#tableUsers').DataTable();
});

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})