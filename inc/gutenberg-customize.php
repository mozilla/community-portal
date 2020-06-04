<?php
/**
 *
 * Gutenberg Blocks Customizations
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @since  1.0.0
 */

/**
 * Allow only paragraph, heading, lists
 *
 * @param array $allowed_blocks Gutenberg blocks.
 */
function mozilla_allowed_block_types( $allowed_blocks ) {
	return array(
		'core/paragraph',
		'core/heading',
		'core/list',
	);
}
