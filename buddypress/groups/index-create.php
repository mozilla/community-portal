<?php
/**
 * Group create page
 *
 * Group create page for community portal
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

session_start();
do_action( 'bp_before_create_group_page' );

$user                = wp_get_current_user();
$meta                = get_user_meta( $user->ID );
$current_translation = mozilla_get_current_translation();
$step = null;

if ( ! isset( $meta['agree'][0] ) || 'I Agree' !== $meta['agree'][0] ) {
	if ( $current_translation ) {
		wp_safe_redirect( "/{$current_translation}/people/{$user->user_nicename}/profile/edit/group/1/" );
	} else {
		wp_safe_redirect( "/people/{$user->user_nicename}/profile/edit/group/1/" );
	}
	exit();
}


if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['step'] ) && isset( $_REQUEST['group_create_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['group_create_field'] ) ), 'group_create' ) ) {
	$step = intval( sanitize_text_field( wp_unslash( $_POST['step'] ) ) );
}

if ( 3 === $step ) {
	if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['group_slug'] ) && isset( $_REQUEST['group_create_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['group_create_field'] ) ), 'group_create' ) ) {
		$slug = sanitize_text_field( wp_unslash( $_POST['group_slug'] ) );
		if ( $current_translation ) {
			wp_safe_redirect( "/{$current_translation}/groups/{$slug}" );
		} else {
			wp_safe_redirect( "/groups/{$slug}" );
		}
		exit();
	}
}

// Main header template!
get_header();

$template_dir = get_template_directory();
require "{$template_dir}/countries.php";
require "{$template_dir}/languages.php";

if ( isset( $_SESSION['form'] ) ) {
	$form = $_SESSION['form'];
}

$form_tags           = isset( $form['tags'] ) ? array_filter( explode( ',', $form['tags'] ), 'strlen' ) : array();
$current_translation = mozilla_get_current_translation();
?>
<div class="content">
	<div class="create-group">
		<?php if ( 3 !== $step ) : ?>
		<div class="create-group__hero">
			<div class="create-group__hero-container">
				<h1 class="create-group__title"><?php esc_html_e( 'Create a Mozilla Group', 'community-portal' ); ?></h1>
			</div>
		</div>
		<form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>
			<input type="hidden" id="string-translation" value="<?php echo esc_attr( $current_translation ); ?>" />
			<div class="create-group__container">
				<ol class="create-group__menu">
					<li class="create-group__menu-item
					<?php
					if ( 1 === $step ) :
						?>
						create-group__menu-item--disabled<?php endif; ?>"><a href="#" class="create-group__menu-link
						<?php
						if ( 1 === $step ) :
							?>
						create-group__menu-link--disabled<?php endif; ?>" data-step=""><?php esc_html_e( 'Basic Information', 'community-portal' ); ?></a></li>
					<li class="create-group__menu-item
					<?php
					if ( 1 !== $step ) :
						?>
						create-group__menu-item--disabled<?php endif; ?>"><a href="#" class="create-group__menu-link
						<?php
						if ( 1 !== $step ) :
							?>
						create-group__menu-link--disabled<?php endif; ?>" data-step=""><?php esc_html_e( 'Terms & Responsibilities', 'community-portal' ); ?></a></li>
				</ol>
				<div class="create-group__menu create-group__menu--mobile">
					<div class="create-group__select-container">
						<select id="create-group-mobile-nav" class="create-group__select" name="mobile_nav">
							<option value="1"
							<?php
							if ( 1 !== $step ) :
								?>
								selected<?php endif; ?>><?php esc_html_e( 'Basic Information', 'community-portal' ); ?></option>
							<option value="2"
							<?php
							if ( 1 === $step ) :
								?>
								selected<?php endif; ?>><?php esc_html_e( 'Terms & Responsibilities', 'community-portal' ); ?></option>
						</select>
					</div>
				</div>
				<?php do_action( 'bp_before_create_group_content_template' ); ?>
					<?php wp_nonce_field( 'group_create', 'group_create_field' ); ?>
					<?php wp_nonce_field( 'protect_content', 'my_nonce_field' ); ?>
					<?php do_action( 'bp_before_create_group' ); ?>
					<input type="hidden" name="step" value="1" />
					<section class="create-group__details
					<?php
					if ( 1 === $step ) :
						?>
						create-group__details--hidden<?php endif; ?>">
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--60">
								<label class="create-group__label" for="group-name"><?php esc_html_e( 'What is your group\'s name? *', 'community-portal' ); ?></label>
								<input type="text" name="group_name" id="group-name" class="create-group__input
								<?php
								if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['group_name'] ) || ( isset( $form['group_name'] ) && empty( trim( $form['group_name'] ) ) ) ) :
									?>
									create-group__input--error<?php endif; ?>" value="<?php echo isset( $form['group_name'] ) ? esc_attr( sanitize_text_field( wp_unslash( $form['group_name'] ) ) ) : ''; ?>" required />
								<div class="form__error-container
								<?php
								if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['group_name'] ) || ( isset( $form['group_name'] ) && empty( trim( $form['group_name'] ) ) ) ) :
									?>
									form__error-container--visible<?php endif; ?>">
									<p class="form__error">
										<span class="form__error__required"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></span>
										<span class="form__error__secondary"><?php esc_html_e( 'This group name is already taken', 'community-portal' ); ?></span>
									</p>
								</div>
							</div>
							<div class="create-group__input-container create-group__input-container--40">
								<label class="create-group__label" for="group-type"><?php esc_html_e( 'Group Type', 'community-portal' ); ?></label>
								<div class="create-group__select-container">
									<select id="group-type" class="create-group__select" name="group_type" required>
										<option value="Online"
										<?php
										if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $form['group_type'] ) && 'Online' === $form['group_type'] || ( empty( $form['group_type'] ) ) ) :
											?>
											selected<?php endif; ?>><?php esc_html_e( 'Online', 'community-portal' ); ?></option>
										<option value="Offline"
										<?php
										if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $form['group_type'] ) && 'Offline' === $form['group_type'] ) :
											?>
											selected<?php endif; ?>><?php esc_html_e( 'Offline', 'community-portal' ); ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
								<label class="create-group__label" for="group-language"><?php esc_html_e( 'Language', 'community-portal' ); ?></label>
								<div class="create-group__select-container">
									<select id="group-language" class="create-group__select" name="group_language">
										<option value="0"><?php esc_html_e( 'Language', 'community-portal' ); ?></option>
										<?php foreach ( $languages as $code => $language_name ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>"
																<?php
																if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $form['group_language'] ) && $form['group_language'] === $code ) :
																	?>
											selected<?php endif; ?>><?php echo esc_html( $language_name ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container  create-group__input-container--40 create-group__input-container--vertical-spacing">
								<label class="create-group__label" for="group-country"><?php esc_html_e( 'Group Location', 'community-portal' ); ?></label>
								<div class="create-group__select-container">
									<select id="group-country" class="create-group__select" name="group_country">
										<option value="0"><?php esc_html_e( 'Country', 'community-portal' ); ?></option>
										<?php foreach ( $countries as $code => $country ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>"
																<?php
																if ( isset( $form['group_country'] ) && $form['group_country'] === $code ) :
																	?>
											selected<?php endif; ?>><?php echo esc_html( $country ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
								<label class="create-group__label" for="group-city"><?php esc_html_e( 'City', 'community-portal' ); ?></label>
								<input type="text" name="group_city" id="group-city" class="create-group__input" placeholder="<?php esc_attr_e( 'City', 'community-portal' ); ?>" value="<?php echo isset( $form['group_city'] ) ? esc_attr( sanitize_text_field( wp_unslash( $form['group_city'] ) ) ) : ''; ?>" maxlength="180" />
							</div>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
								<label class="create-group__label" for="group-desc"><?php esc_html_e( 'Provide a short group description *', 'community-portal' ); ?></label>
								<textarea name="group_desc" id="group-desc" class="create-group__textarea
								<?php
								if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['group_desc'] ) || ( isset( $form['group_desc'] ) && empty( trim( $form['group_desc'] ) ) ) ) :
									?>
									create-group__input--error<?php endif; ?>" required maxlength="3000"><?php echo isset( $form['group_desc'] ) ? esc_html( sanitize_textarea_field( wp_unslash( $form['group_desc'] ) ) ) : ''; ?></textarea>
								<div class="form__error-container
								<?php
								if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['group_desc'] ) || ( isset( $form['group_desc'] ) && empty( trim( $form['group_desc'] ) ) ) ) :
									?>
									form__error-container--visible<?php endif; ?>">
									<div class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></div>
								</div>
							</div>
							<div class="create-group__input-container create-group__input-container--40 create-group__input-container--vertical-spacing">
								<label class="create-group__label"><?php esc_html_e( 'Select an image', 'community-portal' ); ?></label>
								<div id="dropzone-photo-uploader" class="create-group__image-upload">
									<div class="dz-message" data-dz-message="">
										<div>
											<div class="form__error-container">
												<div class="form__error form__error--image"></div>
											</div>
											<button type="button" id="dropzone-trigger" class="dropzone__image-instructions create-group__image-instructions">
												<?php esc_html_e( 'Click or drag a photo above', 'community-portal' ); ?>
												<span><?php esc_html_e( 'min dimensions 703px by 400px', 'community-portal' ); ?></span>
											</button>
										</div>
										<button type="button" class="dz-remove
										<?php
										if ( ! isset( $form['image_url'] ) || 0 === strlen( $form['image_url'] ) ) :
											?>
											dz-remove--hide<?php endif; ?>" data-dz-remove="" ><?php esc_html_e( 'Remove file', 'community-portal' ); ?></button>
									</div>
								</div>
								<input type="hidden" name="image_url" id="image-url" value="<?php echo ( isset( $form['image_url'] ) ) ? esc_url_raw( wp_unslash( $form['image_url'] ) ) : ''; ?>" />
							</div>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--full create-group__input-container--vertical-spacing">
								<fieldset class="fieldset">
									<legend class="create-group__label"><?php esc_html_e( 'Tags for your group', 'community-portal' ); ?></legend>
									<?php
										// Get all tags!

										$tags = get_tags( array( 'hide_empty' => false ) );
									?>
									<div class="create-group__tag-container">
										<?php foreach ( $tags as $loop_tag ) : ?>
											<?php
												if ( 'en' !== $current_translation ) {
													if ( false !== stripos( $loop_tag->slug, '_' ) ) {
														$loop_tag->slug = substr( $loop_tag->slug, 0, stripos( $loop_tag->slug, '_' ) );
													}
												}
											?>
											<input class="create-group__checkbox" type="checkbox" id="<?php echo esc_attr( $loop_tag->slug ); ?>" data-value="<?php echo esc_attr( $loop_tag->slug ); ?>">
											<label class="create-group__tag
											<?php
											if ( in_array( $loop_tag->slug, $form_tags, true ) ) :
												?>
												create-group__tag--active<?php endif; ?>" for="<?php echo esc_attr( $loop_tag->slug ); ?>"><?php echo esc_html( $loop_tag->name ); ?></label>
										<?php endforeach; ?>
									</div>
									<input type="hidden" value="<?php echo ( isset( $form['tags'] ) ) ? esc_attr( sanitize_text_field( wp_unslash( $form['tags'] ) ) ) : ''; ?>" name="tags" id="tags" /> 
								</fieldset>
							</div>
						</div>
					</section>
					<section class="create-group__details
					<?php
					if ( 1 === $step ) :
						?>
						create-group__details--hidden<?php endif; ?>">
						<div class="create-group__section-title"><?php esc_html_e( 'Group Meetings', 'community-portal' ); ?></div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--40">    
								<label class="create-group__label" for="group-address-type" ><?php esc_html_e( 'Where do you meet?', 'community-portal' ); ?></label>
								<div class="create-group__select-container">
									<select class="create-group__select" name="group_address_type" id="group-address-type">
										<option value="<?php esc_attr_e( 'Address', 'community-portal' ); ?>" 
																<?php
																if ( isset( $form['group_address_type'] ) && 'Address' === $form['group_address_type'] ) :
																	?>
											selected<?php endif; ?>><?php esc_html_e( 'Address', 'community-portal' ); ?></option>
										<option value="<?php esc_attr_e( 'URL', 'community-portal' ); ?>"
																<?php
																if ( isset( $form['group_address_type'] ) && 'URL' === $form['group_address_type'] ) :
																	?>
											selected<?php endif; ?>><?php esc_html_e( 'URL', 'community-portal' ); ?></option>
									</select>
								</div>
							</div>
							<div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
								<label class="create-group__label" for="group-address" ><?php esc_html_e( 'Address', 'community-portal' ); ?></label>
								<input type="text" name="group_address" id="group-address" class="create-group__input" value="<?php echo isset( $form['group_address'] ) ? esc_attr( sanitize_text_field( wp_unslash( $form['group_address'] ) ) ) : ''; ?>" />
							</div>
						</div>
						<div class="create-group__input-container create-group__input-container--full">
							<label class="create-group__label" for="group-desc"><?php esc_html_e( 'Meeting details', 'community-portal' ); ?></label>
							<textarea name="group_meeting_details" id="group-meeting-details" class="create-group__textarea create-group__textarea--full create-group__textarea--short" ><?php echo isset( $form['group_meeting_details'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_meeting_details'] ) ) ) : ''; ?></textarea>
						</div>
					</section>
					<section class="create-group__details
					<?php
					if ( 1 === $step ) :
						?>
						create-group__details--hidden<?php endif; ?>">
						<h2 class="create-group__section-title"><?php esc_html_e( 'Community Links', 'community-portal' ); ?></h2>
						<div class="create-group__input-row create-group__subsection">
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label" for="group-discourse"><?php esc_html_e( 'Discourse', 'community-portal' ); ?></label>
								<input type="text" name="group_discourse" id="group-discourse" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link" value="<?php echo isset( $form['group_discourse'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_discourse'] ) ) ) : ''; ?>" />
							</div>
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label"  for="group-matrix"><?php esc_html_e( 'Matrix', 'community-portal' ); ?></label>
								<input type="text" placeholder="room-alias:domain" name="group_matrix" id="group-matrix" class="create-group__input 	create-group__input--inline"  value="<?php echo isset( $form['group_matrix'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_matrix'] ) ) ) : ''; ?>"/>
								<div class="form__error-container form__error-container--checkbox">
									<div class="form__error"><?php esc_html_e( 'Please format as room-alias:domain', 'community-portal' ); ?></div>
								</div>
							</div>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label" for="group-facebook"><?php esc_html_e( 'Facebook', 'community-portal' ); ?></label>
								<input type="text" name="group_facebook" id="group-facebook" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link"  value="<?php echo isset( $form['group_facebook'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_facebook'] ) ) ) : ''; ?>"/>
							</div>
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
								<label class="create-group__label" for="group-twitter"><?php esc_html_e( 'Twitter', 'community-portal' ); ?></label>
								<input type="text" name="group_twitter" id="group-twitter" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link"  value="<?php echo isset( $form['group_twitter'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_twitter'] ) ) ) : ''; ?>"/>
							</div>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
							<label class="create-group__label" for="group-telegram"><?php esc_html_e( 'Telegram', 'community-portal' ); ?></label>
							<input type="text" placeholder="https://" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline"  value="<?php echo isset( $form['group_telegram'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_telegram'] ) ) ) : ''; ?>"/>
						</div>
						<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
							<label class="create-group__label" for="group-github"><?php esc_html_e( 'GitHub', 'community-portal' ); ?></label>
							<input type="text" name="group_github" id="group-github" placeholder="https://" class="create-group__input create-group__input--inline create-group__community-link"  value="<?php echo isset( $form['group_github'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_github'] ) ) ) : ''; ?>"/>
						</div>

					</div>
					<div class="create-group__input-row">
						<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
							<label class="create-group__label"  for="group-other"><?php esc_html_e( 'Other', 'community-portal' ); ?></label>
							<input type="text" placeholder="https://" name="group_other" id="group-other" class="create-group__input create-group__input--inline"  value="<?php echo isset( $form['group_other'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_other'] ) ) ) : ''; ?>"/>
						</div>
					</div>
					</section>
					<section class="create-group__details
					<?php
					if ( 1 === $step ) :
						?>
						create-group__details--hidden<?php endif; ?>">
						<div class="create-group__section-title">
							<?php esc_html_e( 'Secondary Group Contact', 'community-portal' ); ?>
							<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<title><?php esc_html_e( 'Secondary group contact', 'community-portal' ); ?></title>
								<path d="M9 16.5C13.1421 16.5 16.5 13.1421 16.5 9C16.5 4.85786 13.1421 1.5 9 1.5C4.85786 1.5 1.5 4.85786 1.5 9C1.5 13.1421 4.85786 16.5 9 16.5Z" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M9 6V9" stroke="#CDCDD4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<circle cx="9" cy="12" r="0.75" fill="#CDCDD4"/>
							</svg>
						</div>
						<div class="create-group__input-row">
							<div class="create-group__input-container create-group__input-container--full">
								<label for="group-admin" class="create-group__label"><?php esc_html_e( 'Username *', 'community-portal' ); ?></label>
								<input type="text" name="group_admin" id="group-admin" class="create-group__input" value="<?php print isset( $form['group_admin'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_admin'] ) ) ) : ''; ?>" placeholder="<?php esc_html_e( 'Username', 'community-portal' ); ?>" required/>
								<div class="form__error-container
								<?php
								if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! isset( $form['group_admin_id'] ) || ( isset( $form['group_admin_id'] ) && empty( trim( $form['group_admin_id'] ) ) ) ) :
									?>
									form__error-container--visible<?php endif; ?>">
									<p class="form__error"> 
										<span class="form__error__required"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></span>
										<span class="form__error__secondary"><?php esc_html_e( 'Invalid user', 'community-portal' ); ?></span>
									</p>
								</div>
								<input type="hidden" name="group_admin_id" id="group-admin-id" value="<?php echo isset( $form['group_admin_id'] ) ? esc_attr( sanitize_textarea_field( wp_unslash( $form['group_admin_id'] ) ) ) : ''; ?>" required/>
							</div>
						</div>
					</section>
					<?php if ( 1 === $step ) : ?>
					<section class="create-group__details">

						<div class="create-group__terms">
							<p><?php echo esc_html_e('We use cookies to improve your experience on our site and to show you personalized advertising.', 'community-portal') ?></p>
							<p><?php echo esc_html_e('To find out more, read our', 'community-portal' ) ?> <a href="https://www.mozilla.org/privacy/"><?php echo esc_html_e('privacy policy', 'community-portal') ?> </a> <?php echo esc_html_e('and', 'community-portal') ?> <a href="https://www.mozilla.org/privacy/websites/#cookies"><?php echo esc_html_e('cookie policy', 'community-portal') ?></a>.
							</p>
						</div>
						<div class="create-group__input-container create-group__input-container--full cpg">
							<input class="checkbox--hidden" type="checkbox" name="agree" id="agree" value="<?php esc_attr_e( 'I Agree', 'community-portal' ); ?>" required />
							<label class="cpg__label" for="agree">
								<?php esc_html_e( 'I agree to respect and adhere to', 'community-portal' ); ?>
								<a class="create-group__checkbox-container__link" href="https://www.mozilla.org/about/governance/policies/participation/"><?php esc_html_e( 'Mozilla’s Community Participation Guidelines *', 'community-portal' ); ?></a>
								<div class="form__error-container form__error-container--checkbox">
									<p class="form__error"><?php esc_html_e( 'Please agree to the Community Participation Guidelines', 'community-portal' ); ?></p>
								</div>
							</label>
						</div>
						<input type="hidden" name="step" value="2" />
					</section>
					<?php endif; ?>
					<section class="create-group__cta-container">
						<input type="submit" class="create-group__cta" value="<?php esc_attr_e( 'Continue', 'community-portal' ); ?>" />
					</section>
				<?php endif; ?>      
			</div>
		</form>
	</div>
</div>
<?php
	do_action( 'bp_after_create_group_page' );
	get_footer();
?>
