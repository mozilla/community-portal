<?php
/**
 * Events Search
 *
 * Template for searching events for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<?php

	$args = ! empty( $args ) ? $args : array();
?>

<div class="em-search-wrapper row">
	<div class="em-events-search em-search col-lg-7 col-sm-12">
		<?php
			$args['search']     = preg_replace( '/^\"|\"$|^\'|\'$/', '', $args['search'] );
			$query_view         = isset( $_GET['view'] ) && strlen( sanitize_title_for_query( wp_unslash( $_GET['view'] ) ) ) > 0 ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '';
			$args['search_url'] = add_query_arg(array('view' => $query_view), get_home_url(null, 'events'));
			$query_page         = isset( $_GET['pno'] ) && strlen( sanitize_title_for_query( wp_unslash( $_GET['pno'] ) ) ) > 0 ? sanitize_text_field( wp_unslash( $_GET['pno'] ) ) : false;
			$query_country      = isset( $_GET['country'] ) && strlen( sanitize_text_field( wp_unslash( $_GET['country'] ) ) ) > 0 ? sanitize_text_field( wp_unslash( urldecode( $_GET['country'] ) ) ) : false;
			$query_language     = isset( $_GET['language'] ) && strlen( sanitize_title_for_query( wp_unslash( $_GET['language'] ) ) ) > 0 ? sanitize_text_field( wp_unslash( $_GET['language'] ) ) : false;
			$query_category     = isset( $_GET['tag'] ) && strlen( sanitize_title_for_query( wp_unslash( $_GET['tag'] ) ) ) > 0 ? sanitize_text_field( wp_unslash( $_GET['tag'] ) ) : false;
			$query_initiative   = isset( $_GET['initiative'] ) && strlen( sanitize_title_for_query( wp_unslash( $_GET['initiative'] ) ) ) > 0 ? sanitize_title_for_query( wp_unslash( $_GET['initiative'] ) ) : false;
		?>
		<form method="GET" action="
		<?php
		if ( $args['search_url'] ) {
			echo esc_url_raw( $args['search_url'] );
		} else {
			echo esc_attr( EM_URI ); }
		?>
			" class="events__form">
			<input type="hidden" name="action" value="<?php echo esc_attr( $args['search_action'] ); ?>" />
			<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'events-filter' ) ); ?>">
			<?php
			if ( $query_page ) {
				?>
				<input type="hidden" name="pno" value="1" />
				<?php
			}
			if ( $query_view ) {
				?>
				<input type="hidden" name="view" value="<?php echo esc_attr( $query_view ); ?>" />
				<?php
			}
			if ( $query_country ) {
				?>
				<input type="hidden" name="country" value="<?php echo esc_attr( rawurlencode( ucwords( $query_country ) ) ); ?>" />
				<?php
			}
			if ( $query_language ) {
				?>
				<input type="hidden" name="language" value="<?php echo esc_attr( $query_language ); ?>" />
				<?php
			}
			if ( $query_category ) {
				?>
				<input type="hidden" name="tag" value="<?php echo esc_attr( $query_category ); ?>" />
				<?php
			}
			if ( $query_initiative ) {
				?>
				<input type="hidden" name="initiative" value="<?php echo esc_attr( $query_initiative ); ?>" />
				<?php
			}
			?>
			<?php do_action( 'em_template_events_search_form_header' ); // hook in here to add extra fields, text etc. ?>
			<div class="events__input-container">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M17.5 17.5L13.875 13.875" stroke="#737373" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<input type="text" name="em_search" class="events__search-input" placeholder="<?php esc_html_e( 'Search events', 'community-portal' ); ?>" value="<?php echo esc_attr( $args['search'] ); ?>" />
			</div>
			<input type="submit" class="events__search-cta" value="<?php esc_html_e( 'Search', 'community-portal' ); ?>" />
		</form>
	</div>
	<?php
	if ( ! empty( $args['ajax'] ) ) :
		?>
		<div class='em-search-ajax'></div><?php endif; ?>
</div>

