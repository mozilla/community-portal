<?php
/**
 * Newsletter
 *
 * Individual page for users to sign up for the newsletter
 *
 * @package    WordPress
 * @subpackage community-portal
 * @version    1.0.0
 * @author     Playground Inc.
 */

?>

<?php
	get_header();
?>
	<div class="newsletter newsletter__page">
		<?php require get_template_directory() . '/templates/campaigns-newsletter.php'; ?>
	</div>
<?php
	get_footer();
?>
