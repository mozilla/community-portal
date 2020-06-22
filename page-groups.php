<?php
/**
 * Groups
 *
 * Main page for all groups for theme
 *
 * @package WordPress
 * @subpackage community-portal
 * @version 1.0.0
 * @author  Playground Inc.
 */

?>
<?php get_header(); ?>
	<div class="content">
	<?php
		$template_dir = get_template_directory();
		include "{$template_dir}/buddypress/groups/index-directory.php"; 
	?>
	</div>
<?php get_footer(); ?>
