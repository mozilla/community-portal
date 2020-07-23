<?php
/**
 *
 * Campaigns Newsletter
 *
 * Form to signup to newsletter
 *
 * @package    WordPress
 * @subpackage community-portal
 * @version    1.0.0
 * @author     Playground Inc.
 */

?>
<?php

	require "{$theme_directory}/countries.php";
	$languages     = array(
		'de' => 'Deutsch',
		'fr' => 'Français',
		'en' => 'English',
		'es' => 'Español',
		'pt' => 'Português do Brasil',
		'ru' => 'русский язык',
	);
	$user          = wp_get_current_user();
	$user_meta     = get_user_meta( $user->ID, 'community-meta-fields', true );
	$user_language = is_array( $user_meta ) && count( $user_meta ) > 0 && isset( $user_meta['languages'] ) && count( $user_meta['languages'] ) > 0 ? $user_meta['languages'][0] : '';
	$user_country  = is_array( $user_meta ) && count( $user_meta ) > 0 && isset( $user_meta['country'] ) ? $user_meta['country'] : '';
	?>

<div class="content newsletter__container">
	<div class="row">
		<div class="col-lg-6 newsletter__details">
			<h2><?php esc_html_e( 'Get Updates', 'community-portal' ); ?></h2>
			<p class="newsletter__text"><?php esc_html_e( 'Subscribe to our newsletter and join Mozillians all around the world and learn about impactful opportunities to support Mozilla’s mission.', 'community-portal' ); ?></p>
		</div>
		<div class="col-lg-6 newsletter__signup">
			<form id="newsletter_form" name="newsletter__form" action="https://www.mozilla.org/newsletter/" method="post" class="newsletter__form" novalidate>
				<?php wp_nonce_field( 'newsletter_nonce', 'newsletter_nonce_field' ); ?>
				<input type="hidden" id="fmt" name="fmt" value="H">
				<input type="hidden" id="newsletters" name="newsletters" value="about-mozilla">
				<div id="newsletter_email" class="newsletter__fields">
					<label class="newsletter__label" for="newsletter-email"><?php esc_html_e( 'Email', 'community-portal' ); ?></label>
					<input class="newsletter__input" aria-label="Enter your e-mail" aria-required="true" type="email" id="newsletter-email" name="email" required="" value="<?php echo ( isset( $user->user_email ) && strlen( $user->user_email ) > 0 ? esc_attr( $user->user_email ) : esc_attr( '' ) ); ?>">
					<p class="newsletter__error"><?php esc_html_e( 'Invalid email.', 'community-portal' ); ?></p>
				</div>
				<div id="newsletter_details">
					<div class="newsletter__fields">
						<label class="newsletter__label" for="newsletter-country"><?php esc_html_e( 'Country or Region', 'community-portal' ); ?></label>
						<select class="newsletter__dropdown" id="newsletter-country" name="newsletter-country">
							<option value="" disabled="" selected=""><?php esc_html_e( 'Select country or region', 'community-portal' ); ?></option>
							<?php foreach ( $countries as $index => $country ) : ?>
								<option value="<?php echo esc_html( strtolower( $index ) ); ?>" 
									<?php
									if ( $user_country === $index ) {
										echo esc_attr( 'selected' );
									} else {
										esc_attr( '' );
									}
									?>
									>
									<?php echo esc_html( $country ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="newsletter__fields">
						<label class="newsletter__label" for="newsletter-language"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
						<select id="newsletter-language" class="newsletter__dropdown" name="newsletter-language">
							<option value="" disabled="" selected=""><?php esc_html_e( 'Select language', 'community-portal' ); ?></option>
							<?php foreach ( $languages as $index => $language ) : ?>
								<option value="<?php echo esc_attr( $index ); ?>" <?php echo ( strtoupper( $user_language ) === strtoupper( $index ) ? esc_attr( 'selected' ) : esc_attr( '' ) ); ?>><?php echo esc_html( $language ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="newsletter__subtext"><?php esc_html_e( 'We’ll default to English but send in these languages whenever we can.', 'community-portal' ); ?></p>
					</div>
				</div>
				<div id="newsletter_privacy" class="newsletter__cpg cpg">
					<input class="checkbox--hidden" type="checkbox" id="privacy" required>
					<label class="cpg__label" for="privacy">
						<?php esc_html_e( 'I\'m okay with Mozilla handling my info as explained in this', 'community-portal' ); ?>
						<a target="_blank" class="newsletter__link" rel="noopener noreferrer" href="https://www.mozilla.org/privacy/websites/"><?php esc_html_e( 'Privacy Policy', 'community-portal' ); ?></a>.
					</label>
					<p class="newsletter__cpg__error"><?php esc_html_e( 'You must agree to the privacy notice.', 'community-portal' ); ?></p>
				</div>
				<div class="newsletter__submit">
					<input class="btn btn--dark btn--submit" type="submit" value="<?php esc_html_e( 'Subscribe', 'community-portal' ); ?>">
			</div>
			</form>
			<div class="newsletter__success">
				<h2><?php esc_html_e( 'Thanks!', 'community-portal' ); ?></h2>
				<p class="newsletter__text"><?php esc_html_e( 'If you haven’t previously confirmed a subscription to a Mozilla-related newsletter you may have to do so. Please check your inbox and spam filter for an email from us.', 'community-portal' ); ?></p>
			</div>
			<div class="newsletter__failure">
				<h2><?php esc_html_e( 'Sorry!', 'community-portal' ); ?></h2>
				<p class="newsletter__text"><?php esc_html_e( 'Looks like something went wrong.', 'community-portal' ); ?></p>
			</div>
		</div>
	</div>
</div>
