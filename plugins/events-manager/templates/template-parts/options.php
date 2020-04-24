<?php
/**
 * Events Options
 *
 * Options for filters for events page for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>

<div class="events__filter__option">
	<label class="select__label" for="<?php echo esc_attr( $field_name ); ?>">
		<?php echo esc_html( $field_label ); ?>
	</label>
	<select class="select" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>" data-filter="<?php echo esc_attr( $field_name ); ?>">
		<option value="all"><?php esc_html_e( 'All', 'community-portal' ); ?></option>
		<?php foreach ( $options as $key  => $option ) : ?>
			<?php if ( 'Initiative' === $field_name ) : ?>
				<option value="<?php print esc_attr( $key ); ?>" 
					<?php
					if ( isset( $_GET['initiative'] ) && strlen( $_GET['initiative'] ) > 0 && intval( $_GET['initiative'] ) === $key ) :
						?>
					selected<?php endif; ?>><?php print esc_html( $option ); ?></option>
			<?php elseif ( 'Language' === $field_name ) : ?>
				<option value="<?php print esc_attr( $key ); ?>" 
					<?php
					if ( isset( $_GET['language'] ) && strlen( $_GET['language'] ) > 0 && strtolower( $_GET['language'] ) === strtolower( $key ) ) :
						?>
						selected<?php endif; ?>><?php print esc_html( $option ); ?></option>
			<?php else : ?>
				<?php if ( $option === $country || $option === $tag ) : ?>
				<option value="<?php echo esc_attr( $option ); ?>" selected><?php echo esc_html( $option ); ?></option>
			<?php else : ?>
				<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
			<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
</div>
