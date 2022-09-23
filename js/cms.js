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

$("#post_quick_tags").on("change", function() {
    var $select = $(this);
    $("#post_body").val(function(i, val) {
        return val += $select.val() + "\n";
    })
});

$("#post_image_tags").on("change", function() {
    var $select = $(this);
    $("#post_body").val(function(i, val) {
        return val += $select.val() + "\n";
    })
});