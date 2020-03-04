<?php 
	include("{$theme_directory}/countries.php");
	include("{$theme_directory}/languages.php");
?>

<div class="content newsletter__container">
	<div class="row">
		<div class="col-lg-6 newsletter__details">
			<h2>Get Updates</h2>
			<p class="newsletter__text">Subscribe to our newsletter and join Mozillians all around the world and learn about impactful opportunities to support Mozilla’s mission.</p>
		</div>
		<div class="col-lg-6 newsletter__signup">
			<?php include get_template_directory()."/templates/newsletter_form.php"; ?>
			<div class="newsletter__success">
				<h2>Thanks!</h2>
				<p class="newsletter__text">If you haven’t previously confirmed a subscription to a Mozilla-related newsletter you may have to do so. Please check your inbox and spam filter for an email from us.</p>
			</div>
		</div>
	</div>
</div>