</div>

<div id="resultsForSubscription" class="pt-3"></div>

<div class="newsletter-container" style="border: 2px solid #eee; padding: 20px; font-family: Arial, sans-serif; font-size: 16px;">
    <div class="container" style="background-color: #fff; border: 1px solid #eee; padding: 20px;">
        <div class="row">
            <div class="col-md-6">
                <h4 style="margin-top: 0; font-weight: bold; font-size: 24px;">Subscribe to Our Newsletter</h4>
                <p style="font-size: 16px; line-height: 24px;">Get the latest news and updates straight to your inbox.</p>
            </div>
            <div class="col-md-6">
                <form class="form-inline" method="post" id="newsletter-form">
                    <div class="form-group">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="newsletter-email" placeholder="Enter your email" required>
                            <div class="input-group-append">
                                <button type="submit" id="submitSubscribe" class="btn btn-primary">Subscribe</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script> 
$(document).ready(function(){     
   $("#newsletter-form").on('submit', function(e) {
	  e.preventDefault();	  
	  $.ajax({  
		 type:"POST",  
		 url: "/ajax-subscribe.php",  
		 data: {
				"newsletter-email":$("#newsletter-email").val()						
				},				 
		 success: function(results) {  
			$('#resultsForSubscription').html(results);
		 },
		 error: function(data) {
			alert("Uh oh! AJAX error!");
		 }  
	  });  
   }); 
});          
</script> 

<footer class="pt-3 my-3 text-muted border-top text-center">
  <p class="text-center">Copyright &copy; <?= date('Y'); ?> | <a href="<?= urlFull(); ?>" class="text-decoration-none"><?= urlFull(); ?></a> | <a href="<?= urlFull(); ?>rss.php" class="text-decoration-none"><i class="fa-solid fa-square-rss" style="color: orange;"></i></a> <?= getValue("homepage_hide_login_link") != "1" ? "" : '| <a href="'.urlFull().'login.php" class="text-decoration-none"><i class="fas fa-sign-in-alt"></i></a>'; ?></p>
  <p class="text-center">
    <?php if (!empty(getValue("footer_twitter"))) { ?>
      <a class="btn btn-primary border-0" style="background-color: #55acee;" href="<?= getValue("footer_twitter"); ?>" role="button"><i class="fab fa-twitter me-2"></i>Twitter</a>  
    <?php } ?>

    <?php if (!empty(getValue("footer_meta"))) { ?>
      <a class="btn btn-primary border-0" style="background-color: #3b5998;" href="<?= getValue("footer_meta"); ?>" role="button"><i class="fab fa-facebook-f me-2"></i>Facebook</a> 
    <?php } ?>   

    <?php if (!empty(getValue("footer_instagram"))) { ?>
      <a class="btn btn-primary border-0" style="background-color: #ac2bac;" href="<?= getValue("footer_instagram"); ?>" role="button"><i class="fab fa-instagram me-2"></i>Instagram</a> 
    <?php } ?>  
  </p>

  <p><?= !(empty(getValue("footer_amazon_disclosure_text"))) ? getValue("footer_amazon_disclosure_text") : ""; ?></p>

  <?php if (!empty(getValue("bottom_link_1"))) { ?>
    <p class="text-center"><?= getValue("bottom_link_1"); ?></p>
  <?php } ?>
</footer>

</div>
    <script src="<?= urlFull(); ?>js/bootstrap.bundle.min.js"></script>  
    <script src="<?= urlFull(); ?>js/blog.js"></script>  
    <script src="<?= urlFull(); ?>assets/datatables/datatables.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>	
  </body>
</html>