<footer class="pt-5 my-5 text-muted border-top text-center">Copyright &copy; <?= date('Y'); ?> | <a href="<?= urlFull(); ?>" class="text-decoration-none"><?= urlFull(); ?></a> <?= getValue("homepage_hide_login_link") != "1" ? "" : '| <a href="'.urlFull().'login.php" class="text-decoration-none"><i class="fas fa-sign-in-alt"></i></a>'; ?></footer>

<p class="text-center">
	<?php 
	if (!empty(getValue("footer_twitter"))) { ?>
	<a class="btn btn-primary border-0" style="background-color: #55acee;" href="<?= getValue("footer_twitter"); ?>" role="button"><i class="fab fa-twitter me-2"></i>Twitter</a>	
	<?php } ?>

	<?php 
	if (!empty(getValue("footer_meta"))) { ?>
	<a class="btn btn-primary border-0" style="background-color: #3b5998;" href="<?= getValue("footer_meta"); ?>" role="button"><i class="fab fa-facebook-f me-2"></i>Facebook</a>	
	<?php } ?>		

	<?php 
	if (!empty(getValue("footer_instagram"))) { ?>
	<a class="btn btn-primary border-0" style="background-color: #ac2bac;" href="<?= getValue("footer_instagram"); ?>" role="button"><i class="fab fa-instagram me-2"></i>Instagram</a>	
	<?php } ?>	
</p>

<p><?= !(empty(getValue("footer_amazon_disclosure_text"))) ? getValue("footer_amazon_disclosure_text") : ""; ?></p>

</div>
    <script src="<?= urlFull(); ?>js/bootstrap.bundle.min.js"></script>  
    <script src="<?= urlFull(); ?>js/cms.js"></script>  
    <script src="<?= urlFull(); ?>assets/datatables/datatables.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>	
  </body>
</html>