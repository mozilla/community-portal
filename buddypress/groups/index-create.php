<?php
    session_start();
    do_action('bp_before_create_group_page');

    $user = wp_get_current_user();
    $meta = get_user_meta($user->ID);

    if(!isset($meta['agree'][0]) || $meta['agree'][0] != 'I Agree') {
        wp_redirect("/people/{$user->user_nicename}/profile/edit/group/1/");
        die();
    }
    
    if(isset($_POST['step'])) {
        $step = trim($_POST['step']);
    }

    if($step == 3) {
        wp_redirect("/groups/{$_POST['group_slug']}");
        die();
    }

    // Main header template 
    get_header(); 

    $template_dir = get_template_directory();
    include("{$template_dir}/countries.php");
    include("{$template_dir}/languages.php");

    if(isset($_SESSION['form'])) {
        $form = $_SESSION['form'];
    }
    
    $form_tags = isset($form['tags']) ? array_filter(explode(',', $form['tags']), 'strlen') : Array();  
?>
<div class="content">
    <div class="create-group">
        <?php if($step != 3): ?>
        <div class="create-group__hero">
            <div class="create-group__hero-container">
                <h1 class="create-group__title"><?php print __("Create a Mozilla Group", "community-portal"); ?></h1>
            </div>
        </div>
        <form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>
            <div class="create-group__container">
                <ol class="create-group__menu">
                    <li class="create-group__menu-item<?php if($step == 1): ?> create-group__menu-item--disabled<?php endif;?>"><a href="#" class="create-group__menu-link<?php if($step == 1): ?> create-group__menu-link--disabled<?php endif; ?>" data-step=""><?php print __("Basic Information", "community-portal"); ?></a></li>
                    <li class="create-group__menu-item<?php if($step != 1): ?> create-group__menu-item--disabled<?php endif;?>"><a href="#" class="create-group__menu-link<?php if($step != 1): ?> create-group__menu-link--disabled<?php endif; ?>" data-step=""><?php print __("Terms & Responsibilities", "community-portal"); ?></a></li>
                </ol>
                <div class="create-group__menu create-group__menu--mobile">
                    <div class="create-group__select-container">
                        <select id="create-group-mobile-nav" class="create-group__select" name="mobile_nav">
                            <option value="1"<?php if($step != 1): ?> selected<?php endif; ?>><?php print __("Basic Information", "community-portal"); ?></option>
                            <option value="2"<?php if($step == 1): ?> selected<?php endif; ?>><?php print __("Terms & Responsibilities", "community-portal"); ?></option>
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
                                <label class="create-group__label" for="group-name"><?php print __("What is your group's name? *"); ?></label>
                                <input type="text" name="group_name" id="group-name" class="create-group__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> create-group__input--error<?php endif; ?>" value="<?php print isset($form['group_name']) ? $form['group_name'] : ''; ?>" required />
                                <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_name']) || (isset($form['group_name']) && empty(trim($form['group_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                                    <div class="form__error"><?php print __("This field is required", "community-portal"); ?></div>
                                </div>
                            </div>
                            <div class="create-group__input-container create-group__input-container--40">
                                <label class="create-group__label" for="group-type"><?php print __("Group Type", "community-portal"); ?></label>
                                <div class="create-group__select-container">
                                    <select id="group-type" class="create-group__select" name="group_type" required>
                                        <option value="Online"<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_type']) && $form['group_type'] == 'Online' || (empty($form['group_type']))): ?> selected<?php endif; ?>><?php print __("Online", "community-portal"); ?></option>
                                        <option value="Offline"<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_type']) && $form['group_type'] == 'Offline'): ?> selected<?php endif; ?>><?php print __("Offline", "community-portal"); ?></option>
                                    </select>
                                </div>
							</div>
                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
                                <label class="create-group__label" for="group-language"><?php print __("Language", "community-portal"); ?></label>
                                <div class="create-group__select-container">
                                    <select id="group-language" class="create-group__select" name="group_language">
                                        <option value="0"><?php print __("Language", "community-portal"); ?></option>
                                        <?php foreach($languages AS $code =>  $language_name): ?>
                                        <option value="<?php print $code; ?>"<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($form['group_language']) && $form['group_language'] == $code): ?> selected<?php endif; ?>><?php print $language_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container  create-group__input-container--40 create-group__input-container--vertical-spacing">
                                <label class="create-group__label" for="group-country"><?php print __("Group Location", "community-portal"); ?></label>
                                <div class="create-group__select-container">
                                    <select id="group-country" class="create-group__select" name="group_country">
                                        <option value="0"><?php print __("Country", "community-portal"); ?></option>
                                        <?php foreach($countries AS $code => $country): ?>
                                        <option value="<?php print $code; ?>"<?php if(isset($form['group_country']) && $form['group_country'] === $code): ?> selected<?php endif; ?>><?php print __($country); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                                <label class="create-group__label" for="group-city"><?php print __("City", "community-portal"); ?></label>
                                <input type="text" name="group_city" id="group-city" class="create-group__input" placeholder="<?php print __("City"); ?>" value="<?php print isset($form['group_city']) ? $form['group_city'] : ''; ?>" maxlength="180" />
                            </div>
                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                                <label class="create-group__label" for="group-desc"><?php print __("Provide a short group description *", "community-portal"); ?></label>
                                <textarea name="group_desc" id="group-desc" class="create-group__textarea<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> create-group__input--error<?php endif; ?>" required maxlength="3000"><?php print isset($form['group_desc']) ? $form['group_desc'] : ''; ?></textarea>
                                <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_desc']) || (isset($form['group_desc']) && empty(trim($form['group_desc'])) )): ?> form__error-container--visible<?php endif; ?>">
                                    <div class="form__error"><?php print __("This field is required", "community-portal"); ?></div>
                                </div>
                            </div>
                            <div class="create-group__input-container create-group__input-container--40 create-group__input-container--vertical-spacing">
                                <label class="create-group__label"><?php print __("Select an image", "community-portal"); ?></label>
                                <div id="dropzone-photo-uploader" class="create-group__image-upload">
									<div class="dz-message" data-dz-message="">
										<div>
											<div class="form__error-container">
												<div class="form__error form__error--image"></div>
											</div>
											<button type="button" id="dropzone-trigger" class="dropzone__image-instructions create-group__image-instructions">
												<?php print __("Click or drag a photo above", "community-portal"); ?>
												<span><?php print __('min dimensions 703px by 400px', "community-portal"); ?></span>
											</button>
										</div>
										<button type="button" class="dz-remove<?php if(!isset($form['image_url']) || strlen($form['image_url']) === 0): ?> dz-remove--hide<?php endif; ?>" data-dz-remove="" >Remove file</button>
									</div>
                                </div>
                                <input type="hidden" name="image_url" id="image-url" value="<?php print (isset($form['image_url'])) ? $form['image_url'] : '' ?>" />
                            </div>
                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
								<fieldset class="fieldset">
									<legend class="create-group__label"><?php print __("Tags for your group", "community-portal"); ?></legend>
									<?php 
										// Get all tags
										$tags = get_tags(array('hide_empty' => false));
									?>
									<div class="create-group__tag-container">
										<?php foreach($tags AS $tag): ?>
											<input class="create-group__checkbox" type="checkbox" id="<?php echo $tag->slug ?>" data-value="<?php print __($tag->slug); ?>">
											<label class="create-group__tag<?php if(in_array($tag->slug, $form_tags)): ?> create-group__tag--active<?php endif; ?>" for="<?php echo $tag->slug ?>"><?php echo $tag->name ?></label>
										<?php endforeach; ?>
									</div>
									<input type="hidden" value="<?php print (isset($form['tags'])) ? $form['tags'] : '' ?>" name="tags" id="tags" /> 
								</fieldset>
                            </div>
                        </div>
                    </section>
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
                        <div class="create-group__section-title"><?php print __("Group Meetings", "community-portal"); ?></div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--40">    
                                <label class="create-group__label" for="group-address-type" ><?php print __("Where do you meet?", "community-portal"); ?></label>
                                <div class="create-group__select-container">
                                    <select class="create-group__select" name="group_address_type" id="group-address-type">
                                        <option value="<?php print __("Address", "community-portal"); ?>" <?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'Address'):?> selected<?php endif;?>><?php print __("Address", "community-portal"); ?></option>
                                        <option value="<?php print __("URL", "community-portal"); ?>"<?php if(isset($form['group_address_type']) && $form['group_address_type'] == 'URL'):?> selected<?php endif;?>><?php print __("URL", "community-portal"); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
                                <label class="create-group__label" for="group-address" ><?php print __("Address", "community-portal"); ?></label>
                                <input type="text" name="group_address" id="group-address" class="create-group__input" value="<?php print isset($form['group_address']) ? $form['group_address'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="create-group__input-container create-group__input-container--full">
                            <label class="create-group__label" for="group-desc"><?php print __("Meeting details", "community-portal"); ?></label>
                            <textarea name="group_meeting_details" id="group-meeting-details" class="create-group__textarea create-group__textarea--full create-group__textarea--short" ><?php print isset($form['group_meeting_details']) ? $form['group_meeting_details'] : ''; ?></textarea>
                        </div>
                    </section>
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
						<h2 class="create-group__section-title"><?php print __("Community Links", "community-portal"); ?></h2>
                        <div class="create-group__input-row create-group__subsection">
                            <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label" for="group-discourse"><?php print __("Discourse", "community-portal"); ?></label>
								<input type="text" name="group_discourse" id="group-discourse" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link" value="<?php print isset($form['group_discourse']) ? $form['group_discourse'] : ''; ?>" />
                            </div>
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label"  for="group-matrix"><?php print __("Matrix", "community-portal"); ?></label>
								<input type="text" placeholder="username:domain" name="group_matrix" id="group-matrix" class="create-group__input 	create-group__input--inline"  value="<?php print isset($form['group_matrix']) ? $form['group_matrix'] : ''; ?>"/>
								<div class="form__error-container form__error-container--checkbox">
									<div class="form__error"><?php print __("Please format as username:domain", "community-portal"); ?></div>
								</div>
							</div>
                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label" for="group-facebook"><?php print __("Facebook", "community-portal"); ?></label>
								<input type="text" name="group_facebook" id="group-facebook" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link"  value="<?php print isset($form['group_facebook']) ? $form['group_facebook'] : ''; ?>"/>
                            </div>
                            <div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label" for="group-twitter"><?php print __("Twitter", "community-portal"); ?></label>
								<input type="text" name="group_twitter" id="group-twitter" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link"  value="<?php print isset($form['group_twitter']) ? $form['group_twitter'] : ''; ?>"/>
                            </div>
                        </div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
							<label class="create-group__label" for="group-telegram"><?php print __("Telegram", "community-portal"); ?></label>
							<input type="text" placeholder="https://" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_telegram']) ? $form['group_telegram'] : ''; ?>"/>
						</div>
						<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
							<label class="create-group__label" for="group-github"><?php print __("GitHub", "community-portal"); ?></label>
							<input type="text" name="group_github" id="group-github" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link"  value="<?php print isset($form['group_github']) ? $form['group_github'] : ''; ?>"/>
						</div>

					</div>
					<div class="create-group__input-row">
						<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
							<label class="create-group__label"  for="group-other"><?php print __("Other", "community-portal"); ?></label>
							<input type="text" placeholder="https://" name="group_other" id="group-other" class="create-group__input create-group__input--inline"  value="<?php print isset($form['group_other']) ? $form['group_other'] : ''; ?>"/>
						</div>
					</div>
                    </section>
                    <section class="create-group__details<?php if($step == 1): ?> create-group__details--hidden<?php endif; ?>">
                        <div class="create-group__section-title">
                            <?php print __("Secondary Group Contact", "community-portal"); ?>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <title>Secondary group contact</title>
                                <path d="M9 16.5C13.1421 16.5 16.5 13.1421 16.5 9C16.5 4.85786 13.1421 1.5 9 1.5C4.85786 1.5 1.5 4.85786 1.5 9C1.5 13.1421 4.85786 16.5 9 16.5Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 6V9" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="9" cy="12" r="0.75" fill="#CDCDD4"/>
                            </svg>

                        </div>
                        <div class="create-group__input-row">
                            <div class="create-group__input-container create-group__input-container--full">
                                <label for="group-admin" class="create-group__label"><?php print __("Username *", "community-portal"); ?></label>
                                <input type="text" name="group_admin" id="group-admin" class="create-group__input" value="<?php print isset($form['group_admin']) ? $form['group_admin'] : ''; ?>" placeholder="Username" required/>
                                <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['group_admin_id']) || (isset($form['group_admin_id']) && empty(trim($form['group_admin_id'])) )): ?> form__error-container--visible<?php endif; ?>">
                                    <div class="form__error"><?php print __("This field is required", "community-portal"); ?></div>
                                </div>
                                <input type="hidden" name="group_admin_id" id="group-admin-id" value="<?php print isset($form['group_admin_id']) ? $form['group_admin_id'] : ''; ?>" required/>
                            </div>
                        </div>
                    </section>
                    <?php if($step == 1): ?>
                    <section class="create-group__details">
                        <?php
                            $category_id = get_cat_ID('Group Terms of Service', "community-portal");
                            $terms_of_service_posts = get_posts(Array(
                                'numberposts'   =>  1,
                                'category'      =>  $category_id
                            ));
                            
                        ?>
                        <?php if(sizeof($terms_of_service_posts) == 1): ?>
                        <div class="create-group__terms">
                            <?php print apply_filters('the_content', $terms_of_service_posts[0]->post_content); ?> 
                        </div>
                        <div class="create-group__input-container create-group__input-container--full cpg">
							<input class="checkbox--hidden" type="checkbox" name="agree" id="agree" value="<?php print __("I Agree", "community-portal"); ?>" required />
                            <label class="cpg__label" for="agree">
								<?php print __("I agree to respect and adhere to", "community-portal"); ?>
								<a class="create-group__checkbox-container__link" href="https://www.mozilla.org/en-US/about/governance/policies/participation/"><?php print __("Mozilla’s Community Participation Guidelines *", "community-portal") ?></a>
                                <div class="form__error-container form__error-container--checkbox">
                                    <div class="form__error"><?php print __("This field is required", "community-portal"); ?></div>
                                </div>
                            </label>
                        </div>
                        <?php endif; ?>
                        <input type="hidden" name="step" value="2" />
                    </section>
                    <?php endif; ?>
                    <section class="create-group__cta-container">
                        <input type="submit" class="create-group__cta" value="<?php print __("Continue", "community-portal"); ?>" />
                    </section>
                
                <?php endif; ?>      
            </div>
        </form>
        
    </div>
</div>
<?php 
    do_action('bp_after_create_group_page');
    get_footer();
?>