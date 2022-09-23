<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php"); 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

	<main>
	
        <h1 class="text-center">Recovery</h1>
	
	    <div id="results"></div>
	
		<div class="d-flex justify-content-center">
		
		  <form class="w-50">  

				<div class="mb-3">
				    <label class="form-label"><strong>Email:</strong></label>
				    <input type="email" class="form-control" id="member_email" name="member_email" placeholder="" required>
				</div>

			<button class="w-100 btn btn-lg btn-primary" id="submitForgotPassword" name="submitForgotPassword" type="submit"><img src="<?= urlFull(); ?>images/img-loading.gif" alt="preloader" id="imgPreloader" style="display:none"> <i class="fas fa-envelope"></i> Send</button>
		  
		  </form>
		  
		</div>
		
		<script> 
		$(document).ready(function(){     
		   $("#submitForgotPassword").on('click', function(e){
			  e.preventDefault();
			  $('#imgPreloader').show();	  
			  $.ajax({  
				 type:"POST",  
				 url: "/ajax-recovery.php",  
				 data: {
					    "member_email":$("#member_email").val()						
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