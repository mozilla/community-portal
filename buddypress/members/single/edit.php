<?php if($complete === true): ?>
    <div class="profile__container">
        <section class="profile__success-message-container"> 
            <h1 class="profile__title"><?php print __('Profile Created'); ?></h1>
            <p class="profile__success-message">
                <?php 
                    print __('A message here congratulating the user for creating an account and reiterating the benefits of doing so, helping you to connect with other people who like Mozilla. Also an opportunity to encourage the user to put their new account to good use.');
                ?>
            </p>
            <div class="profile__button-container">
                <a href="/members/<?php print $updated_username ? $updated_username : $user->user_nicename; ?>/profile/edit/group/1/" class="profile__button"><?php print __('Complete your profile'); ?></a><a href="" class="profile__button profile__button--secondary"><?php print __('Go back to browsing'); ?></a>
            </div>
        </section>
    </div>
    <?php else: ?>
    <div class="profile__hero" style="background-image: url('<?php print get_stylesheet_directory_uri()."/images/mozilla-create-profile.png"; ?>');">
        <div class="profile__hero-container">
            <h1 class="profile__title"><?php print __("Complete Profile"); ?></h1>
            <p class="profile__hero-copy">
                <?php print __("Here’s a brief explanation on privacy settings. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus massa mauris, interdum vitae convallis."); ?>
            </p>
            <p class="profile__hero-copy profile__hero-copy--small">
                <?php print __('* Optional Information'); ?>
            </p>
        </div>
    </div>
    
    <form class="profile__form" id="complete-profile-form" method="post" novalidate>
        <?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
        <section class="profile__form-container profile__form-container--first">
            <div class="profile__form-primary">
                <h2 class="profile__form-title"><?php print __("Primary Information"); ?></h2>
                <div class="profile__select-container">
                    <label class="profile__label"><?php print __('Visibility Settings'); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="profile-visibility" name="profile_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container profile__input-container--profile">
                    <label class="profile__label" for="image-url"><?php print __("Profile Photo *"); ?></label>
                        <div id="profile-photo-uploader" class="profile__image-upload"<?php if($form && isset($form['image_url'])): ?> style="background-image: url('<?php print $form['image_url']; ?>');"<?php else: ?><?php if(is_array($community_fields) && isset($community_fields['image_url'])): ?> style="background-image: url('<?php print $community_fields['image_url']; ?>');"<?php endif; ?><?php endif; ?>>
                        <?php if(!is_array($community_fields) || !isset($community_fields['image_url'])): ?>
                        <svg width="75" height="75" viewBox="0 0 75 75" fill="none" xmlns="http://www.w3.org/2000/svg" class="create-group__upload-image-svg">
                            <path d="M59.375 9.375H15.625C12.1732 9.375 9.375 12.1732 9.375 15.625V59.375C9.375 62.8268 12.1732 65.625 15.625 65.625H59.375C62.8268 65.625 65.625 62.8268 65.625 59.375V15.625C65.625 12.1732 62.8268 9.375 59.375 9.375Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M26.5625 31.25C29.1513 31.25 31.25 29.1513 31.25 26.5625C31.25 23.9737 29.1513 21.875 26.5625 21.875C23.9737 21.875 21.875 23.9737 21.875 26.5625C21.875 29.1513 23.9737 31.25 26.5625 31.25Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M65.625 46.875L50 31.25L15.625 65.625" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php endif; ?>
                    </div>
                    <div class="profile__image-instructions">
                        <div><?php print __("Click or drag a photo above "); ?></div>
                        <div><?php print __("Minimum dimensions: 175 x 175px"); ?></div>
                    </div>
                    <input type="hidden" name="image_url" id="image-url" value="<?php if($form && isset($form['image_url'])): ?><?php $form['image_url']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['image_url'])): ?><?php print $community_fields['image_url']; ?><?php endif; ?><?php endif; ?>" />
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="profile-image-visibility" name="profile_image_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_image_visibility']) && $form['profile_image_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_image_visibility']) && $community_fields['profile_image_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="username"><?php print __("Username"); ?></label>
                    <input type="text" name="username" id="username" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['username']) || (isset($form['username']) && empty(trim($form['username'])) || isset($form['username_error_message']) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php print __("Username"); ?>" value="<?php print isset($form['username']) ? $form['username'] : $user->user_nicename; ?>"  required/>
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['username']) || (isset($form['username']) && empty(trim($form['username'])) || isset($form['username_error_message']))): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php if(isset($form['username_error_message'])): ?><?php print __($form['username_error_message']); ?><?php else: ?><?php print __("This field is required"); ?><?php endif; ?></div>
                    </div>
                    <span class="profile__input-desc"><?php print __('Usernames must be public'); ?></span>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="username-visibility" name="username_visibility" class="profile__select select--disabled" disabled>
                        <option value="<?php print PrivacySettings::PUBLIC_USERS; ?>"><?php print __('Public (Everyone)'); ?></option>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="first-name"><?php print __("First Name"); ?></label>
                    <input type="text" name="first_name" id="first-name" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['first_name']) || (isset($form['first_name']) && empty(trim($form['first_name'])) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php print __("First Name"); ?>" value="<?php print isset($form['first_name']) ? $form['first_name'] : $meta['first_name'][0]; ?>" required />
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['first_name']) || (isset($form['first_name']) && empty(trim($form['first_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php print __("This field is required"); ?></div>
                    </div>
                    <span class="profile__input-desc"><?php print __('Your first name cannot be hidden from registered users'); ?></span>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="firstname-visibility" name="first_name_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <?php if($value != 'Private (Only Me)'): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($meta['first_name_visibility'][0]) && $meta['first_name_visibility'][0] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="last-name"><?php print __("Last Name"); ?></label>
                    <input type="text" name="last_name" id="first-name" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['last_name']) || (isset($form['last_name']) && empty(trim($form['last_name'])) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php print __("Last Name"); ?>" value="<?php print isset($form['last_name']) ? $form['last_name'] : $meta['last_name'][0]; ?>" required />
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['last_name']) || (isset($form['last_name']) && empty(trim($form['last_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php print __("This field is required"); ?></div>
                    </div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="lastname-visibility" name="last_name_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($meta['last_name_visibility'][0]) && $meta['last_name_visibility'][0] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__select-container profile__select-container--full">
                    <label class="profile__label" for="pronoun"><?php print __("Preferred Pronouns *"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="pronoun" name="pronoun" class="profile__select">
                        <?php foreach($pronouns AS $p): ?>
                        <option value="<?php print $p; ?>"<?php if($form && isset($form['pronoun']) && $form['pronoun'] == $p): ?> selected<?php else: ?><?php if(isset($community_fields['pronoun']) && $community_fields['pronoun'] == $p): ?> selected<?php endif; ?><?php endif; ?>><?php print $p; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="profile-pronoun-visibility" name="profile_pronoun_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_pronoun_visibility']) && $form['profile_pronoun_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_pronoun_visibility']) && $community_fields['profile_pronoun_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="bio"><?php print __("Bio*"); ?></label>
                    <textarea name="bio" id="bio" class="profile__textarea"><?php if($form && isset($form['bio'])): ?><?php $form['bio']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['bio'])): ?><?php print $community_fields['bio']; ?><?php endif; ?><?php endif; ?></textarea>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="profile-bio-visibility" name="profile_bio_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_bio_visibility']) && $form['profile_bio_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_bio_visibility']) && $community_fields['profile_bio_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__select-container profile__select-container--inline">
                    <label class="profile__label" for="country"><?php print __("Country"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="country" name="country" class="profile__select<?php if($form && !isset($form['country']) || (isset($form['country']) && empty(trim($form['country'])))): ?> profile__select--error<?php endif; ?>" required>
                        <?php foreach($countries AS $key    =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['country']) && $form['country'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['country']) && $community_fields['country'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form__error-container<?php if($form && !isset($form['country']) || (isset($form['country']) && empty(trim($form['country'])))): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php print __("This field is required"); ?></div>
                    </div>
                </div>
                <div class="profile__input-container">
                    <label class="profile__label" for="city"><?php print __("City"); ?></label>
                    <input type="text" name="city" id="city" class="profile__input<?php if($form && !isset($form['city']) || (isset($form['city']) && empty(trim($form['city'])) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php print __("City"); ?>" value="<?php print isset($form['city']) ? $form['city'] : $community_fields['city']; ?>" required />
                    <div class="form__error-container<?php if($form && !isset($form['last_name']) || (isset($form['city']) && empty(trim($form['city'])) )): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php print __("This field is required"); ?></div>
                    </div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="profile-bio-visibility" name="profile_bio_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_location_visibility']) && $form['profile_location_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_location_visibility']) && $community_fields['profile_location_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="email"><?php print __("Email contact"); ?></label>
                    <input type="email" name="email" id="email" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['email']) || (isset($form['email']) && empty(trim($form['email'])) || isset($form['email_error_message']))): ?> profile__input--error<?php endif; ?>" placeholder="<?php print __("Email"); ?>" value="<?php print isset($form['email']) ? $form['email'] : $user->user_email; ?>" required/>
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['email']) || (isset($form['email']) && empty(trim($form['email'])) || isset($form['email_error_message']))): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php if(isset($form['email_error_message'])): ?><?php print __($form['email_error_message']); ?><?php else: ?><?php print __("This field is required"); ?><?php endif; ?></div>
                    </div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php print __("Can be viewed by"); ?></label>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                            <path d="M8.12499 9L12.005 12.88L15.885 9C16.275 8.61 16.905 8.61 17.295 9C17.685 9.39 17.685 10.02 17.295 10.41L12.705 15C12.315 15.39 11.685 15.39 11.295 15L6.70499 10.41C6.51774 10.2232 6.41251 9.96952 6.41251 9.705C6.41251 9.44048 6.51774 9.18683 6.70499 9C7.09499 8.62 7.73499 8.61 8.12499 9Z" fill="black" fill-opacity="0.54"/>
                        </g>
                    </svg>
                    <select id="email-visibility" name="email_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($meta['email_visibility'][0]) && $meta['email_visibility'][0] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>
        <?php
            $category_id = get_cat_ID('Community Participation Guidelines');

            $guidelines = get_posts(Array(
                'numberposts'   =>  1,
                'category'      =>  $category_id
            ));  
        ?>
        <section class="profile__form-container">
            <p>
                <?php 
                    print __("Some messaging around signing up for the email newsletter here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed commodo malesuada tincidunt.");
                ?>
            </p>
            <label class="create-group__checkbox-container" for="signup">
                <?php print __("Sign me up for the Mozilla Community Portal email newsletter "); ?>
                <input type="checkbox" name="signup" id="signup" value="<?php print __("Sign me up for the Mozilla Community Portal email newsletter "); ?>" />
                <span class="create-group__check">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
            </label>
        </section>
        <?php if(!isset($meta['agree'][0]) || $meta['agree'][0] != 'I Agree'): ?>
        <?php if(sizeof($guidelines) === 1): ?>
        <section class="profile__form-container">
            <?php print apply_filters('the_content', $guidelines[0]->post_content); ?>
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
        </section>
        <?php endif; ?>
        <?php endif; ?>
        <section class="profile__cta-container">
            <input type="submit" class="profile__cta" value="<?php print strtoupper(__("Complete Profile")); ?>" />
        </section>
    </form>
    <?php endif; ?>