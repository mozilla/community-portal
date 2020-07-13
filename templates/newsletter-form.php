<?php
/**
 * Newsletter Form
 *
 * Basic form to RSVP to newsletter
 *
 * @package    WordPress
 * @subpackage community-portal
 * @version    1.0.0
 * @author     Playground Inc.
 */

?>

<?php
	$user                 = wp_get_current_user();
	$user_meta            = get_user_meta( $user->ID, 'community-meta-fields', true );
	$user_language        = is_array( $user_meta ) && count( $user_meta ) > 0 && isset( $user_meta['languages'] ) && count( $user_meta['languages'] ) > 0 ? $user_meta['languages'][0] : '';
	$user_country         = is_array( $user_meta ) && count( $user_meta ) > 0 && isset( $user_meta['country'] ) ? $user_meta['country'] : '';
	$newsletter_languages = array(
		'de' => 'Deutsch',
		'fr' => 'Français',
		'en' => 'English',
		'es' => 'Español',
		'pt' => 'Português do Brasil',
		'ru' => 'русский язык',
	);
	?>
<h2 class="profile__form-title"><?php esc_html_e( 'Get Updates', 'community-portal' ); ?></h2>
<p><?php esc_html_e( 'Subscribe to our newsletter and join Mozillians all around the world and learn about impactful opportunities to support Mozilla’s mission.', 'community-portal' ); ?></p>
<input type="hidden" id="fmt" name="fmt" value="H">
<input type="hidden" id="newsletters" name="newsletters" value="about-mozilla">
<div class="row profile__newsletter__container">
	<div class="col-lg-4 profile__newsletter__fields">
		<label class="newsletter__label" for="newsletter-email">Email</label>
		<input class="newsletter__input" aria-label="Enter your e-mail" aria-required="true" type="email" id="newsletter-email" name="newsletter-email" value="
		<?php
		if ( isset( $user->user_email ) && strlen( $user->user_email ) > 0 ) {
			echo esc_attr( $user->user_email );
		} else {
			echo esc_attr( '' );
		}
		?>
		">
		<span class="newsletter__error"><?php esc_html_e( 'Invalid email.', 'community-portal' ); ?></span>
	</div>
	<div class="col-lg-4 profile__newsletter__fields">
		<label class="newsletter__label" for="newsletter-country"><?php esc_html_e( 'Country or Region', 'community-portal' ); ?></label>
		<select class="newsletter__dropdown" id="newsletter-country" name="newsletter-country">
			<option value="" disabled="" selected=""><?php esc_html_e( 'Country or region', 'community-portal' ); ?></option>
			<?php foreach ( $countries as $index => $country ) : ?>
				<option value="<?php echo esc_attr( strtolower( $index ) ); ?>" 
										<?php
										if ( $user_country === $index ) {
											echo esc_attr( 'selected' );
										} else {
											echo esc_attr( '' );
										}
										?>
				>
				<?php echo esc_html( $country ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-lg-3 profile__newsletter__fields">
		<label class="newsletter__label" for="newsletter-language"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
		<select id="newsletter-language" class="newsletter__dropdown" name="newsletter-language">
			<option value="" disabled="" selected=""><?php esc_html_e( 'Language', 'community-portal' ); ?></option>
			<?php foreach ( $newsletter_languages as $index => $language ) : ?>
				<option 
					value="<?php echo esc_attr( $index ); ?>" 
					<?php
					if ( strtoupper( $user_language ) === strtoupper( $index ) ) {
						echo esc_attr( 'selected ' );
					} else {
						echo esc_attr( '' );
					}
					?>
				>
					<?php echo esc_html( $language ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="col-lg-12">
		<p class="newsletter__subtext"><?php esc_html_e( 'We\'ll default to English but send in these languages whenever we can.', 'community-portal' ); ?></p>
		<div class="cpg">
			<input class="checkbox--hidden" type="checkbox" id="newsletter">
			<label class="cpg__label" for="newsletter">
				<?php esc_html_e( 'Yes, subscribe me to the newsletter', 'community-portal' ); ?>
			</label>
			<p class="newsletter__cpg__error"><?php esc_html_e( 'You must agree to the privacy notice.', 'community-portal' ); ?></p>
		</div>
	</div>
</div>
