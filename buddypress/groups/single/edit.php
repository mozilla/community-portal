<?php
/**
 * Group edit form
 *
 * Group edit form
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

do_action( 'bp_before_edit_group_page' );
$group_id     = bp_get_current_group_id();
$group        = $bp->groups->current_group;
$group_meta   = groups_get_groupmeta( $group_id, 'meta' );
$group_admins = groups_get_group_admins( $group_id );

if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	$request_post = true;
	if ( ! empty( $_POST['update_group_nonce_field'] ) ) {
		$nonce = trim( sanitize_text_field( wp_unslash( $_POST['update_group_nonce_field'] ) ) );
		if ( wp_verify_nonce( $nonce, 'update_group_nonce_field' ) ) {
			$form = $_POST;
			if ( isset( $form['tags'] ) ) {
				$form['tags'] = array_filter( explode( ',', $form['tags'] ) );
			}
		}
	}
} else {
	$request_post = false;
	// Prepopulate.
	$form['group_name']            = $group->name;
	$form['group_desc']            = $group->description;
	$form['group_type']            = isset( $group_meta['group_type'] ) ? $group_meta['group_type'] : 'Online';
	$form['group_language']        = isset( $group_meta['group_language'] ) ? $group_meta['group_language'] : '0';
	$form['group_country']         = isset( $group_meta['group_country'] ) ? $group_meta['group_country'] : '0';
	$form['group_city']            = isset( $group_meta['group_city'] ) ? $group_meta['group_city'] : '';
	$form['image_url']             = isset( $group_meta['group_image_url'] ) ? $group_meta['group_image_url'] : '';
	$form['tags']                  = $group_meta['group_tags'];
	$form['group_address_type']    = isset( $group_meta['group_address_type'] ) ? $group_meta['group_address_type'] : 'Address';
	$form['group_address']         = isset( $group_meta['group_address'] ) ? $group_meta['group_address'] : '';
	$form['group_meeting_details'] = isset( $group_meta['group_meeting_details'] ) ? $group_meta['group_meeting_details'] : '';
	$form['group_discourse']       = isset( $group_meta['group_discourse'] ) ? $group_meta['group_discourse'] : '';
	$form['group_telegram']        = isset( $group_meta['group_telegram'] ) ? $group_meta['group_telegram'] : '';
	$form['group_facebook']        = isset( $group_meta['group_facebook'] ) ? $group_meta['group_facebook'] : '';
	$form['group_github']          = isset( $group_meta['group_github'] ) ? $group_meta['group_github'] : '';
	$form['group_twitter']         = isset( $group_meta['group_twitter'] ) ? $group_meta['group_twitter'] : '';
	$form['group_other']           = isset( $group_meta['group_other'] ) ? $group_meta['group_other'] : '';
	$form['group_matrix']          = isset( $group_meta['group_matrix'] ) ? $group_meta['group_matrix'] : '';

	if ( ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) || ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) {
		$form['image_url'] = preg_replace( '/^http:/i', 'https:', $form['image_url'] );
	} else {
		$form['image_url'] = $form['image_url'];
	}
}

$form_tags = isset( $form['tags'] ) ? array_unique( array_filter( $form['tags'], 'strlen' ) ) : array();
?>
<div class="content">
	<div class="create-group">
		<div class="create-group__hero">
			<div class="create-group__hero-container">
				<h1 class="create-group__title"><?php esc_html_e( 'Edit Group', 'community-portal' ); ?></h1>
			</div>
		</div>
		<form action="/groups/<?php echo esc_attr( $group->slug ); ?>/admin/edit-details/" method="post" id="create-group-form" class="standard-form create-group__form" enctype="multipart/form-data" novalidate>
		<div class="create-group__container">
			<ol class="create-group__menu">
				<li class="create-group__menu-item create-group__menu-item--disabled"><a href="#" class="create-group__menu-link"><?php esc_html_e( 'Basic Information', 'community-portal' ); ?></a></li>
			</ol>
			<div class="create-group__menu create-group__menu--mobile">
				<div class="create-group__select-container">
					<select id="create-group-mobile-nav" class="create-group__select" name="mobile_nav">
						<option value="1" selected><?php esc_html_e( 'Basic Information', 'community-portal' ); ?></option>
					</select>
				</div>
			</div>
			<?php do_action( 'bp_before_create_group_content_template' ); ?>
			<?php wp_nonce_field( 'update_group', 'update_group_nonce_field' ); ?>
			<?php wp_nonce_field( 'protect_content', 'my_nonce_field' ); ?>
			<section class="create-group__details">
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--60">
						<label class="create-group__label" for="group-name"><?php esc_html_e( 'What is your group\'s name? *', 'community-portal' ); ?></label>
						<input type="text" name="group_name" id="group-name" class="create-group__input
						<?php
						if ( $request_post && ! isset( $form['group_name'] ) || ( isset( $form['group_name'] ) && empty( trim( $form['group_name'] ) ) ) ) :
							?>
							create-group__input--error<?php endif; ?>" value="<?php print isset( $form['group_name'] ) ? esc_attr( $form['group_name'] ) : ''; ?>" required />
						<div class="form__error-container
						<?php
						if ( $request_post && ! isset( $form['group_name'] ) || ( isset( $form['group_name'] ) && empty( trim( $form['group_name'] ) ) ) ) :
							?>
							form__error-container--visible<?php endif; ?>">
							<div class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></div>
						</div>
					</div>
					<div class="create-group__input-container create-group__input-container--40 create-group__input-container--flex">
						<label class="create-group__label" for="group-type"><?php esc_html_e( 'Group Type', 'community-portal' ); ?></label>
						<div class="create-group__select-container">
							<select id="group-type" class="create-group__select" name="group_type" required>
								<option value="Online"
								<?php
								if ( isset( $form['group_type'] ) && 'Online' === $form['group_type'] || ( empty( $form['group_type'] ) ) ) :
									?>
									selected<?php endif; ?>><?php esc_html_e( 'Online', 'community-portal' ); ?></option>
								<option value="Offline"
								<?php
								if ( isset( $form['group_type'] ) && 'Offline' === $form['group_type'] ) :
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
														if ( isset( $form['group_language'] ) && $form['group_language'] === $code ) :
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
						<input type="text" name="group_city" id="group-city" class="create-group__input" placeholder="<?php esc_attr_e( 'City', 'community-portal' ); ?>" value="<?php print isset( $form['group_city'] ) ? esc_attr( $form['group_city'] ) : ''; ?>" maxlength="180" />
					</div>
				</div>
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
						<label class="create-group__label" for="group-desc"><?php esc_html_e( 'Description *', 'community-portal' ); ?></label>
						<textarea name="group_desc" id="group-desc" class="create-group__textarea
						<?php
						if ( $request_post && ! isset( $form['group_desc'] ) || ( isset( $form['group_desc'] ) && empty( trim( $form['group_desc'] ) ) ) ) :
							?>
							create-group__input--error<?php endif; ?>" required ><?php print isset( $form['group_desc'] ) ? esc_html( $form['group_desc'] ) : ''; ?></textarea>
						<div class="form__error-container
						<?php
						if ( ! isset( $form['group_desc'] ) || ( isset( $form['group_desc'] ) && empty( trim( $form['group_desc'] ) ) ) ) :
							?>
							form__error-container--visible<?php endif; ?>">
							<div class="form__error"><?php esc_html_e( 'This field is required', 'community-portal' ); ?></div>
						</div>
					</div>
					<div class="create-group__input-container create-group__input-container--40 create-group__input-container--vertical-spacing">
						<label class="create-group__label" for="group-desc"><?php esc_html_e( 'Group Photo', 'community-portal' ); ?></label>
						<div id="dropzone-photo-uploader" class="create-group__image-upload
						<?php
						if ( isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ) :
							?>
							create-group__image-upload--done<?php endif; ?>"
							<?php
							if ( isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ) :
								?>
							style="background-image: url('<?php print esc_url_raw( $form['image_url'] ); ?>')"<?php endif; ?>>
							<div class="dz-message" data-dz-message="">
								<div class="create-group__image-instructions">
									<div class="form__error-container">
										<div class="form__error form__error--image"></div>
									</div>
									<button type="button" id="dropzone-trigger" class="dropzone__image-instructions create-group__image-instructions <?php echo ( isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ? 'dropzone__image-instructions--hidden' : '' ); ?>">
										<?php esc_html_e( 'Click or drag a photo above', 'community-portal' ); ?>
										<span><?php esc_html_e( 'min dimensions 703px by 400px', 'community-portal' ); ?></span>
									</button>
								</div>
								<button type="button" class="dz-remove
								<?php
								if ( ! isset( $form['image_url'] ) || strlen( $form['image_url'] ) === 0 ) :
									?>
									dz-remove--hide<?php endif; ?>" data-dz-remove="" ><?php esc_html_e( 'Remove file', 'community-portal' ); ?></button>
							</div>
						</div>
						<input type="hidden" name="image_url" id="image-url" value="<?php print ( isset( $form['image_url'] ) && strlen( $form['image_url'] ) > 0 ) ? esc_url_raw( $form['image_url'] ) : ''; ?>" />
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
									<input class="create-group__checkbox" type="checkbox" id="<?php echo esc_attr( $loop_tag->slug ); ?>" data-value="<?php echo esc_attr( $loop_tag->slug ); ?>">
									<label class="create-group__tag
									<?php
									if ( in_array( $loop_tag->slug, $form_tags, true ) ) :
										?>
										create-group__tag--active<?php endif; ?>" for="<?php echo esc_attr( $loop_tag->slug ); ?>"><?php echo esc_html( $loop_tag->name ); ?></label>
								<?php endforeach; ?>
							</div>
							<input type="hidden" value="<?php print ( isset( $form['tags'] ) ) ? esc_attr( implode( ',', $form['tags'] ) ) : ''; ?>" name="tags" id="tags" /> 
						</fieldset>
					</div>
				</div>
			</section>
			<section class="create-group__details">
				<div class="create-group__section-title"><?php esc_html_e( 'Group Meetings', 'community-portal' ); ?></div>
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--40">    
						<label class="create-group__label" for="group-address-type" ><?php esc_html_e( 'Where do you meet?', 'community-portal' ); ?></label>
						<div class="create-group__select-container">
							<select class="create-group__select" name="group_address_type" id="group-address-type">
								<option value="Address" 
								<?php
								if ( isset( $form['group_address_type'] ) && 'Address' === $form['group_address_type'] ) :
									?>
									selected<?php endif; ?>><?php esc_html_e( 'Address', 'community-portal' ); ?></option>
								<option value="URL"; ?>"
								<?php
								if ( isset( $form['group_address_type'] ) && 'URL' === $form['group_address_type'] ) :
									?>
									selected<?php endif; ?>><?php esc_html_e( 'URL', 'community-portal' ); ?></option>
							</select>
						</div>
					</div>
					<div class="create-group__input-container create-group__input-container--60 create-group__input-container--vertical-spacing">
						<label class="create-group__label" for="group-address" ><?php esc_html_e( 'Address', 'community-portal' ); ?></label>
						<input type="text" name="group_address" id="group-address" class="create-group__input" value="<?php print isset( $form['group_address'] ) ? esc_attr( $form['group_address'] ) : ''; ?>" />
					</div>
				</div>
				<div class="create-group__input-container create-group__input-container--full">
					<label class="create-group__label" for="group-desc"><?php esc_html_e( 'Meeting details', 'community-portal' ); ?></label>
					<textarea name="group_meeting_details" id="group-meeting-details" class="create-group__textarea create-group__textarea--full create-group__textarea--short" ><?php print isset( $form['group_meeting_details'] ) ? esc_html( $form['group_meeting_details'] ) : ''; ?></textarea>
				</div>
			</section>
			<section class="create-group__details">
				<h2 class="create-group__section-title"><?php esc_html_e( 'Community Links', 'community-portal' ); ?></h2>
				<div class="create-group__input-row create-group__subsection">
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-discourse"><?php esc_html_e( 'Discourse', 'community-portal' ); ?></label>
						<input placeholder="https://" type="text" name="group_discourse" id="group-discourse" class="create-group__input create-group__input--inline" value="<?php print isset( $form['group_discourse'] ) ? esc_url_raw( $form['group_discourse'] ) : ''; ?>" />
					</div>
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label"  for="group-matrix"><?php esc_html_e( 'Matrix', 'community-portal' ); ?></label>
						<input type="text" placeholder="room-alias:domain" name="group_matrix" id="group-matrix" class="create-group__input create-group__input--inline"  value="<?php print isset( $form['group_matrix'] ) ? esc_attr( $form['group_matrix'] ) : ''; ?>"/>
						<div class="form__error-container form__error-container--checkbox">
							<div class="form__error"><?php esc_html_e( 'Please format as room-alias:domain', 'community-portal' ); ?></div>
						</div>
					</div>
				</div>
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-facebook"><?php esc_html_e( 'Facebook', 'community-portal' ); ?></label>
						<input placeholder="https://" type="text" name="group_facebook" id="group-facebook" class="create-group__input create-group__input--inline"  value="<?php print isset( $form['group_facebook'] ) ? esc_url_raw( $form['group_facebook'] ) : ''; ?>"/>
					</div>
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-twitter"><?php esc_html_e( 'Twitter', 'community-portal' ); ?></label>
						<input placeholder="https://" type="text" name="group_twitter" id="group-twitter" class="create-group__input create-group__input--inline"  value="<?php print isset( $form['group_twitter'] ) ? esc_url_raw( $form['group_twitter'] ) : ''; ?>"/>
					</div>
				</div>
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-telegram"><?php esc_html_e( 'Telegram', 'community-portal' ); ?></label>
						<input type="text" placeholder="https://" name="group_telegram" id="group-telegram" class="create-group__input create-group__input--inline"  value="<?php print isset( $form['group_telegram'] ) ? esc_url_raw( $form['group_telegram'] ) : ''; ?>"/>
					</div>
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label" for="group-github"><?php esc_html_e( 'GitHub', 'community-portal' ); ?></label>
						<input placeholder="https://" type="text" name="group_github" id="group-github" class="create-group__input create-group__input--inline"  value="<?php print isset( $form['group_github'] ) ? esc_url_raw( $form['group_github'] ) : ''; ?>"/>
					</div>
				</div>
				<div class="create-group__input-row">
					<div class="create-group__input-container create-group__input-container--vertical-spacing create-group__input-container--50">
						<label class="create-group__label"  for="group-other"><?php esc_html_e( 'Other', 'community-portal' ); ?></label>
						<input type="text" placeholder="https://" name="group_other" id="group-other" class="create-group__input create-group__input--inline"  value="<?php print isset( $form['group_other'] ) ? esc_attr( $form['group_other'] ) : ''; ?>"/>
					</div>
				</div>
			</section>
			<section class="create-group__cta-container">
				<input type="submit" class="create-group__cta" value="<?php esc_attr_e( 'Continue', 'community-portal' ); ?>" />
			</section>
		</form>
	</div>
</div>
