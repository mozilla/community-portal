<?php
/**
 * Group input for events
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	global $EM_Event;

if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'groups' ) ) {
	return false;
}

	$user_groups   = groups_get_user_groups( get_current_user_id() );
	$active_groups = array();

foreach ( $user_groups['groups'] as $group_id ) {
	$active_groups[] = groups_get_group( array( 'group_id' => $group_id ) );
}

	$group_count = count( $user_groups );
?>
<div class="event-creator__container">
	<label for="group" class="event-creator__label"><?php esc_html_e( 'Hosted By', 'community-portal' ); ?></label>
	<select name="group_id" id="group" class="event-creator__dropdown">
		<option value=""><?php esc_html_e( 'No group', 'community-portal' ); ?></option>
		<?php if ( count( $active_groups ) > 0 ) : ?>
			<?php foreach ( $active_groups as $bp_group ) : ?>
			<option value="<?php echo esc_attr( $bp_group->id ); ?>" 
				<?php
				if ( intval( $bp_group->id ) === intval( $EM_Event->group_id ) ) {
					echo esc_attr( 'selected' ); }
				?>
			><?php echo esc_html( $bp_group->name ); ?>
			</option>
		<?php endforeach; ?>
		<?php endif; ?>
	</select>
	<?php if ( em_wp_is_super_admin() ) : ?>
	<p><?php esc_html_e( 'As a site admin, you see all group events, users will only be able to choose groups they are admins of.', 'community-portal' ); ?></p>
	<?php endif; ?>
</div>
