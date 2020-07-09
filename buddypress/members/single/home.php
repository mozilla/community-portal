<?php
/**
 * Member home
 *
 * Handles routing of member profile and editing
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	$visibility_options = array(
		PrivacySettings::REGISTERED_USERS => __( 'Registered Users', 'community-portal' ),
		PrivacySettings::PUBLIC_USERS     => __( 'Public (Everyone)', 'community-portal' ),
		PrivacySettings::PRIVATE_USERS    => __( 'Private (Only Me)', 'community-portal' ),
	);

	$theme_directory = get_template_directory();
	$pronouns        = array(
		__( 'She/Her', 'community-portal' ),
		__( 'He/Him', 'community-portal' ),
		__( 'They/Them', 'community-portal' ),
	);

	$tags = get_tags( array( 'hide_empty' => false ) );
	?>

<div class="profile">
	<?php if ( bp_is_my_profile() && 'edit' === bp_current_action() ) : ?>
		<?php
			// Get current user!
			$user = wp_get_current_user()->data;

			// Get default user meta data!
			$meta = get_user_meta( $user->ID );

		if ( isset( $meta['community-meta-fields'] ) && isset( $meta['community-meta-fields'][0] ) ) {
			$community_fields = unserialize( $meta['community-meta-fields'][0] );
		} else {
			$community_fields = false;
		}

			$form = ( ! isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['my_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce_field'] ) ), 'protect_content' ) ) ? $_POST : false;

		if ( $form && isset( $form['tags'] ) ) {
			$form_tags = array_filter( explode( ',', sanitize_text_field( wp_unslash( $form['tags'] ) ) ) );
		} else {

			if ( $community_fields && isset( $community_fields['tags'] ) ) {
				$form_tags = array_filter( explode( ',', $community_fields['tags'] ) );
			} else {
				$form_tags = array();
			}
		}
			do_action( 'bp_before_edit_member_page' );

			$complete         = ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['my_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce_field'] ) ), 'protect_content' ) && isset( $_POST['complete'] ) && true === $_POST['complete'] ) ? true : false;
			$edit             = ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['my_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_nonce_field'] ) ), 'protect_content' ) && isset( $_POST['edit'] ) && true === $_POST['edit'] ) ? true : false;
			$updated_username = isset( $form['username'] ) ? $form['username'] : false;

			include "{$theme_directory}/buddypress/members/single/edit.php";
		?>
	<?php else : ?>
		<?php
			$user_id = bp_displayed_user_id();
			$user    = get_user_by( 'ID', $user_id );

			$logged_in = mozilla_is_logged_in();
			$live_user = wp_get_current_user()->data;

			$is_me = $logged_in && intval( $live_user->ID ) === intval( $user->ID );
			$info  = mozilla_get_user_info( $live_user, $user, $logged_in );
			include "{$theme_directory}/buddypress/members/single/profile.php";
		?>
	<?php endif; ?>
</div>	
