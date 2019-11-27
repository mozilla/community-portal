<?php
/* WARNING!!! (2013-07-10) We intend to add a few more fields into this search form over the coming weeks/months. 
 * Overriding shouldn't hinder functionality at all but these new search options won't appear on your form! 
 */ 
/* 
 * By modifying this in your theme folder within plugins/events-manager/templates/events-search.php, you can change the way the search form will look.
 * To ensure compatability, it is recommended you maintain class, id and form name attributes, unless you now what you're doing. 
 * You also have an $args array available to you with search options passed on by your EM settings or shortcode
 */
$args = !empty($args) ? $args:array(); /* @var $args array */
?>
<div class="em-search-wrapper row">
<div class="em-events-search em-search col-lg-7 col-sm-12 css-search">
  <?php 
    $view = get_query_var( 'view', $default = '');
    $args['search_url'] = '/events/?view='.$view;
  ?>
	<form action="<?php echo !empty($args['search_url']) ? esc_url($args['search_url']) : EM_URI; ?>" method="post" class="em-events-search-form em-search-form">
		<input type="hidden" name="action" value="<?php echo esc_attr($args['search_action']); ?>" />
		<div class="em-search-main">
			<?php do_action('em_template_events_search_form_header'); //hook in here to add extra fields, text etc. ?>
			<?php 
			//search text
			if( !empty($args['search_term']) ) em_locate_template('templates/search/search.php',true,array('args'=>$args));
			?>
			<?php if( !empty($args['css']) ) : //show the button here if we're using the default styling, if you still want to use this and use custom CSS, then you have to override our rules ?>
			<button type="submit" class="em-search-submit loading">
				<?php //before you ask, this hack is necessary thanks to stupid IE7 ?>
        <!--[if IE 7]><span><![endif]-->
        Search
				<!-- <img src="<?php echo EM_DIR_URI; ?>includes/images/search-mag.png" alt="<?php esc_attr_e('Search', 'events-manager'); ?>" /> -->
				<!--[if IE 7]></span><![endif]-->
			</button>
			<?php endif; ?>
		</div>
	</form>
</div>
<?php if( !empty($args['ajax']) ): ?><div class='em-search-ajax'></div><?php endif; ?>
</div>