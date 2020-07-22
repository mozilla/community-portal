<?php
/**
 * Single Event Sidebar
 *
 * Sidebar for single events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<div class="col-lg-4 col-sm-12 events-single__sidebar">
	<div>
		<div class="card events-single__attributes">
			<div class="row">
			<?php if ( isset( $external_url ) && strlen( $external_url ) > 0 && filter_var( $external_url, FILTER_VALIDATE_URL ) ) : ?>
				<div class="col-lg-12 col-md-6 col-sm-12">
					<p class="events-single__label"><?php esc_html_e( 'Links', 'community-portal' ); ?></p>
					<p class="events-single__external-link-wrapper" title="<?php echo esc_html( $external_url ); ?>"><a href="<?php echo esc_url_raw( mozilla_verify_url( $external_url, false ) ); ?>" class="events-single__external-link"><?php echo esc_html( $external_url ); ?></a></p>
				</div>
				<?php
			endif;

			?>
			<?php if ( is_array( $categories ) ) : ?>
				<div class="col-lg-12 col-md-6 col-sm-12">
					<p class="events-single__label"><?php esc_html_e( 'Tags', 'community-portal' ); ?></p>
					<ul class="events-single__tags">
						<?php
						foreach ( $categories as $category ) :
							$current_translation = mozilla_get_current_translation();
							$term_object            = mozilla_get_translated_tag( $category );
							?>
							<li class="tag"><a class="events-single__tag-link" href="<?php print esc_attr( add_query_arg( array( 'tag' => $category->term_id ), get_home_url( null, 'events' ) ) ); ?>"><?php echo esc_html( $term_object->name ); ?></a></li>
							<?php break; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<?php if ( $initiative ) : ?>
				<?php
					$c = get_post( $initiative );
				if ( 'en' !== $current_translation ) {
					$translated_initiative = apply_filters( 'wpml_object_id', $c->ID, $c->post_type, true, $current_translation );
					$translated_title      = get_the_title( $translated_initiative );
					$c->post_title         = isset( $translated_title ) && strlen( $translated_title ) > 0 ? $translated_title : $c->post_title;
				}
				if ( ! empty( $c ) ) :
					?>
						<div class="col-lg-12 col-md-6 col-sm-12">
							<p class="events-single__label"><?php esc_html_e( 'Part of', 'community-portal' ); ?></p>
							<a 
								href="
								<?php
								if ( 'campaign' === $c->post_type ) :
									echo esc_attr( get_home_url( null, 'campaigns/' . $c->post_name ) );
									else :
										echo esc_attr( get_home_url( null, 'activities/' . $c->post_name ) );
									endif;
									?>
									" 
								class="events-single__externam-link events-single__externam-link--icon">
							<?php if ( 'campaign' === $c->post_type ) : ?>
							<svg width="27" height="28" viewBox="0 0 27 28" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M14.8491 7.33834L9.19223 8.75256C9.19223 8.75256 3.81822 9.88393 2.96969 10.7325C2.29087 11.4113 2.59257 12.0524 2.82827 12.2881C3.39396 12.8538 5.421 14.8808 6.3638 15.8236" stroke="#0060DF" stroke-width="2"/>
								<path d="M20.5059 12.9956L19.0917 18.6525C19.0917 18.6525 17.9603 24.0265 17.1118 24.875C16.433 25.5539 15.7918 25.2522 15.5561 25.0165C14.9905 24.4508 12.9634 22.4237 12.0206 21.4809" stroke="#0060DF" stroke-width="2"/>
								<path d="M7.36963 17.6465L5.30312 19.713L6.71733 21.1272L8.13155 22.5414L10.1981 20.4749" stroke="#0060DF" stroke-width="2" stroke-linejoin="round"/>
								<path d="M21.7236 11.7788L12.0205 21.4819L9.19211 18.6535L6.36368 15.825L16.0668 6.12197C18.3059 3.8828 21.6843 4.27564 22.6271 5.21845C23.5699 6.16126 23.9628 9.53966 21.7236 11.7788Z" stroke="#0060DF" stroke-width="2"/>
							</svg>
							<?php else : ?>
							<svg width="27" height="28" viewBox="0 0 27 28" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M21 10H17L14 19L8 1L5 10H1" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php endif; ?>
								<?php print esc_html( $c->post_title ); ?>
							</a>
						</div>
					<?php endif; ?>
			<?php endif; ?>
			<div class="events-single__share col-lg-12 col-md-6 col-sm-12 <?php echo ( ! isset( $campaign ) && ! isset( $external_url ) && ! is_array( $categories ) && ! strlen( $external_url ) > 0 ? esc_attr( 'only-share' ) : null ); ?>">
				<button id="open-events-share-lightbox" class="btn btn--light btn--share">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M3 9V15C3 15.3978 3.15804 15.7794 3.43934 16.0607C3.72064 16.342 4.10218 16.5 4.5 16.5H13.5C13.8978 16.5 14.2794 16.342 14.5607 16.0607C14.842 15.7794 15 15.3978 15 15V9M12 4.5L9 1.5M9 1.5L6 4.5M9 1.5V11.25" stroke="#0060DF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<?php esc_html_e( 'Share', 'community-portal' ); ?>
				</button>
			</div>
		</div>
	</div>
	<?php require locate_template( 'plugins/events-manager/templates/template-parts/event-single/event-host.php', false, false ); ?>
	<?php if ( $language ) : ?>
	<div class="card events-single__language">
		<div class="row">
			<div class="col-lg-12 col-md-6 col-sm-12">
				<p class="events-single__label"><?php esc_html_e( 'Preferred Language', 'community-portal' ); ?></p>
				<p>
				<a href="/events/?language=<?php print esc_attr( $event_meta[0]->language ); ?>" class="events-single__language-link"><?php echo esc_html( $language ); ?></a>
				</p>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
