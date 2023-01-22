$(document).ready(function() {
  $('#categoryTable').DataTable();
  $('#postTable').DataTable();
  $('#userTable').DataTable();
  $('#imageTable').DataTable();
});

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})