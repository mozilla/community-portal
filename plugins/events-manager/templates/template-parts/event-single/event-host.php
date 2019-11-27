<div class="card events-single__group">
  <?php if ($EM_Event->group_id):
    $group = new BP_Groups_Group($EM_Event->group_id);
    $admins = groups_get_group_admins($group->id);
  endif; 
  ?>
  <div class="row">
      <div class="<?php if (is_array($admins) && count($admins) < 2): echo __('col-lg-12 col-md-6'); else: echo __('events-single__hosts--multiple'); endif; ?> col-sm-12 events-single__hosts">
        <p class="events-single__label"><?php echo __('Hosted by') ?></p>
        <?php if (isset($group)): ?>
          <a class="events-single__host" href="<?php echo get_site_url(null, 'groups/'.bp_get_group_slug($group)) ?>">
            <?php echo bp_get_group_name($group) ?>
          </a>
        <?php endif; ?>
      </div>
        <?php if ($EM_Event->group_id):
          if (is_array($admins)) {
            foreach($admins AS $admin) {
              $user = get_userdata($admin->user_id);
              $meta = get_user_meta($admin->user_id);
              $logged_in = mozilla_is_logged_in();
              $is_me = $logged_in && intval($current_user) === intval($admin->user_id);

              $community_fields = isset($meta['community-meta-fields'][0]) ? unserialize($meta['community-meta-fields'][0]) : Array('f');
              $community_fields['username'] =  $user->user_nicename;
              $community_fields['first_name'] = isset($meta['first_name'][0]) ? $meta['first_name'][0] : '';
              $community_fields['last_name'] = isset($meta['last_name'][0]) ? $meta['last_name'][0] : '';
              $fields = Array(
                'username',
                'profile_image_url',
                'first_name',
                'last_name',
                'country',
              );
              $visibility_settings = Array();
                foreach($fields AS $field) {
                  $field_visibility_name = "{$field}_visibility";
                  $visibility = mozilla_determine_field_visibility($field, $field_visibility_name, $community_fields, $is_me, $logged_in);
                  $field_visibility_name = ($field === 'city' || $field === 'country') ? 'profile_location_visibility' : $field_visibility_name;
                  $visibility_settings[$field_visibility_name] = $visibility;
                }

                if(stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0) {
                  $avatar_url = preg_replace("/^http:/i", "https:", $community_fields['image_url']);
                } else {
                  $avatar_url = $community_fields['image_url'];
                }
              ?>
              <div class="events-single__member-card col-lg-12 col-md-6 col-sm-12">
                <a href="<?php echo esc_attr(get_site_url().'/members/'.$user->user_nicename)?>">
                  <div class="events-single__avatar<?php if(!$visibility_settings['profile_image_url_visibility'] || !strlen($community_fields['image_url']) > 0) : ?> members__avatar--identicon<?php endif; ?>" <?php if($visibility_settings['profile_image_url_visibility'] && strlen($community_fields['image_url']) > 0): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $community_fields['username']; ?>">
                  </div>
                  <p class="events-single__username"><?php echo __($user->user_nicename) ?></p>
                </a>
              </div>
            <?php
            }
          }
        ?>
        <?php 
          else: 
          $user_id = $EM_Event->event_owner;
          $hosted_user = get_user_by('ID', $user_id);
          $meta = get_user_meta($user_id);
          $logged_in = mozilla_is_logged_in();
          $is_me = $logged_in && intval($current_user) === intval($user_id);

          $community_fields = isset($meta['community-meta-fields'][0]) ? unserialize($meta['community-meta-fields'][0]) : Array('f');
          $community_fields['username'] =  $hosted_user->user_nicename;
          $community_fields['first_name'] = isset($meta['first_name'][0]) ? $meta['first_name'][0] : '';
          $community_fields['last_name'] = isset($meta['last_name'][0]) ? $meta['last_name'][0] : '';
          $fields = Array(
            'username',
            'profile_image_url',
            'first_name',
            'last_name',
            'country',
          );

          $visibility_settings = Array();
          foreach($fields AS $field) {
            $field_visibility_name = "{$field}_visibility";
            $visibility = mozilla_determine_field_visibility($field, $field_visibility_name, $community_fields, $is_me, $logged_in);
            $field_visibility_name = ($field === 'city' || $field === 'country') ? 'profile_location_visibility' : $field_visibility_name;
            $visibility_settings[$field_visibility_name] = $visibility;
          }
          if(stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0) {
            $avatar_url = preg_replace("/^http:/i", "https:", $community_fields['image_url']);
          } else {
            $avatar_url = $community_fields['image_url'];
          }
        ?>
        <div class="events-single__member-card col-lg-12 col-md-6 col-sm-12">
          <a href="<?php echo esc_attr(get_site_url().'/members/'.$hosted_user->user_nicename)?>">
            <div class="events-single__avatar<?php if(!$visibility_settings['profile_image_url_visibility'] || !strlen($community_fields['image_url']) > 0) : ?> members__avatar--identicon<?php endif; ?>" <?php if($visibility_settings['profile_image_url_visibility'] && strlen($community_fields['image_url']) > 0): ?> style="background-image: url('<?php print $avatar_url; ?>')"<?php endif; ?> data-username="<?php print $community_fields['username']; ?>">
            </div>
            <p class="events-single__username"><?php echo __($hosted_user->user_nicename) ?></p>
          </a>
        </div>
      <?php endif; ?>
    </div> 
  </div>
</div>