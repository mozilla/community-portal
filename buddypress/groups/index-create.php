<?php
    session_start();
    // Main header template 
    get_header(); 

    $template_dir = get_template_directory();
    include("{$template_dir}/countries.php");

    do_action('bp_before_create_group_page'); 
    if(isset($_POST['step'])) {
        $step = $_POST['step'];
    }
    if(isset($_SESSION['form'])) {
        $form = $_SESSION['form'];
    }
    $form_tags = isset($form['tags']) ? array_filter(explode(',', $form['tags']), 'strlen') : Array();
?>
<div class="content">
    <div class="create-group">
        <?php if($step !== 3): ?>
        <div class="create-group__hero">
            <div class="create-group__hero-container">
                <h1 class="create-group__title"><?php print __("Create a Mozilla Group"); ?></h1>
            </div>
        </div>
        <form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>
            <div class="create-group__container">
                <ol class="create-group__menu">
                    <li class="create-group__menu-item<?php if($step == 1): ?> create-group__menu-item--disabled<?php endif;?>"><a href="#" class="create-group__menu-link<?php if($step == 1): ?> create-group__menu-link--disabled<?php endif; ?>" data-step=""><?php print __("Basic Information"); ?></a></li>
                    <li class="create-group__menu-item<?php if($step != 1): ?> create-group__menu-item--disabled<?php endif;?>"><a href="#" class="create-group__menu-link<?php if($step != 1): ?> create-group__menu-link--disabled<?php endif; ?>" data-step=""><?php print __("Terms & Responsibilities"); ?></a></li>
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
                            <option value="1"<?php if($step != 1): ?> selected<?php endif; ?>><?php print __("Basic Information"); ?></option>
                            <option value="2"<?php if($step == 1): ?> selected<?php endif; ?>><?php print __("Terms & Responsibilities"); ?></option>
                        </select>
                    </div>
                </div>
                <?php do_action('bp_before_create_group_content_template'); ?>
            
                    <?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
                    <?php do_action('bp_before_create_group'); ?>
                    <input type="hidden" name="step" value="1" />
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
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
                                    <input type="radio" name="group_type" id="group-type" value="<?php print __("Online"); ?>"<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_type']) && $form['group_type'] == 'Online' || (empty($form['group_type']))): ?> checked<?php endif; ?> required />
                                    <span class="create-group__radio"></span>
                                </label>
                                <label class="create-group__radio-container create-group__radio-container--second">
                                    <?php print __("Offline"); ?>
                                    <input type="radio" name="group_type" id="group-type" value="<?php print __("Offline"); ?>" <?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_type']) && $form['group_type'] == 'Offline'): ?> checked<?php endif; ?> required />
                                    <span class="create-group__radio"></span>
                                </label>
                                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_type'])): ?> form__error-container--visible<?php endif; ?>">
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
                                <label class="create-group__label" for="group-desc"><?php print __("Provide a short group description"); ?></label>
                                <textarea name="group_desc" id="group-desc" class="create-group__textarea<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> create-group__input--error<?php endif; ?>" required ><?php print isset($form['group_desc']) ? $form['group_desc'] : ''; ?></textarea>
                                <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> form__error-container--visible<?php endif; ?>">
                                    <div class="form__error"><?php print __("This field is required"); ?></div>
                                </div>
                            </div>
                            <div class="create-group__input-container create-group__input-container--40 create-group__input-container--vertical-spacing">
                                <label class="create-group__label" for="group-desc"><?php print __("Select an image *"); ?></label>
                                <div id="group-photo-uploader" class="create-group__image-upload">
                                   
                                </div>
                                <a class="dz-remove<?php if(!isset($form['image_url']) || strlen($form['image_url']) === 0): ?> dz-remove--hide<?php endif; ?>" href="#" data-dz-remove="" >Remove file</a>
                                <div class="create-group__image-instructions">
                                    <?php print __("Click or drag a photo above"); ?>
                                    <span><?php print __('min dimensions 703px by 400px'); ?></span>
                                    <div class="form__error-container">
                                        <div class="form__error form__error--image"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url'])) ? $form['image_url'] : '' ?>" />
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
                                    <input type="hidden" value="<?php print (isset($form['tags'])) ? $form['tags'] : '' ?>" name="tags" id="tags" /> 
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
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
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
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
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
                        <div class="create-group__section-title">
                            <?php print __("Secondary Group Contact *"); ?>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <title>Secondary group contact</title>
                                <path d="M9 16.5C13.1421 16.5 16.5 13.1421 16.5 9C16.5 4.85786 13.1421 1.5 9 1.5C4.85786 1.5 1.5 4.85786 1.5 9C1.5 13.1421 4.85786 16.5 9 16.5Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 6V9" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="9" cy="12" r="0.75" fill="#CDCDD4"/>
                            </svg>

                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--full">
                                <label class="create-group__label"><?php print __("Username"); ?></label>
                                <input type="text" name="group_admin" id="group-admin" class="create-group__input" value="<?php print isset($form['group_admin']) ? $form['group_admin'] : ''; ?>" placeholder="@Username" />
                                <input type="hidden" name="group_admin_id" id="group-admin-id" value="<?php print isset($form['group_admin_id']) ? $form['group_admin_id'] : ''; ?>" />
                            </div>
                        </div>
                    </section>
                    <?php if($step == 1): ?>
                    <section class="create-group__details">
                        <?php
                            $category_id = get_cat_ID('Group Terms of Service');
                            $terms_of_service_posts = get_posts(Array(
                                'numberposts'   =>  1,
                                'category'      =>  $category_id
                            ));
                            
                        ?>
                        <?php if(sizeof($terms_of_service_posts) === 1): ?>
                        <div class="create-group__terms">
                            <?php print apply_filters('the_content', $terms_of_service_posts[0]->post_content); ?> 
                        </div>
                        <div class="create-group__input-container create-group__input-container--full">
                            <label class="create-group__checkbox-container" for="agree">
                                <?php print __("I agree to respect and adhere to Mozilla’s Community Participation Guidelines"); ?>
                                <input type="checkbox" name="agree" id="agree" value="<?php print __("I Agree"); ?>" required />
                                <div class="form__error-container form__error-container--checkbox">
                                    <div class="form__error"><?php print __("This field is required"); ?></div>
                                </div>
                                <span class="create-group__check">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                            </label>
                        </div>
                        <?php endif; ?>
                        <input type="hidden" name="step" value="2" />
                    </section>
                    <?php endif; ?>
                    <section class="create-group__cta-container">
                        <input type="submit" class="create-group__cta" value="<?php print __("Continue"); ?>" />
                    </section>
                
                <?php endif; ?>
                <?php if($step === 3): ?>
                    <script type="text/javascript">
                        jQuery(function(){
                            window.location = "/groups/<?php print $_POST['group_slug']; ?>";
                        });
                    </script>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php 
    do_action('bp_after_create_group_page');
    get_footer();
?>