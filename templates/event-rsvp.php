<?php
/**
 * Event RSVP
 *
 * Form to RSVP to events when not logged in
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
	</div>
</div>
