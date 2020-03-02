<?php 
	include("{$theme_directory}/countries.php");
	include("{$theme_directory}/languages.php");
?>
<div class="newsletter content newsletter__container">
	<div class="row">
		<div class="col-md-6 newsletter__details">
			<h2>Get Updates</h2>
			<p>Subscribe to our newsletter and join Mozillians all around the world and learn about impactful opportunities to support Mozilla’s mission.</p>
		</div>
		<div class="col-md-6 newsletter__form">
			<form id="newsletter_form" name="newsletter__form" action="https://www.mozilla.org/en-US/newsletter/" method="post" novalidate>
				<input type="hidden" id="fmt" name="fmt" value="H">
				<input type="hidden" id="newsletters" name="newsletters" value="about-mozilla">
				<div id="newsletter_errors" class="newsletter__errors"></div>
				<div id="newsletter_email" class="newsletter__fields">
					<label class="newsletter__label" for="email">Email</label>
					<input class="newsletter__input" aria-label="Enter your e-mail" aria-required="true" type="email" id="email" name="email" required="" placeholder="Enter your e-mail">
					<p class="newsletter__error">This field is required.</p>
				</div>
				<div id="newsletter_details">
					<div class="newsletter__fields">
						<label class="newsletter__label" for="newsletter-country">Country or Region</label>
						<select class="newsletter__dropdown" id="newsletter-country" name="country">
							<option value="" disabled="" selected="">Select country or region</option>
							<?php foreach($countries as $index=>$country): ?>
								<option value="<?php echo $index ?>"><?php echo $country ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="newsletter__fields">
						<label class="newsletter__label" for="newsletter-language">Language</label>
						<select id="newsletter-language" class="newsletter__dropdown" name="country">
							<option value="" disabled="" selected="">Select language</option>
							<?php foreach($languages as $index=>$language): ?>
								<option value="<?php echo $index ?>"><?php echo $language ?></option>
							<?php endforeach; ?>
						</select>
						<p class="newsletter__subtext">We’ll default to English but send in these languages whenever we can.</p>

					</div>
				</div>
				<div id="newsletter_privacy" class="newsletter__cpg cpg">
					<input class="checkbox--hidden" type="checkbox" id="privacy" required>
					<label class="cpg__label" for="privacy">
						<?php echo __('I\'m okay with Mozilla handling my info as explained in this') ?>
						<a target="_blank" rel="noopener noreferrer" href="https://www.mozilla.org/privacy/websites/"><?php echo __('Privacy Policy') ?></a>.
					</label>
					<p class="newsletter__cpg__error">You must agree to the privacy notice.</p>
				</div>
				<div class="newsletter__submit">
					<input class="btn btn--dark btn--submit" type="submit" value="Subscribe">
				</div>
			</form>
		</div>
	</div>
</div>