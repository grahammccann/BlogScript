</div>

<div id="resultsForSubscription" class="pt-3"></div>

<div class="newsletter-container" style="border: 2px solid #eee; padding: 20px; font-family: Arial, sans-serif; font-size: 16px; background-color: #f5f5f5;">
    <div class="container" style="background-color: #fff; border: 1px solid #eee; padding: 20px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div class="row">
            <div class="col-md-6">
                <p style="margin-top: 0; font-weight: bold; font-size: 24px;">Subscribe to Our Newsletter</p>
                <p style="font-size: 16px; line-height: 24px;">Get the latest news and updates straight to your inbox.</p>
            </div>
            <div class="col-md-6">
                <form class="form-inline" method="post" id="newsletter-form">
                    <div class="form-group" style="width: 100%;">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <div class="input-group" style="width: 100%;">
                            <input type="email" class="form-control" id="newsletter-email" placeholder="Enter your email" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <div class="input-group-append">
                                <button type="submit" id="submitSubscribe" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Subscribe</button>
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
    <p class="text-center">Copyright &copy; <?= date('Y'); ?> | <a href="<?= urlFull(); ?>" class="text-decoration-none"><?= urlFull(); ?></a> | <a href="<?= urlFull(); ?>rss.php" class="text-decoration-none" aria-label="RSS Feed"><i class="fa-solid fa-square-rss" style="color: orange;"></i></a> <?= getValue("homepage_hide_login_link") != "1" ? "" : '| <a href="'.urlFull().'login.php" class="text-decoration-none" aria-label="Login"><i class="fas fa-sign-in-alt"></i></a>'; ?></p>
    <div class="text-center icon-container">
        <?php if (!empty(getValue("footer_twitter"))) { ?>
            <a href="<?= getValue("footer_twitter"); ?>" role="button" aria-label="Visit us on Twitter">
                <i class="fab fa-twitter fa-2x me-3"></i>
            </a>
        <?php } if (!empty(getValue("footer_meta"))) { ?>
            <a href="<?= getValue("footer_meta"); ?>" role="button" aria-label="Visit us on Twitter">
                <i class="fab fa-facebook fa-2x me-3"></i>
            </a>
        <?php } if (!empty(getValue("footer_instagram"))) { ?>
            <a href="<?= getValue("footer_instagram"); ?>" role="button" aria-label="Visit us on Instagram">
                <i class="fab fa-instagram fa-2x me-3"></i>
            </a>
        <?php } ?>
    </div>
    <?= !(empty(getValue("footer_amazon_disclosure_text"))) ? '<p>' . getValue("footer_amazon_disclosure_text") . '</p>' : ""; ?>
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