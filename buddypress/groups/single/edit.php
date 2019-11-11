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
        $form['group_country'] = isset($group_meta['group_country']) ? $group_meta['group_country'] : '0';
        $form['group_city'] = isset($group_meta['group_city']) ? $group_meta['group_city'] : '';
        $form['image_url'] = isset($group_meta['group_image_url']) ? $group_meta['group_image_url'] : '';
        $form['tags'] = $group_meta['group_tags'];
        $form['group_address_type'] = isset($group_meta['group_address_type']) ? $group_meta['group_address_type'] : 'Address';
        $form['group_address'] = isset($group_meta['group_address']) ? $group_meta['group_address'] : '';
        $form['group_meeting_details'] = isset($group_meta['group_meeting_details']) ? $group_meta['group_meeting_details'] : '';
        $form['group_discourse'] = isset($group_meta['group_discourse']) ? $group_meta['group_discourse'] : '';
        $form['group_facebook'] = isset($group_meta['group_facebook']) ? $group_meta['group_facebook'] : '';
        $form['group_github'] = isset($group_meta['group_github']) ? $group_meta['group_github'] : '';
        $form['group_twitter'] = isset($group_meta['group_twitter']) ? $group_meta['group_twitter'] : '';
        $form['group_other'] = isset($group_meta['group_other']) ? $group_meta['group_other'] : '';

    }

    $form_tags = isset($form['tags']) ? array_filter($form['tags'], 'strlen') : Array();

?>
<div class="content">
    <div class="create-group">
        <div class="create-group__container">
            <h1 class="create-group__title"><?php print __("Edit Group"); ?></h1>
            <div class="create-group__required"><?php print __("* Optional Information"); ?></div>
            <?php do_action('bp_before_create_group_content_template'); ?>
            <form action="/groups/<?php print $group->slug; ?>/admin/edit-details/" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>
                <?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
                <?php do_action('bp_before_create_group'); ?>
                <section class="create-group__details">
                    <div class="create-group__input-container">
                        <label class="create-group__label" for="group-name"><?php print __("What is your group's name?"); ?></label>
                        <input type="text" name="group_name" id="group-name" class="create-group__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> create-group__input--error<?php endif; ?>" value="<?php print isset($form['group_name']) ? $form['group_name'] : ''; ?>" required />
                        <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) ) || isset($form['group_name_error'])): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php if(isset($form['group_name_error'])): ?><?php print $form['group_name_error']; ?><?php else: ?><?php print __("This field is required"); ?><?php endif; ?></div>
                        </div> 
                        <input type="hidden" id="current-group" value="<?php print $group->id; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--short">
                        <label class="create-group__label" for="group-desc"><?php print __("Online or Offline Group"); ?></label>
                        <label class="create-group__radio-container">
                            <?php print __("Online"); ?>
                            <input type="radio" name="group_type" id="group-type" value="<?php print __("Online"); ?>"<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_type']) && $form['group_type'] == 'Online' || (empty($form['group_type']) || $form['group_type'] == 'Online')): ?> checked<?php endif; ?> required />
                            <span class="create-group__radio"></span>
                        </label>
                        <label class="create-group__radio-container">
                            <?php print __("Offline"); ?>
                            <input type="radio" name="group_type" id="group-type" value="<?php print __("Offline"); ?>" <?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_type']) && $form['group_type'] == 'Offline' || $form['group_type'] === 'Offline'): ?> checked<?php endif; ?> required />
                            <span class="create-group__radio"></span>
                        </label>
                            <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_type'])): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php print __("This field is required"); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container  create-group__input-container--short">
                        <label class="create-group__label" for="group-country"><?php print __("Where is your group from ? *"); ?></label>
                        <div class="create-group__select-container">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                                </g>
                            </svg>
                            <select id="group-country" class="create-group__select<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_country']) || (isset($form['group_country']) && $form['group_country'] == "0" )): ?> create-group__input--error<?php endif; ?>" name="group_country">
                                <option value="0">Country</option>
                                <?php foreach($countries AS $code => $country): ?>
                                <option value="<?php print $code; ?>"<?php if(isset($form['group_country']) && $form['group_country'] === $code): ?> selected<?php endif; ?>><?php print __($country); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && $form['group_type'] === 'offline' && !isset($form['group_country']) || (isset($form['group_country']) && $form['group_country'] == "0" )): ?> form__error-container--visible<?php endif; ?>">
                                <div class="form__error"><?php print __("This field is required"); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="create-group__input-container">
                        <label class="create-group__label" for="group-city"><?php print __("City *"); ?></label>
                        <input type="text" name="group_city" id="group-city" class="create-group__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_city']) || (isset($form['group_city']) && empty(trim($form['group_city'])) )): ?> create-group__input--error<?php endif; ?>" placeholder="<?php print __("City"); ?>" value="<?php print isset($form['group_city']) ? $form['group_city'] : ''; ?>" />
                        <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && $form['group_type'] === 'offline' && !isset($form['group_city']) || (isset($form['group_city']) && empty(trim($form['group_city'])) )): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php print __("This field is required"); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container">
                        <label class="create-group__label" for="group-desc"><?php print __("Description"); ?></label>
                        <textarea name="group_desc" id="group-desc" class="create-group__textarea<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> create-group__input--error<?php endif; ?>" required ><?php print isset($form['group_desc']) ? $form['group_desc'] : ''; ?></textarea>
                        <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php print __("This field is required"); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--short">
                        <label class="create-group__label" for="group-desc"><?php print __("Group Photo"); ?></label>
                        <div id="group-photo-uploader" class="create-group__image-upload<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> create-group__image-upload--done<?php endif; ?>"<?php if(isset($form['image_url'])): ?> style="background-image: url('<?php print $form['image_url'];?>')"<?php endif; ?>>
                            <svg width="75" height="75" viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg" class="create-group__upload-image-svg<?php if(!isset($form['image_url']) || strlen($form['image_url']) === 0): ?> create-group__upload-image-svg--hide<?php endif; ?>">
                                <path d="M59.375 9.375H15.625C12.1732 9.375 9.375 12.1732 9.375 15.625V59.375C9.375 62.8268 12.1732 65.625 15.625 65.625H59.375C62.8268 65.625 65.625 62.8268 65.625 59.375V15.625C65.625 12.1732 62.8268 9.375 59.375 9.375Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M26.5625 31.25C29.1513 31.25 31.25 29.1513 31.25 26.5625C31.25 23.9737 29.1513 21.875 26.5625 21.875C23.9737 21.875 21.875 23.9737 21.875 26.5625C21.875 29.1513 23.9737 31.25 26.5625 31.25Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M65.625 46.875L50 31.25L15.625 65.625" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <a class="dz-remove<?php if(!isset($form['image_url']) || strlen($form['image_url']) === 0): ?> dz-remove--hide<?php endif; ?>" href="#" data-dz-remove="" >Remove file</a>
                        <div class="create-group__image-instructions<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> create-group__image-instructions--hide<?php endif; ?>"><?php print __("Click or drag a photo above"); ?></div>
                        <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url'])) ? $form['image_url'] : '' ?>" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--full">
                        <label class="create-group__label"><?php print __("Tags for your group"); ?></label>
                        <?php 
                            // Get all tags
                            $tags = get_tags(array('hide_empty' => false));
                        ?>
                        <div class="create-group__tag-container">
                            <?php foreach($tags AS $tag): ?>
                            <a href="#" class="create-group__tag<?php if(in_array($tag->slug, $form_tags)): ?> create-group__tag--active<?php endif; ?>" data-value="<?php print __($tag->slug); ?>"> <?php print __($tag->name); ?></a>
                            <?php endforeach; ?>
                            <input type="hidden" value="<?php print (isset($form['tags'])) ? join(',', $form['tags']) : '' ?>" name="tags" id="tags" /> 
                        </div>
                    </div>
                </section>
                <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
                    <div class="create-group__input-container  create-group__input-container--short">
                        <label class="create-group__label"><?php print __("Group Meetings *"); ?></label>
                        <div class="create-group__select-container">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                                </g>
                            </svg>
                            <select class="create-group__select" name="group_address_type">
                                <option value="<?php print __("Address"); ?>" <?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'Address'):?> selected<?php endif;?>><?php print __("Address"); ?></option>
                                <option value="<?php print __("URL"); ?>"<?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'URL'):?> selected<?php endif;?>><?php print __("URL"); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="create-group__input-container">
                        <input type="text" name="group_address" id="group-discourse" class="create-group__input create-group__input--inline" value="<?php print isset($form['group_address']) ? $form['group_address'] : ''; ?>" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--full">
                        <label class="create-group__label" for="group-desc"><?php print __("Meeting details"); ?></label>
                        <textarea name="group_meeting_details" id="group-meeting-details" class="create-group__textarea create-group__textarea--full create-group__textarea--short" ><?php print isset($form['group_meeting_details']) ? $form['group_meeting_details'] : ''; ?></textarea>
                    </div>
                </section>
                <section class="create-group__details">
                    <div class="create-group__input-container">
                        <label class="create-group__label"><?php print __("Community Links *"); ?></label>
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline" for="group-discourse"><?php print __("Discourse"); ?></label><input type="text" name="group_discourse" id="group-discourse" class="create-group__input create-group__input--inline" value="<?php print isset($form['group_discourse']) ? $form['group_discourse'] : ''; ?>" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline" for="group-github"><?php print __("GitHub"); ?></label><input type="text" name="group_github" id="group-github" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_github']) ? $form['group_github'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline" for="group-facebook"><?php print __("Facebook"); ?></label><input type="text" name="group_facebook" id="group-facebook" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_facebook']) ? $form['group_facebook'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline" for="group-twitter"><?php print __("Twitter"); ?></label><input type="text" name="group_twitter" id="group-twitter" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_twitter']) ? $form['group_twitter'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline" for="group-telegram"><?php print __("Telegram"); ?></label><input type="text" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_telegram']) ? $form['group_telegram'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--inline">
                        <label class="create-group__label create-group__label--inline"  for="group-other"><?php print __("Other"); ?></label><input type="text" name="group_other" id="group-other" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_other']) ? $form['group_other'] : ''; ?>"/>
                    </div>
                </section>
                <section class="create-group__cta-container">
                    <input type="submit" class="create-group__cta" value="<?php print strtoupper(__("Continue")); ?>" />
                </section>
            </form>
        </div>
    </div>
</div>