<?php
/**
 * Member edit
 *
 * Edit form for members
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php
$theme_directory     = get_template_directory();
$current_translation = mozilla_get_current_translation();
require "{$theme_directory}/countries.php";
require "{$theme_directory}/languages.php";
require "{$theme_directory}/pronouns.php";
$subscribed = get_user_meta( $user->ID, 'newsletter', true );

?>

<?php if ( true === $complete && false === $edit ) : ?>
	<div class="profile__container">
		<section class="profile__success-message-container"> 
			<h1 class="profile__title"><?php esc_html_e( 'CONGRATULATIONS!', 'community-portal' ); ?></h1>
			<p class="profile__success-message">
				<?php
					esc_html_e( 'Your Account has been created! You can keep adding to your profile or dive right in. You are now ready to connect with other users, participate in events and projects, and get involved in the Mozilla community.', 'community-portal' );
				?>
			</p>
			<?php if ( isset( $subscribed ) && intval( $subscribed ) !== 1 ) : ?>	
				<p class="profile__error-message">
					<?php
						esc_html_e( 'Notice: We had a problem registering you for our newsletter. Please try signing up again later. To try again ', 'community-portal' );
					?>
					<a class="newsletter__link" href="<?php echo esc_attr( get_home_url( null, 'newsletter' ) ); ?>">
						<?php esc_html_e( 'Click here', 'community-portal' ); ?>
					</a> 
				</p>
			<?php endif; ?>
			<div class="profile__button-container">
				<?php $username = $updated_username ? $updated_username : $user->user_nicename; ?>
				<a href="<?php echo esc_attr( get_home_url( null, 'people/' . $username . '/profile/edit/group/1' ) ); ?>" class="profile__button">
					<?php esc_html_e( 'Complete your profile', 'community-portal' ); ?>
				</a>
				<a href="<?php echo esc_url_raw(get_home_url()); ?>" class="profile__button profile__button--secondary">
					<?php esc_html_e( 'Go back to browsing', 'community-portal' ); ?>
				</a>
			</div>
		</section>
	</div>
<?php else : ?>
	<div class="profile__hero">
		<div class="profile__hero-container">
			<div class="profile__hero-content">
				<h1 class="profile__title">
					<?php ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) ? esc_html_e( 'Edit Profile', 'community-portal' ) : esc_html_e( 'Complete Profile', 'community-portal' ); ?>
				</h1>
				<p class="profile__hero-copy profile__hero-copy--green">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M12 16V12" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<circle cx="12" cy="8" r="1" fill="black"/>
					</svg>
					<span>
						<?php esc_html_e( 'We\'ve pre-populated some of your information via your connected account with ', 'community-portal' ); ?>
						<a href="#" class="profile__hero-link"><?php esc_html_e( 'Mozilla SSO.', 'community-portal' ); ?></a>
					</span>
				</p>
			</div>
		</div>
	</div>
	<input type="hidden" id="string-translation" value="<?php echo esc_attr( $current_translation ? $current_translation : 'en' ); ?>" />
	<form class="profile__form" id="complete-profile-form" method="post" novalidate>
		<?php wp_nonce_field( 'newsletter_nonce', 'newsletter_nonce_field' ); ?>
		<?php wp_nonce_field( 'protect_content', 'my_nonce_field' ); ?>
		<section class="profile__form-container profile__form-container--first">
			<div class="profile__form-primary">
				<h2 class="profile__form-title"><?php esc_html_e( 'Primary Information', 'community-portal' ); ?></h2>
				<div class="profile__select-container">
					<label class="profile__label" for="profile-visibility"><?php esc_html_e( 'Visibility Settings', 'community-portal' ); ?></label>
					<select id="profile-visibility" name="profile_visibility" class="profile__select">
						<option><?php esc_html_e( 'Custom', 'community-portal' ); ?></option>
						<?php foreach ( $visibility_options as $key   => $value ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<?php if ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) : ?>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container profile__input-container--profile">
						<label class="profile__label" for="image-url">
							<?php esc_html_e( 'Profile Photo (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
							if ( isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ) {
								$avatar_url = preg_replace( '/^http:/i', 'https:', $form['image_url'] );
							} else {
								if ( is_array( $community_fields ) && isset( $community_fields['image_url'] ) && strlen( $community_fields['image_url'] ) > 0 ) {
									$avatar_url = preg_replace( '/^http:/i', 'https:', $community_fields['image_url'] );
								}
							}
						} else {
							if ( isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ) {
								$avatar_url = $form['image_url'];
							} else {
								if ( is_array( $community_fields ) && isset( $community_fields['image_url'] ) && strlen( $community_fields['image_url'] ) > 0 ) {
									$avatar_url = $community_fields['image_url'];
								}
							}
						}
						?>
						<div 
							id="dropzone-photo-uploader" 
							class="profile__image-upload"
							<?php if ( $form && isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ) : ?>
								style="background: url('<?php echo esc_url_raw( $avatar_url ); ?>') cover;"
							<?php else : ?>
								<?php if ( is_array( $community_fields ) && isset( $community_fields['image_url'] ) && strlen( $community_fields['image_url'] ) > 0 ) : ?>
									style="background: url('<?php echo esc_url_raw( $avatar_url ); ?>'); background-size: cover;"
								<?php endif; ?>
							<?php endif; ?>>
							<div class="dz-message" data-dz-message="">
								<div class="profile__image-instructions">
									<div class="form__error-container">
										<p class="form__error form__error--image"></p>
									</div>
									<button 
										id="dropzone-trigger" 
										type="button" 
										class="dropzone__image-instructions profile__image-instructions 
											<?php if ( isset( $community_fields['image_url'] ) && strlen( $community_fields['image_url'] ) !== 0 ) : ?>
												dropzone__image-instructions--hidden 
											<?php endif; ?>"
									>
										<?php esc_html_e( 'Click or drag a photo above', 'community-portal' ); ?>
										<span>
											<?php esc_html_e( 'minimum dimensions 175px by 175px', 'community-portal' ); ?>
										</span>
									</button>
								</div>
								<button 
									class="dz-remove
									<?php if ( ! isset( $community_fields['image_url'] ) || isset( $community_fields['image_url'] ) && 0 === strlen( $community_fields['image_url'] ) ) : ?>
										dz-remove--hide
									<?php endif; ?>" 
									type="button" 
									data-dz-remove="" 
								>
										<?php esc_html_e( 'Remove file', 'community-portal' ); ?>
								</button>
							</div>
						</div>
						<input 
							type="hidden" 
							name="image_url" 
							id="image-url" 
							value="
								<?php if ( $form && isset( $form['image_url'] ) ) : ?>
									<?php echo esc_url( $form['image_url'] ); ?>
								<?php else : ?>
									<?php if ( is_array( $community_fields ) && isset( $community_fields['image_url'] ) ) : ?>
										<?php echo esc_url( $community_fields['image_url'] ); ?>
									<?php endif; ?>
								<?php endif; ?>" 
						/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-image-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-image-visibility" name="profile_image_url_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( $form && isset( $form['	'] ) && "{$key}" === $form['profile_image_url_visibility'] ) : ?>
										selected
									<?php else : ?>
										<?php if ( isset( $community_fields['profile_image_url_visibility'] ) && "{$key}" === $community_fields['profile_image_url_visibility'] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif; ?>
			<hr class="profile__keyline" />
			<div class="profile__form-field">
				<div class="profile__input-container">
					<label class="profile__label" for="username">
						<?php esc_html_e( 'Username (required)', 'community-portal' ); ?>
					</label>
					<input 
						type="text" 
						name="username" 
						id="username" 
						class="profile__input
							<?php if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['username'] ) || ( isset( $form['username'] ) && empty( trim( $form['username'] ) ) || isset( $form['username_error_message'] ) ) ) : ?>
								profile__input--error
							<?php endif; ?>" 
						placeholder="<?php esc_attr_e( 'Username', 'community-portal' ); ?>" 
						value="<?php echo isset( $form['username'] ) ? esc_attr( wp_unslash( $form['username'] ) ) : esc_attr( wp_unslash( $user->user_nicename ) ); ?>"  
						required
					/>
					<div 
						class="form__error-container
							<?php if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['username'] ) || ( isset( $form['username'] ) && empty( trim( $form['username'] ) ) || isset( $form['username_error_message'] ) ) ) : ?>
								form__error-container--visible
							<?php endif; ?>"
					>
						<p class="form__error">
							<?php if ( isset( $form['username_error_message'] ) ) : ?>
								<span>
									<?php print esc_html( $form['username_error_message'] ); ?>
								</span>
							<?php else : ?>
								<span class="form__error__required">
									<?php esc_html_e( 'This field is required', 'community-portal' ); ?>
								</span>
								<span class="form__error__secondary">
									<?php esc_html_e( 'This username is already taken', 'community-portal' ); ?>
								</span>
							<?php endif; ?>
						</p>
						<span class="profile__input-desc">
							<?php esc_html_e( 'Usernames are public', 'community-portal' ); ?>
						</span>
					</div>
				</div>
				<div class="profile__select-container">
					<label class="profile__label" for="username-visibility">
						<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
					</label>
					<select id="username-visibility" name="username_visibility" class="profile__select select--disabled" disabled>
						<option value="<?php echo esc_attr( PrivacySettings::PUBLIC_USERS ); ?>">
							<?php esc_html_e( 'Public (Everyone)', 'community-portal' ); ?>
						</option>
					</select>
				</div>
			</div>
			<hr class="profile__keyline" />
			<div class="profile__form-field">
				<div class="profile__input-container">
					<label class="profile__label" for="first-name">
						<?php esc_html_e( 'First Name (required)', 'community-portal' ); ?>
					</label>
					<input 
						type="text" 
						name="first_name" 
						id="first-name" 
						class="profile__input
							<?php
							if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['first_name'] ) || ( isset( $form['first_name'] ) && empty( trim( $form['first_name'] ) ) ) ) :
								?>
								profile__input--error
							<?php endif; ?>" 
						placeholder="<?php esc_attr_e( 'First Name', 'community-portal' ); ?>" 
						value="<?php echo isset( $form['first_name'] ) ? esc_attr( wp_unslash( $form['first_name'] ) ) : esc_attr( wp_unslash( $meta['first_name'][0] ) ); ?>" 
						required 
					/>
					<div 
						class="form__error-container
							<?php if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['first_name'] ) || ( isset( $form['first_name'] ) && empty( trim( $form['first_name'] ) ) ) ) : ?>
									form__error-container--visible
							<?php endif; ?>"
					>
						<div class="form__error">
							<?php esc_html_e( 'This field is required', 'community-portal' ); ?>
						</div>
					</div>
					<span class="profile__input-desc">
						<?php esc_html_e( 'Your first name is always visible to registered users', 'community-portal' ); ?>
					</span>
				</div>
				<div class="profile__select-container">
					<label class="profile__label" for="firstname-visibility">
						<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
					</label>
					<select id="firstname-visibility" name="first_name_visibility" class="profile__select">
						<?php foreach ( $visibility_options as $key   => $value ) : ?>
							<?php if ( 'Private (Only Me)' !== $value ) : ?>
								<option 
									value="<?php echo esc_html( $key ); ?>"
									<?php
									if ( isset( $meta['first_name_visibility'][0] ) && "{$key}" === $meta['first_name_visibility'][0] ) :
										?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<hr class="profile__keyline" />
			<div class="profile__form-field">
				<div class="profile__input-container">
					<label class="profile__label" for="last-name">
						<?php esc_html_e( 'Last Name (required)', 'community-portal' ); ?>
					</label>
					<input 
						type="text" 
						name="last_name" 
						id="last-name" 
						class="profile__input
							<?php
							if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['last_name'] ) || ( isset( $form['last_name'] ) && empty( trim( $form['last_name'] ) ) ) ) :
								?>
								profile__input--error
							<?php endif; ?>" 
						placeholder="<?php esc_attr_e( 'Last Name', 'community-portal' ); ?>" 
						value="<?php echo isset( $form['last_name'] ) ? esc_attr( wp_unslash( $form['last_name'] ) ) : esc_attr( wp_unslash( $meta['last_name'][0] ) ); ?>" 
						required 
					/>
					<div 
						class="form__error-container
							<?php
							if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['last_name'] ) || ( isset( $form['last_name'] ) && empty( trim( $form['last_name'] ) ) ) ) :
								?>
								form__error-container--visible
							<?php endif; ?>"
					>
						<div class="form__error">
							<?php esc_html_e( 'This field is required', 'community-portal' ); ?>
						</div>
					</div>
				</div>
				<div class="profile__select-container">
					<label class="profile__label" for="lastname-visibility">
						<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
					</label>
					<select id="lastname-visibility" name="last_name_visibility" class="profile__select">
						<?php foreach ( $visibility_options as $key   => $value ) : ?>
							<option 
								value="<?php echo esc_attr( $key ); ?>"
									<?php
									if ( isset( $meta['last_name_visibility'][0] ) && "{$key}" === $meta['last_name_visibility'][0] ) :
										?>
										selected
									<?php endif; ?>
							>
								<?php echo esc_html( $value ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<?php if ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) : ?>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__select-container profile__select-container--full">
						<label class="profile__label" for="pronoun">
							<?php esc_html_e( 'Preferred Pronouns (optional)', 'community-portal' ); ?>
						</label>
						<select id="pronoun" name="pronoun" class="profile__select">
							<option value="">
								<?php esc_html_e( 'Preferred Pronoun', 'community-portal' ); ?>
							</option> 
							<?php foreach ( $pronouns as $key=>$p ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php
									if ( $form && isset( $form['pronoun'] ) && $form['pronoun'] === $key ) :
										?>
										selected
									<?php else : ?>
										<?php
										if ( isset( $community_fields['pronoun'] ) && $community_fields['pronoun'] === $key ) :
											?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $p ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-pronoun-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-pronoun-visibility" name="profile_pronoun_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_html( $key ); ?>"
									<?php
									if ( $form && isset( $form['profile_pronoun_visibility'] ) && "{$key}" === $form['profile_pronoun_visibility'] ) :
										?>
										selected
										<?php
										else :
											?>
											<?php
											if ( isset( $community_fields['profile_pronoun_visibility'] ) && "{$key}" === $community_fields['profile_pronoun_visibility'] ) :
												?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="bio">
							<?php esc_html_e( 'Bio (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( $form && isset( $form['bio'] ) ) {
							$bio = $form['bio'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['bio'] ) ) {
								$bio = $community_fields['bio'];
							}
						}
						?>
						<textarea name="bio" id="bio" class="profile__textarea" maxlength="3000">
							<?php echo esc_textarea( $bio ); ?>
						</textarea>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-bio-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-bio-visibility" name="profile_bio_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_html( $key ); ?>"
									<?php if ( $form && isset( $form['profile_bio_visibility'] ) && "{$key}" === $form['profile_bio_visibility'] ) : ?>
										selected
									<?php else : ?>
										<?php if ( isset( $community_fields['profile_bio_visibility'] ) && "{$key}" === $community_fields['profile_bio_visibility'] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__select-container profile__select-container--inline profile__select-container--half">
						<label class="profile__label" for="country">
							<?php esc_html_e( 'Country (optional)', 'community-portal' ); ?>
						</label>
						<select id="country" name="country" class="profile__select">
							<option value="0">
								<?php esc_html_e( 'Country', 'community-portal' ); ?>
							</option>
							<?php foreach ( $countries as $key    => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( $form && isset( $form['country'] ) && $form['country'] === $key ) : ?>
										selected
									<?php else : ?>
										<?php if ( isset( $community_fields['country'] ) && $community_fields['country'] === $key ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="profile__input-container">
						<label class="profile__label" for="city">
							<?php esc_html_e( 'City (optional)', 'community-portal' ); ?>
						</label>
						<input 
							type="text" 
							name="city" 
							id="city" 
							class="profile__input" 
							placeholder="<?php esc_attr_e( 'City', 'community-portal' ); ?>" 
							value="<?php echo isset( $form['city'] ) ? esc_attr( wp_unslash( $form['city'] ) ) : esc_attr( $community_fields['city'] ); ?>" 
							maxlength="180" 
						/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-location-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-location-visibility" name="profile_location_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( $form && isset( $form['profile_location_visibility'] ) && "{$key}" === $form['profile_location_visibility'] ) : ?>
										selected
									<?php else : ?>
										<?php if ( isset( $meta['profile_location_visibility'][0] ) && "{$key}" === $meta['profile_location_visibility'][0] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif; ?>
			<hr class="profile__keyline" />
			<div class="profile__form-field">
				<div class="profile__input-container">
					<label class="profile__label" for="email">
						<?php esc_html_e( 'Email contact (required)', 'community-portal' ); ?>
					</label>
					<input 
						type="email" 
						name="email" 
						id="email" 
						class="profile__input
							<?php if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['email'] ) || ( isset( $form['email'] ) && empty( trim( $form['email'] ) ) || isset( $form['email_error_message'] ) ) ) : ?>
								profile__input--error
							<?php endif; ?>" 
						placeholder="<?php esc_attr_e( 'Email', 'community-portal' ); ?>" 
						value="<?php echo isset( $form['email'] ) ? esc_attr( $form['email'] ) : esc_attr( $user->user_email ); ?>" 
						required
					/>
					<div class="form__error-container
						<?php if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['email'] ) || ( isset( $form['email'] ) && empty( trim( $form['email'] ) ) || isset( $form['email_error_message'] ) ) ) : ?>
							form__error-container--visible
						<?php endif; ?>"
					>
						<p class="form__error">
							<?php if ( isset( $form['email_error_message'] ) ) : ?>
								<span>
									<?php print esc_html( $form['email_error_message'] ); ?>
								</span>
							<?php else : ?>
								<span class="form__error__required">
									<?php esc_html_e( 'This field is required', 'community-portal' ); ?>
								</span>
								<span class="form__error__secondary">
									<?php esc_html_e( 'An account with this email already exists', 'community-portal' ); ?>
								</span>
								<span class="form__error__tertiary">
									<?php esc_html_e( 'Invalid email address', 'community-portal' ); ?>
								</span>
							<?php endif; ?>
						</p>
					</div>
				</div>
				<div class="profile__select-container">
					<label class="profile__label" for="email-visibility">
						<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
					</label>
					<select id="email-visibility" name="email_visibility" class="profile__select">
						<?php foreach ( $visibility_options as $key   => $value ) : ?>
							<option 
								value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $meta['email_visibility'][0] ) && "{$key}" === $meta['email_visibility'][0] ) : ?>
										selected
									<?php endif; ?>
							>
								<?php echo esc_html( $value ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<?php if ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) : ?>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="phone">
							<?php esc_html_e( 'Phone contact (optional)', 'community-portal' ); ?>
						</label>
						<input 
							type="text" 
							name="phone" 
							id="phone" 
							class="profile__input" 
							value="
								<?php if ( $form && isset( $form['phone'] ) ) : ?>
									<?php echo esc_attr( $form['phone'] ); ?>
								<?php else : ?>
									<?php if ( is_array( $community_fields ) && isset( $community_fields['phone'] ) ) : ?>
										<?php echo esc_attr( $community_fields['phone'] ); ?>
									<?php endif; ?>
								<?php endif; ?>"
						/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-phone-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-phone-visibility" name="profile_phone_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $community_fields['profile_phone_visibility'] ) && "{$key}" === $community_fields['profile_phone_visibility'] ) : ?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endif; ?>
		</section>
		<?php if ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) : ?>
			<section class="profile__form-container">
				<div class="profile__form-primary">
					<h2 class="profile__form-title">
						<?php esc_html_e( 'Social Links', 'community-portal' ); ?>
					</h2>
					<div class="profile__select-container">
						<label class="profile__label" for="social-visibility">
							<?php esc_html_e( 'Visibility Settings', 'community-portal' ); ?>
						</label>
						<select id="social-visibility" name="social_visibility" class="profile__select">
							<option><?php esc_html_e( 'Custom', 'community-portal' ); ?></option>
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>">
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="discourse"><?php esc_html_e( 'Mozilla Discourse username (optional)', 'community-portal' ); ?></label>
						<?php
						if ( $form && isset( $form['discourse'] ) ) {
							$discourse_value = $form['discourse'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['discourse'] ) ) {
								$discourse_value = $community_fields['discourse'];
							} else {
								$discourse_value = '';
							}
						}
						?>
						<input type="text" name="discourse" id="discourse" class="profile__input" value="<?php echo esc_attr( $discourse_value ); ?>"/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-discourse-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-discourse-visibility" name="profile_discourse_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $community_fields['profile_discourse_visibility'] ) && "{$key}" === $community_fields['profile_discourse_visibility'] ) : ?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="facebook">
							<?php esc_html_e( 'Facebook username (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( $form && isset( $form['facebook'] ) ) {
							$facebook_value = $form['facebook'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['facebook'] ) ) {
								$facebook_value = $community_fields['facebook'];
							} else {
								$facebook_value = '';
							}
						}
						?>
						<input type="text" name="facebook" id="facebook" class="profile__input" value="<?php echo esc_attr( $facebook_value ); ?>"/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-facebook-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-facebook-visibility" name="profile_facebook_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $community_fields['profile_facebook_visibility'] ) && "{$key}" === $community_fields['profile_facebook_visibility'] ) : ?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="twitter">
							<?php esc_html_e( 'Twitter username (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( $form && isset( $form['twitter'] ) ) {
							$twitter_value = $form['twitter'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['twitter'] ) ) {
								$twitter_value = $community_fields['twitter'];
							} else {
								$twitter_value = '';
							}
						}
						?>
						<input type="text" name="twitter" id="twitter" class="profile__input" value="<?php echo esc_attr( $twitter_value ); ?>"/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-twitter-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-twitter-visibility" name="profile_twitter_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $community_fields['profile_twitter_visibility'] ) && "{$key}" === $community_fields['profile_twitter_visibility'] ) : ?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="linkedin"><?php esc_html_e( 'LinkedIn username (optional)', 'community-portal' ); ?></label>
						<?php
						if ( $form && isset( $form['linkedin'] ) ) {
							$linkedin_value = $form['linkedin'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['linkedin'] ) ) {
								$linkedin_value = $community_fields['linkedin'];
							} else {
								$linkedin_value = '';
							}
						}
						?>
						<input type="text" name="linkedin" id="linkedin" class="profile__input" value="<?php echo esc_attr( $linkedin_value ); ?>"/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-linkedin-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-linkedin-visibility" name="profile_linkedin_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $community_fields['profile_linkedin_visibility'] ) && "{$key}" === $community_fields['profile_linkedin_visibility'] ) : ?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="github">
							<?php esc_html_e( 'Github username (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( $form && isset( $form['github'] ) ) {
							$github_value = $form['github'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['github'] ) ) {
								$github_value = $community_fields['github'];
							} else {
								$github_value = '';
							}
						}
						?>
						<input type="text" name="github" id="github" class="profile__input" value="<?php echo esc_attr( $github_value ); ?>"/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-github-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-github-visibility" name="profile_github_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php
									if ( isset( $community_fields['profile_github_visibility'] ) && "{$key}" === $community_fields['profile_github_visibility'] ) :
										?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="telegram">
							<?php esc_html_e( 'Telegram username (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( $form && isset( $form['telegram'] ) ) {
							$telegram_value = $form['telegram'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['telegram'] ) ) {
								$telegram_value = $community_fields['telegram'];
							} else {
								$telegram_value = '';
							}
						}
						?>
						<input type="text" name="telegram" id="telegram" class="profile__input" value="<?php echo esc_attr( $telegram_value ); ?>"/>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-telegram-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-telegram-visibility" name="profile_telegram_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php
									if ( isset( $community_fields['profile_telegram_visibility'] ) && "{$key}" === $community_fields['profile_telegram_visibility'] ) :
										?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<label class="profile__label" for="matrix">
							<?php esc_html_e( 'Matrix username (optional)', 'community-portal' ); ?>
						</label>
						<?php
						if ( $form && isset( $form['matrix'] ) ) {
							$matrix_input = $form['matrix'];
						} else {
							if ( is_array( $community_fields ) && isset( $community_fields['matrix'] ) ) {
								$matrix_input = $community_fields['matrix'];
							} else {
								$matrix_input = '';
							}
						}
						?>
						<input 
							placeholder="<?php esc_attr_e('username:domain', 'community-portal') ?>"
							type="text" 
							name="matrix" 
							id="matrix" 
							class="profile__input" 
							value="<?php echo esc_attr( $matrix_input ); ?>"
						/>
						<div class="form__error-container form__error-container--checkbox">
							<p class="form__error">
								<?php esc_html_e( 'Please format as username:domain', 'community-portal' ); ?>
							</p>
						</div>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-matrix-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-matrix-visibility" name="profile_matrix_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"
									<?php if ( isset( $community_fields['profile_matrix_visibility'] ) && "{$key}" === $community_fields['profile_matrix_visibility'] ) : ?>
										selected
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</section>
			<section class="profile__form-container">
				<div class="profile__form-primary">
					<h2 class="profile__form-title"><?php esc_html_e( 'Communication & Interests', 'community-portal' ); ?></h2>
					<div class="profile__select-container">
						<label class="profile__label" for="communication-visibility">
							<?php esc_html_e( 'Visibility Settings', 'community-portal' ); ?>
						</label>
						<select id="communication-visibility" name="communication_visibility" class="profile__select">
							<option><?php esc_html_e( 'Custom', 'community-portal' ); ?></option>   
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<?php
				if ( $form && isset( $form['languages'] ) && is_array( $form['languages'] ) ) {
					$languages_spoken = $form['languages'];
				} else {
					if ( is_array( $community_fields ) && isset( $community_fields['languages'] ) && is_array( $community_fields['languages'] ) ) {
						$languages_spoken = array_filter( $community_fields['languages'] );
					} else {
						$languages_spoken = array();
					}
				}
				?>
				<?php if ( count( $languages_spoken ) < 2 ) : ?>
					<hr class="profile__keyline" />
					<div class="profile__form-field profile__form-field--tight">
						<div class="profile__select-container profile__select-container--full profile__select-container--first">
							<label class="profile__label" for="languages-1">
								<?php esc_html_e( 'Languages spoken (optional)', 'community-portal' ); ?>
							</label>
							<select id="languages-1" name="languages[]" class="profile__select">
								<option value=""><?php esc_html_e( 'Make Selection', 'community-portal' ); ?>
								<?php foreach ( $languages as $key    => $language ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>"
										<?php if ( $form && isset( $form['langauges'][0] ) && $form['languages'][0] === $key ) : ?>
											selected
										<?php else : ?>
											<?php if ( isset( $community_fields['languages'][0] ) && $community_fields['languages'][0] === $key ) : ?>
												selected
											<?php endif; ?>
										<?php endif; ?>
									>
										<?php echo esc_html( $language ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="profile__select-container profile__select-container--hide-mobile profile__select-container--flex">
							<label class="profile__label profile__label--full profile__label--max" for="profile-languages-visibility">
								<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
							</label>
							<select id="profile-languages-visibility" class="profile__select profile__select--flex">
								<?php foreach ( $visibility_options as $key   => $value ) : ?>
									<option 
										value="<?php echo esc_attr( $key ); ?>"
										<?php
										if ( isset( $community_fields['profile_languages_visibility'] ) && "{$key}" === $community_fields['profile_languages_visibility'] ) :
											?>
											selected
											<?php
											else :
												?>
												<?php
												if ( isset( $community_fields['profile_languages_visibility'] ) && "{$key}" === $community_fields['profile_languages_visibility'] ) :
													?>
												selected
											<?php endif; ?>
										<?php endif; ?>
									>
										<?php echo esc_html( $value ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="profile__form-field profile__form-field--tight profile__form-field--hidden">
						<div class="profile__select-container profile__select-container--full profile__select-container--no-label profile__select-container--languages">
							<select id="languages-" name="languages[]" class="profile__select profile__select--short profile__select--hide">
								<option value=""><?php esc_html_e( 'Make Selection (optional)', 'community-portal' ); ?>
								<?php foreach ( $languages as $key    => $language ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>">
										<?php echo esc_html( $language ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<button type="button" class="profile__remove-language">&mdash;</button>
						</div>
						<div class="profile__select-container profile__select-container--empty">
						</div>                      
					</div>
					<div class="profile__add-language-container"> 
						<a href="#" class="profile__add-language"><?php esc_html_e( 'Add Another Language', 'community-portal' ); ?></a>
					</div>
				<?php else : ?>
					<hr class="profile__keyline" />
					<?php foreach ( $languages_spoken as $index => $value ) : ?>
						<div class="profile__form-field profile__form-field--tight">
							<div 
								class="profile__select-container profile__select-container--full
								<?php if ( $index > 0 ) : ?>
									profile__select-container--no-label
								<?php endif; ?>
								<?php if ( 0 === $index ) : ?>
									profile__select-container--first
								<?php endif; ?>"
							>
								<?php if ( 0 === $index ) : ?>
									<label class="profile__label" for="languages-<?php echo esc_attr( $index ); ?>">
										<?php esc_html_e( 'Languages spoken (optional)', 'community-portal' ); ?>
									</label>
								<?php endif; ?>
								<select 
									id="languages-<?php echo esc_attr( $index ); ?>" 
									name="languages[]" 
									class="profile__select
									<?php if ( $index > 0 ) : ?>
										profile__select--short
									<?php endif; ?>"
								>
									<option value=""><?php esc_html_e( 'Make Selection', 'community-portal' ); ?>
									<?php foreach ( $languages as $key    => $language ) : ?>
										<option 
											value="<?php echo esc_attr( $key ); ?>"
											<?php if ( $form && isset( $form['languages'][ $index ] ) && $form['languages'][ $index ] === $key ) : ?>
												selected
											<?php else : ?>
												<?php if ( isset( $community_fields['languages'][ $index ] ) && $community_fields['languages'][ $index ] === $key ) : ?>
													selected
												<?php endif; ?>
											<?php endif; ?>
										>
											<?php echo esc_html( $language ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<?php if ( $index > 0 ) : ?>
									<button type="button" class="profile__remove-language">&mdash;</button>
								<?php endif; ?>
							</div>
							<?php if ( 0 === $index ) : ?>
								<div class="profile__select-container profile__select-container--hide-mobile profile__select-container--flex">
									<label class="profile__label profile__label--full profile__label--max" for="profile-languages-visibility">
										<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
									</label>
									<select id="profile-languages-visibility" class="profile__select profile__select--flex">
										<option value=""><?php esc_html_e( 'Make Selection', 'community-portal' ); ?>
										<?php foreach ( $visibility_options as $key   => $value ) : ?>
											<option value="<?php echo esc_attr( $key ); ?>"
												<?php if ( $form && isset( $form['profile_languages_visibility'] ) && "{$key}" === $form['profile_languages_visibility'] ) : ?>
													selected
												<?php else : ?>
													<?php
													if ( isset( $community_fields['profile_languages_visibility'] ) && "{$key}" === $community_fields['profile_languages_visibility'] ) :
														?>
														selected
													<?php endif; ?>
												<?php endif; ?>
											>
												<?php echo esc_html( $value ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php else : ?>
								<div class="profile__select-container profile__select-container--empty">
								</div>  
							<?php endif; ?>
						</div>
						<?php if ( ( $index + 1 ) === count( $languages_spoken ) ) : ?>
							<div class="profile__add-language-container"> 
								<a href="#" class="profile__add-language"><?php esc_html_e( 'Add Another Language', 'community-portal' ); ?></a>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="profile__select-container profile__select-container--mobile">
					<label class="profile__label" for="profile-languages-visibility-mobile">
						<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
					</label>
					<select id="profile-languages-visibility-mobile" class="profile__select profile__select--mobile">
						<?php foreach ( $visibility_options as $key   => $value ) : ?>
							<option 
								value="<?php echo esc_html( $key ); ?>"
								<?php if ( $form && isset( $form['profile_languages_visibility'] ) && "{$key}" === $form['profile_languages_visibility'] ) : ?>
									selected
								<?php else : ?>
									<?php
									if ( isset( $community_fields['profile_languages_visibility'] ) && "{$key}" === $community_fields['profile_languages_visibility'] ) :
										?>
										selected
									<?php endif; ?>
								<?php endif; ?>
							>
								<?php echo esc_html( $value ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<?php
				if ( $form && isset( $form['profile_languages_visibility'] ) ) {
					$language_visibility = $form['profile_languages_visibility'];
				} else {
					if ( isset( $community_fields['profile_languages_visibility'] ) ) {
						$language_visibility = $community_fields['profile_languages_visibility'];
					}
				}
				?>
				<input type="hidden" name="profile_languages_visibility" value="<?php echo esc_attr( $language_visibility ); ?>" />
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div>
						<fieldset class="fieldset">
							<legend class="profile__label">
								<?php esc_html_e( 'Skills and interests (optional)', 'community-portal' ); ?>
							</legend>
							<?php
								// Get all tags!
								$tags = get_tags( array( 'hide_empty' => false ) );
							?>
							<div class="profile__tag-container">
								<?php foreach ( $tags as &$loop_tag ) : ?>
									<?php
									if ( 'en' !== $current_translation ) {
										if ( false !== stripos( $loop_tag->slug, '_' ) ) {
											$loop_tag->slug = substr( $loop_tag->slug, 0, stripos( $loop_tag->slug, '_' ) );
										}
									}
									?>
									<input 
										class="profile__checkbox" 
										type="checkbox" 
										id="<?php echo esc_attr( $loop_tag->slug ); ?>" 
										data-value="<?php echo esc_attr( $loop_tag->slug ); ?>"
									>
									<label 
										class="profile__tag
											<?php
											if ( in_array( $loop_tag->slug, $form_tags, true ) ) :
												?>
												profile__tag--active
											<?php endif; ?>" 
										for="<?php echo esc_attr( $loop_tag->slug ); ?>"
									>
										<?php echo esc_html( $loop_tag->name ); ?>
									</label>
								<?php endforeach; ?>
							</div>
							<?php
							if ( $form && isset( $form['tags'] ) ) {
								$input_tags = $form['tags'];
							} else {
								if ( $community_fields && isset( $community_fields['tags'] ) ) {
									$input_tags = $community_fields['tags'];
								} else {
									$input_tags = '';
								}
							}
							?>
							<input type="hidden" value="<?php echo esc_attr( $input_tags ); ?>" name="tags" id="tags" /> 
						</fieldset>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-tags-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-tags-visibility" name="profile_tags_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( $form && isset( $form['profile_tags_visibility'] ) && "{$key}" === $form['profile_tags_visibility'] ) : ?>
										selected
									<?php else : ?>
										<?php if ( isset( $community_fields['profile_tags_visibility'] ) && "{$key}" === $community_fields['profile_tags_visibility'] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</section>
			<section class="profile__form-container">
				<div class="profile__form-primary">
					<h2 class="profile__form-title"><?php esc_html_e( 'Community Portal Activity', 'community-portal' ); ?></h2>
					<div class="profile__select-container">
						<label class="profile__label" for="portal-visibility"><?php esc_html_e( 'Visibility Settings', 'community-portal' ); ?></label>
						<select id="portal-visibility" name="portal_visibility" class="profile__select">
							<option><?php esc_html_e( 'Custom', 'community-portal' ); ?></option>
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<div class="profile__copy"><?php esc_html_e( 'Groups joined', 'community-portal' ); ?></div>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-groups-joined-visibility"><?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?></label>
						<select id="profile-groups-joined-visibility" name="profile_groups_joined_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( $form && isset( $form['profile_groups_joined_visibility'] ) && "{$key}" === $form['profile_groups_joined_visibility'] ) : ?>
										selectd
									<?php else : ?>
										<?php if ( isset( $community_fields['profile_groups_joined_visibility'] ) && "{$key}" === $community_fields['profile_groups_joined_visibility'] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
					<div class="profile__form-field">
						<div class="profile__input-container">
							<div class="profile__copy"><?php esc_html_e( 'Events attended', 'community-portal' ); ?></div>
						</div>
						<div class="profile__select-container">
						<label class="profile__label" for="profile-events-attended-visibility">
							<?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?>
						</label>
						<select id="profile-events-attended-visibility" name="profile_events_attended_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php if ( $form && isset( $form['profile_events_attended_visibility'] ) && "{$key}" === $form['profile_events_attended_visibility'] ) : ?>
										selected
									<?php else : ?> 
										<?php if ( isset( $community_fields['profile_events_attended_visibility'] ) && "{$key}" === $community_fields['profile_events_attended_visibility'] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<div class="profile__copy"><?php esc_html_e( 'Events organized', 'community-portal' ); ?></div>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-events-organized-visibility"><?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?></label>
						<select id="profile-events-organized-visibility" name="profile_events_organized_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key => $value ) : ?>
								<option 
									value="<?php echo esc_attr( "{$key}" ); ?>"
									<?php if ( $form && isset( $form['profile_events_organized_visibility'] ) && "{$key}" === $form['profile_events_organized_visibility'] ) : ?>
										selected
									<?php else : ?>
										<?php if ( isset( $community_fields['profile_events_organized_visibility'] ) && "{$key}" === $community_fields['profile_events_organized_visibility'] ) : ?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<hr class="profile__keyline" />
				<div class="profile__form-field">
					<div class="profile__input-container">
						<div class="profile__copy"><?php esc_html_e( 'Campaigns participated in', 'community-portal' ); ?></div>
					</div>
					<div class="profile__select-container">
						<label class="profile__label" for="profile-discourse-visibility"><?php esc_html_e( 'Can be viewed by', 'community-portal' ); ?></label>
						<select id="profile-campaigns-visibility" name="profile_campaigns_visibility" class="profile__select">
							<?php foreach ( $visibility_options as $key   => $value ) : ?>
								<option 
									value="<?php echo esc_attr( $key ); ?>"
									<?php
									if ( $form && isset( $form['profile_campaigns_visibility'] ) && "{$key}" === $form['profile_campaigns_visibility'] ) :
										?>
										selected
									<?php else : ?>
										<?php
										if ( isset( $community_fields['profile_campaigns_visibility'] ) && "{$key}" === $community_fields['profile_campaigns_visibility'] ) :
											?>
											selected
										<?php endif; ?>
									<?php endif; ?>
								>
									<?php echo esc_html( $value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</section>  
		<?php endif; ?>
		<?php
			$category_id = get_cat_ID( 'Community Participation Guidelines' );
			$guidelines  = get_posts(
				array(
					'numberposts' => 1,
					'category'    => $category_id,
				)
			);
		?>
		<?php
		if ( ! isset( $subscribed ) || ( isset( $subscribed ) && intval( $subscribed ) !== 1 ) ) :
			?>
			<section class="profile__form-container">
				<div class="profile__newsletter">
				<?php include get_template_directory() . '/templates/newsletter-form.php'; ?>
				</div>
			</section>
			<?php
			endif;
		?>
		<?php if ( ! isset( $meta['agree'][0] ) || 'I Agree' !== $meta['agree'][0] ) : ?>
			<?php if ( 1 === count( $guidelines ) ) : ?> 
				<section class="profile__form-container cpg">
					<?php
						echo wp_kses(
							apply_filters( 'the_content', $guidelines[0]->post_content ),
							array(
								'p' => array(),
								'a' => array( 'href' => array() ),
							)
						);
					?>
					<input class="checkbox--hidden" type="checkbox" name="agree" id="agree" value="I Agree" required />
					<label class="create-group__checkbox-container cpg__label" for="agree">
						<p class="create-group__checkbox-container__copy">
							<?php esc_html_e( 'I agree to respect and adhere to', 'community-portal' ); ?>
							<a class="create-group__checkbox-container__link" href="https://www.mozilla.org/about/governance/policies/participation/"><?php esc_html_e( 'Mozilla\'s Community Participation Guidelines*', 'community-portal' ); ?></a>
						</p>
						<div class="form__error-container form__error-container--checkbox">
							<p class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></p>
						</div>
					</label>
				</section>
			<?php endif ?>
		<?php endif ?>
		<section class="profile__cta-container">
			<input type="submit" class="profile__cta" value="<?php esc_attr_e( 'Complete Profile', 'community-portal' ); ?>" />
			<?php if ( isset( $meta['agree'][0] ) && 'I Agree' === $meta['agree'][0] ) : ?>
				<a id="profile-delete-account" class="profile__delete-cta"><?php esc_html_e( 'Delete Profile', 'community-portal' ); ?></a>
				<div class="profile__delete-account-error profile__delete-account-error--hidden"><?php esc_html_e( 'Could not delete profile at this time, please contact a community manager', 'community-portal' ); ?></div>
			<?php endif; ?>
		</section>
	</form>
<?php endif; ?>
