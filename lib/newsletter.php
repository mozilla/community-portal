<?php
/**
 * Newsletter Library
 *
 * Newsletter Functionality
 *
 * @package    WordPress
 * @subpackage community-portal
 * @version    1.0.0
 * @author     Playground Inc.
 */

/**
 * Newsletter Subscribe
 **/
function mozilla_newsletter_subscribe() {
	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		$user = wp_get_current_user();
		if ( isset( $user ) ) {
			if ( isset( $_POST['subscribed'] ) ) {
				$subscribed = intval( $_POST['subscribed'] );
				update_user_meta( $user->ID, 'newsletter', $subscribed );
			}
			wp_send_json_success(
				array(
					'status' => 'success',
				)
			);
		}
		wp_send_json_success(
			array(
				'status' => 'success',
			)
		);
	}
}
