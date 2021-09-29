<?php
/**
 * Single Campaign
 *
 * Page for single campaign views
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

	get_header();
	global $post;

	$campaign_image = get_the_post_thumbnail_url( $post->ID );

	$campaign_status         = get_field( 'campaign_status' );
	$campaign_hero_cta       = get_field( 'hero_cta' );
	$campaign_hero_unsub_cta = get_field( 'hero_cta_unsub' );
	$campaign_hero_cta_link  = get_field( 'hero_cta_link' );

	$campaign_start_date = get_field( 'campaign_start_date' );
	$campaign_end_date   = get_field( 'campaign_end_date' );

	$campaign_content = get_field( 'campaign_content' );

	$tags = get_the_terms( $post, 'post_tag' );

	$mailchimp = get_post_meta( $post->ID, 'mailchimp-list-id', true );
	$logged_in = mozilla_is_logged_in();

	$user                  = wp_get_current_user();
	$members_participating = get_post_meta( $post->ID, 'members-participating', true );
	$sub                   = true;
	$current_translation   = mozilla_get_current_translation();

	$translated_status = array(
		'Active' => __( 'Active', 'community-portal' ),
		'Closed' => __( 'Closed', 'community-portal' ),
	);

	if ( $logged_in && is_array( $members_participating ) && in_array( $user->ID, $members_participating, true ) ) {
		$sub = false;
	}

	?>
	<div class="content">
		<div class="campaign">
			<div class="campaign__hero">
				<div class="campaign__hero-container">
					<div class="campaign__hero-image" style="background-image: url(<?php print esc_attr( $campaign_image ); ?>);">
					</div>
					<div class="campaign__hero-content-container">
						<span class="campaign__status"><?php print esc_html( $translated_status[ $campaign_status ] ); ?></span>
						<h1 class="campaign__hero-title"><?php print esc_html( $post->post_title ); ?></h1>
						<div class="campaign__date-container">
							<?php
								$date_format = 'en' === $current_translation ? 'F d' : 'd F';
							if ( ! $campaign_end_date ) {
								$date_format = 'en' === $current_translation ? 'F d, Y' : 'd F Y';

							}
								$formatted_start_date = mozilla_localize_date( $campaign_start_date, $date_format );
								print esc_html( $formatted_start_date );
							?>
							<?php
							if ( $campaign_end_date ) :
								$date_format        = 'en' === $current_translation ? 'F d, Y' : 'd F Y';
								$formatted_end_date = mozilla_localize_date( $campaign_end_date, $date_format );
								?>
							- <?php print esc_html( $formatted_end_date ); ?>
							<?php endif; ?>
						</div>
						<?php if ( ( $campaign_hero_cta && $mailchimp && isset( $mailchimp->id ) ) || is_preview() ) : ?>
							<?php if ( $logged_in ) : ?>
								<a href="<?php print ( $campaign_hero_cta_link ) ? esc_attr( $campaign_hero_cta_link ) : '#'; ?>" class="campaign__hero-cta
									<?php
									if ( $mailchimp && isset( $mailchimp->id ) && true === $sub ) :
										?>
										campaign__hero-cta--sub
										<?php
										else :
											?>
										campaign__hero-cta--unsub
									<?php endif; ?>"
									<?php
									if ( $mailchimp && isset( $mailchimp->id ) ) :
										?>
										<?php
										if ( $mailchimp ) :
											?>
											data-list="<?php print esc_attr( $mailchimp->id ); ?>"<?php endif; ?><?php endif; ?> data-unsub-copy="<?php print esc_attr( $campaign_hero_unsub_cta ); ?>" data-sub-copy="<?php print esc_attr( $campaign_hero_cta ); ?>" data-campaign="<?php print esc_attr( $post->ID ); ?>"
											data-nonce="<?php print esc_attr( wp_create_nonce( 'mailing-list' ) ); ?>"
											>
											<?php
											print $sub ? esc_attr( $campaign_hero_cta ) : esc_attr( $campaign_hero_unsub_cta );
											?>
										</a>
										<?php else : ?>
											<a href="<?php print ( $campaign_hero_cta_link ) ? esc_attr( $campaign_hero_cta_link ) : '#'; ?>" class="campaign__hero-cta campaign__hero-cta--no-account" data-list="<?php print esc_attr( $mailchimp->id ); ?>" data-campaign="<?php print esc_attr( $post->ID ); ?>">
												<?php print esc_html( $campaign_hero_cta ); ?>
											</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="campaign__intro">
				<div class="campaign__intro-card">
					<?php echo wp_kses( $post->post_content, wp_kses_allowed_html( 'post' ) ); ?>
			<?php if ( ! empty( $post->post_content ) ) : ?>
					<hr class="campaign__keyline" />
			<?php endif; ?>
					<div class="campaign__share-container">
			<?php if ( is_array( $tags ) && ! empty( $tags ) ) : ?>
						<div class="campaign__tag-container">
							<span><?php esc_html_e( 'Tags', 'community-portal' ); ?></span>
							<span class="campaign__tag"><?php print esc_html( $tags[0]->name ); ?></span>
						</div>
			<?php endif; ?>
						<a href="#" class="campaign__share-cta">
							<svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 9V15C1 15.3978 1.15804 15.7794 1.43934 16.0607C1.72064 16.342 2.10218 16.5 2.5 16.5H11.5C11.8978 16.5 12.2794 16.342 12.5607 16.0607C12.842 15.7794 13 15.3978 13 15V9M10 4.5L7 1.5M7 1.5L4 4.5M7 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php esc_html_e( 'Share Campaign', 'community-portal' ); ?>

						</a>
					</div>

				</div>
			</div>
			<div class="campaign__container">
				<?php if ( $campaign_content && is_array( $campaign_content ) ) : ?>
					<?php foreach ( $campaign_content as $block ) : ?>
						<?php
						$theme_dir = get_template_directory();
						switch ( $block['acf_fc_layout'] ) {
							case 'text_1up_block':
								include "{$theme_dir}/templates/blocks/text-1up-block.php";
								break;
							case 'text_2up_block':
								include "{$theme_dir}/templates/blocks/text-2up-block.php";
								break;
							case 'text_3up_block':
								include "{$theme_dir}/templates/blocks/text-3up-block.php";
								break;
							case 'text_image':
								include "{$theme_dir}/templates/blocks/text-image-block.php";
								break;
							case 'text_card':
								include "{$theme_dir}/templates/blocks/text-card-block.php";
								break;
							case 'events_block':
								include "{$theme_dir}/templates/blocks/events-block.php";
								break;
							case 'video_block':
								include "{$theme_dir}/templates/blocks/video-block.php";
								break;
							case 'imagery_block':
								include "{$theme_dir}/templates/blocks/imagery-block.php";
								break;
							case 'outro_cta_block':
								include "{$theme_dir}/templates/blocks/outro-cta-block.php";
								break;
						}
						?>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div id="campaign-share-lightbox" class="lightbox">
		<?php require locate_template( 'templates/share-modal.php', false, false ); ?>
	</div>
	<?php if ( ( $campaign_hero_cta && ! $logged_in && $mailchimp && isset( $mailchimp->id ) ) || is_preview() ) : ?>
		<div id="campaign-rsvp-lightbox" class="lightbox">
			<?php include locate_template( 'templates/campaign-rsvp.php', false, false ); ?>
		</div>
	<?php endif; ?>

<?php
	get_footer();
?>
