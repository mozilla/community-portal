<?php
/**
 * Campaign RSVP
 *
 * Form to RSVP to campaigns when not logged in
 *
 * @package    WordPress
 * @subpackage community-portal
 * @version    1.0.0
 * @author     Playground Inc.
 */

?>

<div class="lightbox__container campaign-rsvp">
	<button id="close-rsvp-lightbox" class="btn btn--close">
		<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M18 6.2019L6 18.2019" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M6 6.2019L18 18.2019" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</button>
	<div class="campaign-rsvp__initial">
		<h2 class="campaign-rsvp__title title"><?php esc_html_e( 'Get Involved', 'community-portal' ); ?></h2>
		<div>
			<h3 class="campaign-rsvp__subtitle"><?php esc_html_e( 'Sign Up', 'community-portal' ); ?></h3>
			<p>
				<?php esc_html_e( 'Create a Community Portal account to connect with the community and participate in a number of exciting campaigns.', 'community-portal' ); ?>
			</p>
			<div class="campaign-rsvp__section--center">
				<a class="btn btn--dark btn--submit" href="/wp-login.php?action=login"><?php esc_html_e( 'Sign Up', 'community-portal' ); ?></a>
				<p class="campaign-rsvp__login">
					<?php esc_html_e( 'Already a member?', 'community-portal' ); ?>
					<a href="/wp-login.php?action=login" class="campaign-rsvp__link"><?php esc_html_e( 'Log In', 'community-portal' ); ?></a>
				</p>
			</div>
		</div>
		<div class="campaign-rsvp__form">
			<h3 class="campaign-rsvp__subtitle"><?php esc_html_e( 'Get involved without a user account', 'community-portal' ); ?></h3>
			<p><?php esc_html_e( 'Fill out the form below to join this campaign without creating a user account', 'community-portal' ); ?></p>
			<form id="campaign-rsvp-form" method="post" novalidate data-list="<?php print esc_attr( $mailchimp->id ); ?>" data-campaign="<?php print esc_attr( $post->ID ); ?>">
				<div class="row campaign-rsvp__section">
					<div class="col-lg-6 campaign-rsvp__row campaign-rsvp__row--double">
						<label class="campaign-rsvp__label" for="first-name"><?php esc_html_e( 'First Name', 'community-portal' ); ?></label>
						<input id="rsvp-first-name" class="campaign-rsvp__input" type="text" id="first-name" name="first-name">
						<p class="form__error-container"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
					</div>
					<div class="col-lg-6 campaign-rsvp__row campaign-rsvp__row--double">
						<label class="campaign-rsvp__label" for="last-name"><?php esc_html_e( 'Last Name', 'community-portal' ); ?></label>
						<input id="rsvp-last-name" class="campaign-rsvp__input" type="text" id="last-name" name="last-name">
						<p class="form__error-container"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 campaign-rsvp__row">
						<label class="campaign-rsvp__label" for="email"><?php esc_html_e( 'Email', 'community-portal' ); ?></label>
						<input id="rsvp-email" class="campaign-rsvp__input" type="email" name="email">
						<p class="form__error-container"><?php esc_html_e( 'Invalid email', 'community-portal' ); ?></p>
					</div>
					<div class="col-sm-12 cpg">
						<input class="checkbox--hidden" type="checkbox" name="privacy-policy" id="privacy-policy">
						<label class="cpg__label" for="privacy-policy">
							<?php esc_html_e( 'I\'m okay with Mozilla handling my info as explained in this', 'community-portal' ); ?> 
							<a href="https://www.mozilla.org/en-US/privacy/"><?php esc_html_e( 'Privacy Policy', 'community-portal' ); ?></a>
						</label>
						<p class="form__error-container"><?php esc_html_e( 'You must agree to the privacy policy', 'community-portal' ); ?></p>
					</div>
					<div class="col-sm-12 campaign-rsvp__section--center">
						<input class="btn btn--light btn--submit" type="submit" value="<?php esc_html_e( 'Get Involved', 'community-portal' ); ?>">
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="campaign-rsvp__success">
		<h3 class="campaign-rsvp__title"><?php esc_html_e( 'Thank you for getting involved!', 'community-portal' ); ?></h3>
		<p><?php esc_html_e( 'An email has been sent with more details', 'community-portal' ); ?></p>
	</div>
	<div class="campaign-rsvp__failure">
		<h3 class="campaign-rsvp__title"><?php esc_html_e( 'Something went wrong.', 'community-portal' ); ?></h3>
		<p><?php esc_html_e( 'Please try again later.', 'community-portal' ); ?></p>
	</div>
</div>
