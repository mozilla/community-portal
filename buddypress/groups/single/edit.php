<?php
    do_action('bp_before_edit_group_page'); 
    $group_id = bp_get_current_group_id();
    $group = $bp->groups->current_group;
    $group_meta = groups_get_groupmeta($group_id, 'meta');
    $group_admins = groups_get_group_admins($group_id);

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $form = $_POST;

        if(isset($form['tags'])) {
            $form['tags'] = array_filter(explode(',', $form['tags']));
        }
    } else {
		// Prepopulate
        $form['group_name'] = $group->name;
        $form['group_desc'] = $group->description;
        $form['group_type'] = isset($group_meta['group_type']) ? $group_meta['group_type'] : 'Online';
        $form['group_language'] = isset($group_meta['group_language']) ? $group_meta['group_language'] : '0';
        $form['group_country'] = isset($group_meta['group_country']) ? $group_meta['group_country'] : '0';
        $form['group_city'] = isset($group_meta['group_city']) ? $group_meta['group_city'] : '';
        $form['image_url'] = isset($group_meta['group_image_url']) ? $group_meta['group_image_url'] : '';
        $form['tags'] = $group_meta['group_tags'];
        $form['group_address_type'] = isset($group_meta['group_address_type']) ? $group_meta['group_address_type'] : 'Address';
        $form['group_address'] = isset($group_meta['group_address']) ? $group_meta['group_address'] : '';
        $form['group_meeting_details'] = isset($group_meta['group_meeting_details']) ? $group_meta['group_meeting_details'] : '';
		$form['group_discourse'] = isset($group_meta['group_discourse']) ? $group_meta['group_discourse'] : '';
        $form['group_telegram'] = isset($group_meta['group_telegram']) ? $group_meta['group_telegram'] : '';
        $form['group_facebook'] = isset($group_meta['group_facebook']) ? $group_meta['group_facebook'] : '';
        $form['group_github'] = isset($group_meta['group_github']) ? $group_meta['group_github'] : '';
        $form['group_twitter'] = isset($group_meta['group_twitter']) ? $group_meta['group_twitter'] : '';
		$form['group_other'] = isset($group_meta['group_other']) ? $group_meta['group_other'] : '';
		$form['group_matrix'] = isset($group_meta['group_matrix']) ? $group_meta['group_matrix'] : '';


        if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
            $form['image_url'] = preg_replace("/^http:/i", "https:", $form['image_url']);
        } else {
            $form['image_url'] = $form['image_url'];
        }
    }

    $form_tags = isset($form['tags']) ? array_unique(array_filter($form['tags'], 'strlen')) : Array();
?>
<div class="content">
    <?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['done']) && $_POST['done'] == true): ?>
    <script type="text/javascript">
        window.location = '/groups/<?php print $group->slug; ?>';
    </script>
    <?php else: ?>
    <div class="create-group">
        <div class="create-group__hero">
            <div class="create-group__hero-container">
                <h1 class="create-group__title"><?php _e('Edit Group', 'community-portal'); ?></h1>
            </div>
        </div>
        <form action="/groups/<?php print $group->slug; ?>/admin/edit-details/" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>

        <div class="create-group__container">
            <ol class="create-group__menu">
                <li class="create-group__menu-item create-group__menu-item--disabled"><a href="#" class="create-group__menu-link"><?php _e('Basic Information', 'community-portal'); ?></a></li>
            </ol>
            <div class="create-group__menu create-group__menu--mobile">
                <div class="create-group__select-container">
                    <select id="create-group-mobile-nav" class="create-group__select" name="mobile_nav">
                        <option value="1" selected><?php _e('Basic Information', 'community-portal'); ?></option>
                    </select>
                </div>
            </div>
            <?php do_action('bp_before_create_group_content_template'); ?>
            <?php wp_nonce_field('protect_content', 'my_nonce_field'); ?>
            <section class="create-group__details">
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--60">
                        <label class="create-group__label" for="group-name"><?php _e('What is your group\'s name? *', 'community-portal'); ?></label>
                        <input type="text" name="group_name" id="group-name" class="create-group__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> create-group__input--error<?php endif; ?>" value="<?php print isset($form['group_name']) ? $form['group_name'] : ''; ?>" required />
                        <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php _e('This field is required', 'community-portal'); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--40 create-group__input-container--flex">
                        <label class="create-group__label" for="group-type"><?php _e('Group Type', 'community-portal'); ?></label>
                        <div class="create-group__select-container">
                            <select id="group-type" class="create-group__select" name="group_type" required>
                                <option value="Online"<?php if(isset($form['group_type']) && $form['group_type'] == 'Online' || (empty($form['group_type']))): ?> selected<?php endif; ?>><?php _e('Online', 'community-portal'); ?></option>
                                <option value="Offline"<?php if(isset($form['group_type']) && $form['group_type'] == 'Offline'): ?> selected<?php endif; ?>><?php _e('Offline', 'community-portal'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-language"><?php _e('Language', 'community-portal'); ?></label>
                        <div class="create-group__select-container">
                            <select id="group-language" class="create-group__select" name="group_language">
                                <option value="0"><?php _e('Language', 'community-portal'); ?></option>
                                <?php foreach($languages AS $code =>  $language_name): ?>
                                <option value="<?php print $code; ?>"<?php if(isset($form['group_language']) && $form['group_language'] == $code): ?> selected<?php endif; ?>><?php print $language_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container  create-group__input-container--40 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-country"><?php _e('Group Location', 'community-portal'); ?></label>
                        <div class="create-group__select-container">
                            <select id="group-country" class="create-group__select" name="group_country">
                                <option value="0"><?php _e('Country', 'community-portal'); ?></option>
                                <?php foreach($countries AS $code => $country): ?>
                                <option value="<?php print $code; ?>"<?php if(isset($form['group_country']) && $form['group_country'] === $code): ?> selected<?php endif; ?>><?php print $country; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-city"><?php _e('City', 'community-portal'); ?></label>
                        <input type="text" name="group_city" id="group-city" class="create-group__input" placeholder="<?php _e('City', 'community-portal'); ?>" value="<?php print isset($form['group_city']) ? $form['group_city'] : ''; ?>" maxlength="180" />
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-desc"><?php _e('Description *', 'community-portal'); ?></label>
                        <textarea name="group_desc" id="group-desc" class="create-group__textarea<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> create-group__input--error<?php endif; ?>" required ><?php print isset($form['group_desc']) ? $form['group_desc'] : ''; ?></textarea>
                        <div class="form__error-container<?php if(!isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php _e('This field is required', 'community-portal'); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--40 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-desc"><?php _e('Group Photo', 'community-portal'); ?></label>
                        <div id="dropzone-photo-uploader" class="create-group__image-upload<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> create-group__image-upload--done<?php endif; ?>"<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> style="background-image: url('<?php print $form['image_url'];?>')"<?php endif; ?>>
							<div class="dz-message" data-dz-message="">
								<div class="create-group__image-instructions">
									<div class="form__error-container">
										<div class="form__error form__error--image"></div>
									</div>
									<button type="button" id="dropzone-trigger" class="dropzone__image-instructions create-group__image-instructions <?php echo (isset($form['image_url']) && strlen($form['image_url']) > 0 ? 'dropzone__image-instructions--hidden' : '' ) ?>">
										<?php _e('Click or drag a photo above', 'community-portal'); ?>
										<span><?php _e('min dimensions 703px by 400px', 'community-portal'); ?></span>
									</button>
								</div>
								<button type="button" class="dz-remove<?php if(!isset($form['image_url']) || strlen($form['image_url']) === 0): ?> dz-remove--hide<?php endif; ?>" data-dz-remove="" ><?php _e('Remove file', 'community-portal'); ?></button>
							</div>
						</div>
                        <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url']) && strlen($form['image_url']) > 0) ? $form['image_url'] : '' ?>" />
                    </div>
                </div>
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
						<fieldset class="fieldset">
							<legend class="create-group__label"><?php _e('Tags for your group', 'community-portal'); ?></legend>
							<?php 
								// Get all tags
								$tags = get_tags(array('hide_empty' => false));
							?>
							<div class="create-group__tag-container">
								<?php foreach($tags AS $tag): ?>
									<input class="create-group__checkbox" type="checkbox" id="<?php echo $tag->slug ?>" data-value="<?php print $tag->slug; ?>">
									<label class="create-group__tag<?php if(in_array($tag->slug, $form_tags)): ?> create-group__tag--active<?php endif; ?>" for="<?php echo $tag->slug ?>"><?php echo $tag->name ?></label>
								<?php endforeach; ?>
							</div>
							<input type="hidden" value="<?php print (isset($form['tags'])) ? implode(',', $form['tags']) : '' ?>" name="tags" id="tags" /> 
						</fieldset>
					</div>
				</div>
            </section>
            <section class="create-group__details">
                <div class="create-group__section-title"><?php _e('Group Meetings', 'community-portal'); ?></div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--40">    
                        <label class="create-group__label" for="group-address-type" ><?php _e('Where do you meet?', 'community-portal'); ?></label>
                        <div class="create-group__select-container">
                            <select class="create-group__select" name="group_address_type" id="group-address-type">
                                <option value="Address" <?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'Address'):?> selected<?php endif;?>><?php _e('Address', 'community-portal'); ?></option>
                                <option value="URL"; ?>"<?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'URL'):?> selected<?php endif;?>><?php _e('URL', 'community-portal'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-address" ><?php _e('Address', 'community-portal'); ?></label>
                        <input type="text" name="group_address" id="group-address" class="create-group__input" value="<?php print isset($form['group_address']) ? $form['group_address'] : ''; ?>" />
                    </div>
                </div>
                <div class="create-group__input-container create-group__input-container--full">
                    <label class="create-group__label" for="group-desc"><?php _e('Meeting details', 'community-portal'); ?></label>
                    <textarea name="group_meeting_details" id="group-meeting-details" class="create-group__textarea create-group__textarea--full create-group__textarea--short" ><?php print isset($form['group_meeting_details']) ? $form['group_meeting_details'] : ''; ?></textarea>
                </div>
            </section>
            <section class="create-group__details">
				<h2 class="create-group__section-title"><?php _e('Community Links', 'community-portal'); ?></h2>
                <div class="create-group__input-row create-group__subsection">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-discourse"><?php _e('Discourse', 'community-portal'); ?></label>
						<input placeholder="https://" type="text" name="group_discourse" id="group-discourse" class="create-group__input create-group__input--inline" value="<?php print isset($form['group_discourse']) ? $form['group_discourse'] : ''; ?>" />
                    </div>
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label"  for="group-matrix"><?php _e('Matrix', 'community-portal'); ?></label>
						<input type="text" placeholder="room-alias:domain" name="group_matrix" id="group-matrix" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_matrix']) ? $form['group_matrix'] : ''; ?>"/>
						<div class="form__error-container form__error-container--checkbox">
							<div class="form__error"><?php _e('Please format as room-alias:domain', 'community-portal'); ?></div>
						</div>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-facebook"><?php _e('Facebook', 'community-portal'); ?></label>
						<input placeholder="https://" type="text" name="group_facebook" id="group-facebook" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_facebook']) ? $form['group_facebook'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-twitter"><?php _e('Twitter', 'community-portal'); ?></label>
						<input placeholder="https://" type="text" name="group_twitter" id="group-twitter" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_twitter']) ? $form['group_twitter'] : ''; ?>"/>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-telegram"><?php _e('Telegram', 'community-portal'); ?></label>
						<input type="text" placeholder="https://" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_telegram']) ? $form['group_telegram'] : ''; ?>"/>
                    </div>
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-github"><?php _e('GitHub', 'community-portal'); ?></label>
						<input placeholder="https://" type="text" name="group_github" id="group-github" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_github']) ? $form['group_github'] : ''; ?>"/>
                    </div>
					
                </div>
				<div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label"  for="group-other"><?php _e('Other', 'community-portal'); ?></label>
						<input type="text" placeholder="https://" name="group_other" id="group-other" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_other']) ? $form['group_other'] : ''; ?>"/>
                    </div>
                </div>
            </section>
            <section class="create-group__cta-container">
                <input type="submit" class="create-group__cta" value="<?php _e('Continue', 'community-portal'); ?>" />
            </section>
        </form>
    </div>
<?php endif; ?>
</div>