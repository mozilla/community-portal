<?php
global $EM_Event;
if( !function_exists('bp_is_active') || !bp_is_active('groups') ) return false;
  $user_groups = groups_get_user_groups(get_current_user_id());
  if( !em_wp_is_super_admin() ){
    foreach( $group_data['groups'] as $group_id ){
    $user_groups[] = groups_get_group( array('group_id'=>$group_id)); 
	}
	$group_count = count($user_groups);
}else{
  $groups = groups_get_groups(array('show_hidden'=>true, 'per_page'=>0));
  $user_groups = $groups['groups'];
	$group_count = $groups['total'];
}
if( count($user_groups) > 0 ){ 
	?>
	<div class="event-creator__container">
    <label for="group" class="event-creator__label"><?php echo __('Hosted By') ?></label>
    <select name="group_id" id="group" class="event-creator__dropdown">
		<option value=""><?php _e('No group', 'events-manager'); ?></option>
		<?php
		//show user groups
		foreach($user_groups as $BP_Group){
			?>
			<option value="<?php echo esc_attr($BP_Group->id); ?>" <?php echo ($BP_Group->id == $EM_Event->group_id) ? esc_attr('selected') : null; ?>><?php echo __($BP_Group->name); ?></option>
			<?php
		} 
		?>
  </select>
  <?php if( em_wp_is_super_admin() ): ?>
	<!-- <p><em><?php _e ( 'As a site admin, you see all group events, users will only be able to choose groups they are admins of.', 'events-manager')?></em></p> -->
	<?php endif; 
	
}else{
	?><p><em><?php _e('No groups defined yet.','events-manager'); ?></em></p><?php
}?>
	</div>