<?php
/**
 * Share
 *
 * Share modal template
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php

if ( ! empty( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['HTTP_HOST'] ) ) {
	$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	$http_host   = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) );
	if ( ! empty( ICL_LANGUAGE_CODE ) && false !== stripos( $request_uri, ICL_LANGUAGE_CODE . '/' ) ) {
		$language_removed = explode( ICL_LANGUAGE_CODE . '/', $request_uri );
		if ( isset( $language_removed[1] ) && ! empty( $language_removed[1] ) ) {
			$request_uri = $language_removed[1];
		}
	}
	$url = get_site_url( null, $request_uri );
}

?>
<div class="lightbox__container lightbox__container--share">
	<button id="close-share-lightbox" class="btn btn--close">
		<svg width="20" height="20" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M25 1L1 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M1 1L25 25" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</button>
	<div class="share-lightbox">
		<p class="title--secondary"><?php esc_html_e( 'Share', 'community-portal' ); ?></p> 
		<div class="lightbox__flex-container">
		<ul class="share-link-container">
			<li class="share-link">
				<a href="#" id="copy-share-link" data-url="<?php echo esc_attr( $url ); ?>" class="btn btn--light btn--share share-link__copy">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15 7H18C18.6566 7 19.3068 7.12933 19.9134 7.3806C20.52 7.63188 21.0712 8.00017 21.5355 8.46447C21.9998 8.92876 22.3681 9.47996 22.6194 10.0866C22.8707 10.6932 23 11.3434 23 12C23 12.6566 22.8707 13.3068 22.6194 13.9134C22.3681 14.52 21.9998 15.0712 21.5355 15.5355C21.0712 15.9998 20.52 16.3681 19.9134 16.6194C19.3068 16.8707 18.6566 17 18 17H15M9 17H6C5.34339 17 4.69321 16.8707 4.08658 16.6194C3.47995 16.3681 2.92876 15.9998 2.46447 15.5355C1.52678 14.5979 1 13.3261 1 12C1 10.6739 1.52678 9.40215 2.46447 8.46447C3.40215 7.52678 4.67392 7 6 7H9" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M8 12H16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<span class="share-link__initial"><?php esc_html_e( 'Copy share link', 'community-portal' ); ?></span>
					<span class="share-link__completed"><?php esc_html_e( 'Link copied', 'community-portal' ); ?></span>
				</a>
			</li>
			<li class="share-link">
				<a href="<?php echo esc_url_raw( 'https://www.facebook.com/sharer/sharer.php?u=' . $url ); ?>" class="btn btn--light btn--share share-link__facebook">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 17.9895 4.38823 22.954 10.125 23.8542V15.4688H7.07812V12H10.125V9.35625C10.125 6.34875 11.9165 4.6875 14.6576 4.6875C15.9705 4.6875 17.3438 4.92188 17.3438 4.92188V7.875H15.8306C14.3399 7.875 13.875 8.80001 13.875 9.74899V12H17.2031L16.6711 15.4688H13.875V23.8542C19.6118 22.954 24 17.9895 24 12Z" fill="black"/>
					</svg>
					<?php esc_html_e( 'Share to Facebook', 'community-portal' ); ?>
				</a>
			</li>
			<li class="share-link">
				<a href="<?php echo esc_url_raw( 'https://twitter.com/intent/tweet?url=' . $url ); ?>" class="btn btn--light btn--share share-link__twitter">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M7.65187 21.5238C16.4963 21.5238 21.3337 14.1963 21.3337 7.84193C21.3337 7.6338 21.3337 7.42662 21.3197 7.22037C22.2608 6.53966 23.0731 5.69681 23.7188 4.7313C22.8411 5.12018 21.9102 5.3752 20.9569 5.48786C21.9607 4.88691 22.712 3.94171 23.0709 2.82818C22.127 3.38829 21.0944 3.78303 20.0175 3.99537C19.2925 3.22444 18.3336 2.71396 17.2893 2.54291C16.2449 2.37186 15.1733 2.54977 14.2402 3.04913C13.3071 3.54848 12.5647 4.34143 12.1277 5.30528C11.6907 6.26912 11.5835 7.35012 11.8228 8.38099C9.91102 8.28515 8.04075 7.78833 6.33341 6.92279C4.62608 6.05726 3.11985 4.84234 1.9125 3.35693C1.29759 4.41552 1.10925 5.66867 1.38584 6.86125C1.66243 8.05382 2.38315 9.09614 3.40125 9.77599C2.63601 9.75331 1.88745 9.54688 1.21875 9.17412C1.21875 9.1938 1.21875 9.21443 1.21875 9.23505C1.21905 10.3453 1.60337 11.4212 2.30651 12.2803C3.00966 13.1395 3.98834 13.729 5.07656 13.9488C4.36863 14.1419 3.62586 14.1701 2.90531 14.0313C3.21259 14.9868 3.81081 15.8223 4.61632 16.4211C5.42182 17.0198 6.39433 17.3518 7.39781 17.3707C5.69506 18.7089 3.59162 19.4354 1.42594 19.4332C1.04335 19.4324 0.661129 19.4093 0.28125 19.3638C2.48028 20.775 5.03898 21.5235 7.65187 21.5201" fill="black"/>
					</svg>
					<?php esc_html_e( 'Share to Twitter', 'community-portal' ); ?>
				</a>
			</li>
			<li class="share-link">
				<a href="<?php echo esc_url_raw( 'https://discourse.mozilla.org/new-topic?title=' . $url ); ?>" class="btn btn--light btn--share share-link__discourse">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7118 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0035 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 3.99656 12.8199 4 11.5C4.00061 9.92179 4.44061 8.37488 5.27072 7.03258C6.10083 5.69028 7.28825 4.6056 8.7 3.90003C9.87812 3.30496 11.1801 2.99659 12.5 3.00003H13C15.0843 3.11502 17.053 3.99479 18.5291 5.47089C20.0052 6.94699 20.885 8.91568 21 11V11.5Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>    
					<?php esc_html_e( 'Share to Discourse', 'community-portal' ); ?>
				</a>
			</li>
			<li class="share-link">
				<a href="<?php echo esc_url_raw( 'https://telegram.me/share/url?url=' . $url ); ?>" class="btn btn--light btn--share share-link__telegram" >
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">   
						<path d="M22 2L11 13" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<?php esc_html_e( 'Share to Telegram', 'community-portal' ); ?>
				</a>
			</li>
		</ul>
		</div>
		
	</div>
</div>
