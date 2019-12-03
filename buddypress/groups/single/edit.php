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
        $form['group_discourse_url'] = isset($group_meta['discourse_category_url']) ? $group_meta['discourse_category_url'] : '';
        $form['group_discourse_id'] = isset($group_meta['discourse_category_id']) ? $group_meta['discourse_category_id'] : '';


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
                <h1 class="create-group__title"><?php print __("Edit Group"); ?></h1>
            </div>
        </div>
        <form action="/groups/<?php print $group->slug; ?>/admin/edit-details/" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="group_discourse_url" value="<?php print $form['group_discourse_url']; ?>" />
        <input type="hidden" name="group_discourse_id" value="<?php print $form['group_discourse_id']; ?>" />
        <div class="create-group__container">
            <ol class="create-group__menu">
                <li class="create-group__menu-item create-group__menu-item--disabled"><a href="#" class="create-group__menu-link"><?php print __("Basic Information"); ?></a></li>
                <li class="create-group__menu-item create-group__menu-item--disabled create-group__menu-item--right"><?php print __("* Optional Information"); ?></li>
            </ol>
            <div class="create-group__menu create-group__menu--mobile">
                <div class="create-group__select-container">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="create-group-mobile-nav" class="create-group__select" name="mobile_nav">
                        <option value="1" selected><?php print __("Basic Information"); ?></option>
                    </select>
                </div>
            </div>
            <?php do_action('bp_before_create_group_content_template'); ?>
            <?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
            <section class="create-group__details">
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--60">
                        <label class="create-group__label" for="group-name"><?php print __("What is your group's name?"); ?></label>
                        <input type="text" name="group_name" id="group-name" class="create-group__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> create-group__input--error<?php endif; ?>" value="<?php print isset($form['group_name']) ? $form['group_name'] : ''; ?>" required />
                        <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php print __("This field is required"); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--40 create-group__input-container--flex">
                        <label class="create-group__label create-group__label--full-width" for="group-desc"><?php print __("Online or Offline Group"); ?></label>
                        <label class="create-group__radio-container">
                            <?php print __("Online"); ?>
                            <input type="radio" name="group_type" id="group-type" value="<?php print __("Online"); ?>"<?php if(isset($form['group_type']) && $form['group_type'] == 'Online' || (empty($form['group_type']))): ?> checked<?php endif; ?> required />
                            <span class="create-group__radio"></span>
                        </label>
                        <label class="create-group__radio-container create-group__radio-container--second">
                            <?php print __("Offline"); ?>
                            <input type="radio" name="group_type" id="group-type" value="<?php print __("Offline"); ?>" <?php if(isset($form['group_type']) && $form['group_type'] == 'Offline'): ?> checked<?php endif; ?> required />
                            <span class="create-group__radio"></span>
                        </label>
                            <div class="form__error-container<?php if(!isset($form['group_type'])): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php print __("This field is required"); ?></div>
                        </div>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container  create-group__input-container--40 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-country"><?php print __("Group Location *"); ?></label>
                        <div class="create-group__select-container">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                                </g>
                            </svg>
                            <select id="group-country" class="create-group__select" name="group_country">
                                <option value="0">Country</option>
                                <?php foreach($countries AS $code => $country): ?>
                                <option value="<?php print $code; ?>"<?php if(isset($form['group_country']) && $form['group_country'] === $code): ?> selected<?php endif; ?>><?php print __($country); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-city"><?php print __("City *"); ?></label>
                        <input type="text" name="group_city" id="group-city" class="create-group__input" placeholder="<?php print __("City"); ?>" value="<?php print isset($form['group_city']) ? $form['group_city'] : ''; ?>" maxlength="180" />
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-desc"><?php print __("Description"); ?></label>
                        <textarea name="group_desc" id="group-desc" class="create-group__textarea<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> create-group__input--error<?php endif; ?>" required ><?php print isset($form['group_desc']) ? $form['group_desc'] : ''; ?></textarea>
                        <div class="form__error-container<?php if(!isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> form__error-container--visible<?php endif; ?>">
                            <div class="form__error"><?php print __("This field is required"); ?></div>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--40 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-desc"><?php print __("Group Photo"); ?></label>
                        <div id="group-photo-uploader" class="create-group__image-upload<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> create-group__image-upload--done<?php endif; ?>"<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> style="background-image: url('<?php print $form['image_url'];?>')"<?php endif; ?>>
                        
                        </div>
                        <a class="dz-remove<?php if(!isset($form['image_url']) || strlen($form['image_url']) === 0): ?> dz-remove--hide<?php endif; ?>" href="#" data-dz-remove="" >Remove file</a>
                            <div class="create-group__image-instructions<?php if(isset($form['image_url']) && strlen($form['image_url']) > 0): ?> create-group__image-instructions--hide<?php endif;?>">
                            <?php print __("Click or drag a photo above"); ?>
                            <span><?php print __('min dimensions 703px by 400px'); ?></span>
                            <div class="form__error-container">
                                <div class="form__error form__error--image"></div>
                            </div>
                        </div>
                        <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url']) && strlen($form['image_url']) > 0) ? $form['image_url'] : '' ?>" />
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
                        <label class="create-group__label"><?php print __("Tags for your group"); ?></label>
                        <?php 
                            // Get all tags
                            $tags = get_tags(array('hide_empty' => false));
                        ?>
                        <div class="create-group__tag-container">
                            <?php foreach($tags AS $tag): ?>
                            <a href="#" class="create-group__tag<?php if(in_array($tag->slug, $form_tags)): ?> create-group__tag--active<?php endif; ?>" data-value="<?php print __($tag->slug); ?>"> <?php print __($tag->name); ?></a>
                            <?php endforeach; ?>
                            <input type="hidden" value="<?php print (isset($form['tags'])) ? implode(',', $form['tags']) : '' ?>" name="tags" id="tags" /> 
                        </div>
                    </div>
                </div>
            </section>
            <section class="create-group__details">
                <div class="create-group__section-title"><?php print __("Group Meetings *"); ?></div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--40">    
                        <label class="create-group__label" for="group-address-type" ><?php print __("Where do you meet?"); ?></label>
                        <div class="create-group__select-container">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                                </g>
                            </svg>
                            <select class="create-group__select" name="group_address_type" id="group-address-type">
                                <option value="<?php print __("Address"); ?>" <?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'Address'):?> selected<?php endif;?>><?php print __("Address"); ?></option>
                                <option value="<?php print __("URL"); ?>"<?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'URL'):?> selected<?php endif;?>><?php print __("URL"); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                        <label class="create-group__label" for="group-address" ><?php print __("Address"); ?></label>
                        <input type="text" name="group_address" id="group-address" class="create-group__input" value="<?php print isset($form['group_address']) ? $form['group_address'] : ''; ?>" />
                    </div>
                </div>
                <div class="create-group__input-container create-group__input-container--full">
                    <label class="create-group__label" for="group-desc"><?php print __("Meeting details"); ?></label>
                    <textarea name="group_meeting_details" id="group-meeting-details" class="create-group__textarea create-group__textarea--full create-group__textarea--short" ><?php print isset($form['group_meeting_details']) ? $form['group_meeting_details'] : ''; ?></textarea>
                </div>
            </section>
            <section class="create-group__details">
                <div class="create-group__section-title"><?php print __("Community Links *"); ?></div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
                        <label class="create-group__label" for="group-discourse"><?php print __("Discourse"); ?></label><input type="text" name="group_discourse" id="group-discourse" class="create-group__input create-group__input--inline" value="<?php print isset($form['group_discourse']) ? $form['group_discourse'] : ''; ?>" />
                    </div>
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
                        <label class="create-group__label" for="group-github"><?php print __("GitHub"); ?></label><input type="text" name="group_github" id="group-github" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_github']) ? $form['group_github'] : ''; ?>"/>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
                        <label class="create-group__label" for="group-facebook"><?php print __("Facebook"); ?></label><input type="text" name="group_facebook" id="group-facebook" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_facebook']) ? $form['group_facebook'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
                        <label class="create-group__label" for="group-twitter"><?php print __("Twitter"); ?></label><input type="text" name="group_twitter" id="group-twitter" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_twitter']) ? $form['group_twitter'] : ''; ?>"/>
                    </div>
                </div>
                <div class="create-group__input-row">
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
                        <label class="create-group__label" for="group-telegram"><?php print __("Telegram"); ?></label><input type="text" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_telegram']) ? $form['group_telegram'] : ''; ?>"/>
                    </div>
                    <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
                        <label class="create-group__label"  for="group-other"><?php print __("Other"); ?></label><input type="text" name="group_other" id="group-other" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_other']) ? $form['group_other'] : ''; ?>"/>
                    </div>
                </div>
            </section>
            <section class="create-group__cta-container">
                <input type="submit" class="create-group__cta" value="<?php print __("Continue"); ?>" />
            </section>
        </form>
    </div>
<?php endif; ?>
</div>