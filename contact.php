<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

	<h1 class="text-center">Contact</h1>

	<div id="results"></div>

	<div class="d-flex justify-content-center">

		<form class="w-50" method="post"> 

			<div class="mb-3">
				<label class="form-label"><strong>Name:</strong></label>
				<input class="form-control" id="contact_name" name="contact_name" type="text" placeholder="" required>
			</div>

			<div class="mb-3">	
				<label class="form-label"><strong>Subject:</strong></label>				
				<select class="form-select" id="contact_subject" name="contact_subject">
				  <option value="Query regarding your website ...">Query regarding your website ...</option>
				  <option value="Query regarding an item on your website ...">Query regarding an item on your website ...</option>
				  <option value="Query regarding something else ...">Query regarding something else ...</option>
				</select>
			</div>

			<div class="mb-3">
				<label class="form-label"><strong>Email Address:</strong></label>
				<input class="form-control" id="contact_email" name="contact_email" type="email" placeholder="" required>
			</div>

			<div class="mb-3">
				<label class="form-label"><strong>Message:</strong></label>
				<textarea class="form-control" id="contact_message" name="contact_message" rows="5" required></textarea>
			</div>

			<div class="d-grid">
				<button class="btn btn-primary btn-lg" id="submitContact" type="submit"><img src="<?= urlFull(); ?>images/img-loading.gif" alt="preloader" id="imgPreloader" style="display:none"> <i class="fas fa-envelope"></i> Send</button>
			</div>

		</form>
	
	</div>
	
	<script> 
	$(document).ready(function(){     
	   $("#submitContact").on('click', function(e) {
		  e.preventDefault();
		  $('#imgPreloader').show();	  
		  $.ajax({  
			 type:"POST",  
			 url: "/ajax-contact.php",  
			 data: {
					"contact_name":$("#contact_name").val(), 
					"contact_subject":$("#contact_subject").val(),
					"contact_email":$("#contact_email").val(),
					"contact_message":$("#contact_message").val()						
					},				 
			 success: function(results) {  
				$('#results').html(results);
				$('#imgPreloader').hide();
			 },
			 error: function(data) {
				alert("Uh oh! AJAX error!");
			 }  
		  });  
	   }); 
	});        
	</script> 
	
</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>
