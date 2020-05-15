<?php
/**
 * Single Event Host
 *
 * Event host for single events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<div class="card events-single__group">
<?php
	$em_event    = $GLOBALS['EM_Event'];
	$active_user = wp_get_current_user()->data;
	$logged_in   = mozilla_is_logged_in();

if ( $em_event->group_id ) {
	$group = new BP_Groups_Group( $em_event->group_id );
}
	$user_id     = $em_event->event_owner;
	$hosted_user = get_user_by( 'ID', $user_id );
	$is_me       = $logged_in && intval( $active_user->ID ) === intval( $user_id );
	$info        = mozilla_get_user_info( $active_user, $hosted_user, $logged_in );

if ( ( ! empty( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['SERVER_PORT'] ) && 'off' !== $_SERVER['HTTPS'] ) || 443 === $_SERVER['SERVER_PORT'] ) {
	$avatar_url = preg_replace( '/^http:/i', 'https:', $info['profile_image']->value );
} else {
	$avatar_url = $info['profile_image']->value;
}
?>
	<div class="row">
		<div class="
		<?php
		if ( is_array( $admins ) && count( $admins ) < 2 ) :
			echo 'col-lg-12 col-md-6';
else :
	echo 'events-single__hosts--multiple';
endif;
?>
col-sm-12 events-single__hosts">
				<p class="events-single__label"><?php esc_html_e( 'Hosted by', 'community-portal' ); ?></p>
				<?php if ( isset( $group ) ) : ?>
					<a class="events-single__host" href="<?php echo esc_url_raw( get_site_url( null, 'groups/' . bp_get_group_slug( $group ) ) ); ?>">
						<?php echo esc_html( bp_get_group_name( $group ) ); ?>
					</a>
				<?php endif; ?>
		</div>
		<div class="events-single__member-card col-lg-12 col-md-6 col-sm-12">
			<a href="<?php echo esc_attr( '/people/' ) . esc_attr( $hosted_user->user_nicename ); ?>">
				<div class="events-single__avatar
				<?php
				if ( false === $info['profile_image']->display || false === $info['profile_image']->value ) :
					?>
					members__avatar--identicon<?php endif; ?>" 
					<?php
					if ( $info['profile_image']->display && $info['profile_image']->value ) :
						?>
					style="background-image: url('<?php print esc_url_raw( $avatar_url ); ?>')"<?php endif; ?> data-username="<?php print esc_attr( $hosted_user->user_nicename ); ?>">
				</div>
				<p class="events-single__username"><?php echo esc_html( $hosted_user->user_nicename ); ?></p>
				<?php if ( strlen( $community_fields['first_name'] ) > 0 || strlen( $community_fields['last_name'] > 0 ) ) : ?>
					<div class="events-single__name">
					<?php
					if ( $info['first_name']->display && $info['first_name']->value ) :
						print esc_html( $info['first_name']->value );
							endif;

					if ( $info['last_name']->display && $info['last_name']->value ) :
						print esc_html( " {$info['last_name']->value}" );
							endif;
					?>
					</div>
				<?php endif; ?>
			</a>
		</div>
	</div> 
	</div>
</div>
