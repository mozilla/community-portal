<?php 
	$user = wp_get_current_user();
	$user_meta = get_user_meta($user->ID, 'community-meta-fields', true);
	$user_language = is_array($user_meta) && sizeof($user_meta) > 0 && isset($user_meta['languages']) && sizeof($user_meta['languages']) > 0 ? $user_meta['languages'][0] : '';
	$user_country = is_array($user_meta) && sizeof($user_meta) > 0 && isset($user_meta['country']) ? $user_meta['country'] : '';
?>
<div class="row">
	<input type="hidden" id="fmt" name="fmt" value="H">
	<input type="hidden" id="newsletters" name="newsletters" value="about-mozilla">
	<div class="col-lg-4 profile__newsletter__fields">
		<label class="newsletter__label" for="newsletter-email">Email</label>
		<input class="newsletter__input" aria-label="Enter your e-mail" aria-required="true" type="email" id="newsletter-email" name="newsletter-email" value="<?php echo (isset($user->user_email) && strlen($user->user_email) > 0 ? $user->user_email : '')?>">
		<p class="newsletter__error">Invalid email.</p>
	</div>
	<div class="col-lg-4 profile__newsletter__fields">
		<label class="newsletter__label" for="newsletter-country">Country or Region</label>
		<select class="newsletter__dropdown" id="newsletter-country" name="country">
			<option value="" disabled="" selected="">Country or region</option>
			<?php foreach($countries as $index=>$country): ?>
				<option value="<?php echo $index ?>" <?php echo ($user_country === $index ? 'selected' : '') ?>><?php echo $country ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-lg-3 profile__newsletter__fields">
		<label class="newsletter__label" for="newsletter-language">Language</label>
		<select id="newsletter-language" class="newsletter__dropdown" name="country">
			<option value="" disabled="" selected="">Language</option>
			<?php foreach($languages as $index=>$language): ?>
				<option value="<?php echo $index ?>" <?php echo (strtoupper($user_language) === strtoupper($index) ? 'selected' : '') ?>><?php echo $language ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<p class="newsletter__subtext">We’ll default to English but send in these languages whenever we can.</p>
	<div class="cpg">
		<input class="checkbox--hidden" type="checkbox" id="newsletter">
		<label class="cpg__label" for="newsletter">
			<?php echo __('Yes, subscribe me to the newsletter') ?>
		</label>
		<p class="newsletter__cpg__error"><?php echo __('You must agree to the privacy notice.') ?></p>
	</div>
</div>