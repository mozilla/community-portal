<?php 
	$user = wp_get_current_user();
	$user_meta = get_user_meta($user->ID, 'community-meta-fields');
	$user_language = isset($user_meta[0]['languages'][0]) ? $user_meta[0]['languages'][0] : '';
	$user_country = isset($user_meta[0]['country']) ? $user_meta[0]['country'] : '';
?>
<form id="newsletter_form" name="newsletter__form" action="https://www.mozilla.org/en-US/newsletter/" method="post" class="newsletter__form" novalidate>
	<input type="hidden" id="fmt" name="fmt" value="H">
	<input type="hidden" id="newsletters" name="newsletters" value="about-mozilla">
	<div id="newsletter_email" class="newsletter__fields">
		<label class="newsletter__label" for="email">Email</label>
		<input class="newsletter__input" aria-label="Enter your e-mail" aria-required="true" type="email" id="email" name="email" required="" value="<?php echo (isset($user->user_email) && strlen($user->user_email) > 0 ? $user->user_email : '')?>">
		<p class="newsletter__error">This field is required.</p>
	</div>
	<div id="newsletter_details">
		<div class="newsletter__fields">
			<label class="newsletter__label" for="newsletter-country">Country or Region</label>
			<select class="newsletter__dropdown" id="newsletter-country" name="country">
				<option value="" disabled="" selected="">Select country or region</option>
				<?php foreach($countries as $index=>$country): ?>
					<option value="<?php echo $index ?>" <?php echo ($user_country === $index ? 'selected' : '') ?>><?php echo $country ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="newsletter__fields">
			<label class="newsletter__label" for="newsletter-language">Language</label>
			<select id="newsletter-language" class="newsletter__dropdown" name="country">
				<option value="" disabled="" selected="">Select language</option>
				<?php foreach($languages as $index=>$language): ?>
					<option value="<?php echo $index ?>" <?php echo (strtoupper($user_language) === strtoupper($index) ? 'selected' : '') ?>><?php echo $language ?></option>
				<?php endforeach; ?>
			</select>
			<p class="newsletter__subtext">We’ll default to English but send in these languages whenever we can.</p>
		</div>
	</div>
	<div id="newsletter_privacy" class="newsletter__cpg cpg">
		<input class="checkbox--hidden" type="checkbox" id="privacy" required>
		<label class="cpg__label" for="privacy">
			<?php echo __('I\'m okay with Mozilla handling my info as explained in this') ?>
			<a target="_blank" class="newsletter__link" rel="noopener noreferrer" href="https://www.mozilla.org/privacy/websites/"><?php echo __('Privacy Policy') ?></a>.
		</label>
		<p class="newsletter__cpg__error">You must agree to the privacy notice.</p>
	</div>
	<div class="newsletter__submit">
		<input class="btn btn--dark btn--submit" type="submit" value="Subscribe">
	</div>
</form>